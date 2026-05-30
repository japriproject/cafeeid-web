<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Callback extends CI_Controller
{
    public function index()
    {
        $this->output->set_content_type('application/json');

        if (!$this->is_authorized()) {
            return $this->json_response(0, 'Callback key tidak valid.', 403);
        }

        $prefix = strtoupper(trim((string)$this->input->get('prefix', TRUE)));
        $this->write_log('Callback masuk. Prefix: ' . ($prefix !== '' ? $prefix : 'KOSONG'));

        if ($prefix === '') {
            return $this->json_response(0, 'Parameter query ?prefix=KODE_KAFE wajib disertakan.', 400);
        }

        $cafe = $this->db
            ->where('UPPER(prefix_invoice) = ' . $this->db->escape($prefix), null, false)
            ->limit(1)
            ->get('cafes')
            ->row();

        if (!$cafe) {
            $this->write_log('Kafe tidak ditemukan untuk prefix: ' . $prefix);
            return $this->json_response(0, "Kafe dengan prefix '{$prefix}' tidak terdaftar.", 404);
        }

        $raw = file_get_contents('php://input');
        $message = $this->read_message($raw);
        $this->write_log('Method: ' . $this->input->method(TRUE) . ', Content-Type: ' . (string)$this->input->get_request_header('Content-Type', TRUE));
        $this->write_log('Payload mentah: ' . $raw);
        $this->write_log('Message terbaca: ' . ($message !== '' ? $message : 'KOSONG'));

        if ($message === '') {
            return $this->json_response(0, 'Payload mutasi kosong.', 400);
        }

        $nominal = $this->extract_nominal($message);
        $this->write_log('Nominal terbaca: ' . $nominal);

        if ($nominal <= 0) {
            return $this->json_response(0, 'Gagal mendeteksi nominal angka transfer.', 400);
        }

        $invoice_hint = trim((string)$this->input->get('invoice', TRUE));
        if ($invoice_hint !== '') {
            $this->write_log('Invoice hint: ' . $invoice_hint);
        }

        $this->db->trans_start();

        $this->db
            ->where('price', $nominal)
            ->where('id_cafe', (int)$cafe->id_cafe)
            ->where('status', 0);

        if ($invoice_hint !== '') {
            $this->db->where('invoice', $invoice_hint);
        }

        $trx = $this->db
            ->order_by('created_at', 'ASC')
            ->limit(1)
            ->get('transaksi')
            ->row();

        if (!$trx) {
            $this->db->trans_complete();
            $this->write_log('Transaksi pending tidak ditemukan. Cafe: ' . $cafe->id_cafe . ', nominal: ' . $nominal);
            $this->write_pending_candidates((int)$cafe->id_cafe);
            return $this->json_response(0, 'Invoice pending nominal ' . format_rupiah($nominal) . ' tidak ditemukan.', 404, array(
                'nominal' => $nominal,
                'id_cafe' => (int)$cafe->id_cafe,
            ));
        }

        $this->db
            ->where('invoice', $trx->invoice)
            ->where('status', 0)
            ->update('transaksi', array(
                'status' => 1,
                'status_update' => 1,
            ));

        $this->create_affiliate_bonus($trx, $cafe);

        $this->db->trans_complete();

        if (!$this->db->trans_status()) {
            $this->write_log('Gagal update transaksi invoice: ' . $trx->invoice);
            return $this->json_response(0, 'Gagal memperbarui status lunas.', 500);
        }

        $this->send_paid_notifications($trx, $cafe, $nominal);
        $this->write_log('Callback sukses. Invoice: ' . $trx->invoice);

        return $this->json_response(1, 'Status terupdate paid dan komisi referral selesai diproses.', 200, array(
            'invoice' => $trx->invoice,
            'nominal' => $nominal,
        ));
    }

    private function is_authorized()
    {
        $secret = trim((string)getenv('CAFEEID_CALLBACK_SECRET'));
        if ($secret === '') {
            return true;
        }

        $provided = (string)$this->input->get('key', TRUE);
        if ($provided === '') {
            $provided = (string)$this->input->get_request_header('X-Callback-Key', TRUE);
        }

        return hash_equals($secret, $provided);
    }

    private function read_message($raw)
    {
        $payload = json_decode($raw, true);
        if (is_array($payload)) {
            foreach (array('message', 'text', 'body', 'notification_text', 'content') as $key) {
                if (!empty($payload[$key])) {
                    return (string)$payload[$key];
                }
            }
        }

        foreach (array('message', 'text', 'body', 'notification_text', 'content') as $key) {
            $value = $this->input->post($key, TRUE);
            if ($value !== null && $value !== '') {
                return (string)$value;
            }

            $value = $this->input->get($key, TRUE);
            if ($value !== null && $value !== '') {
                return (string)$value;
            }
        }

        $raw = trim((string)$raw);
        if ($raw !== '' && strpos($raw, '=') !== false) {
            parse_str($raw, $form);
            foreach (array('message', 'text', 'body', 'notification_text', 'content') as $key) {
                if (!empty($form[$key])) {
                    return (string)$form[$key];
                }
            }
        }

        return $raw;
    }

    private function extract_nominal($message)
    {
        if (preg_match('/(?:Rp\.?|IDR)\s*([\d\.,]+)/i', $message, $matches)) {
            return (int)str_replace(array('.', ','), '', $matches[1]);
        }

        if (preg_match('/(?:masuk|transfer)\s+([\d\.,]+)/i', $message, $matches)) {
            return (int)str_replace(array('.', ','), '', $matches[1]);
        }

        return 0;
    }

    private function create_affiliate_bonus($trx, $cafe)
    {
        if (empty($trx->members)) {
            return;
        }

        $member = $this->db->select('upline')->where('reff', $trx->members)->limit(1)->get('members')->row();
        if (!$member || empty($member->upline)) {
            return;
        }

        $is_reservation = ((string)$trx->order_type === 'reservation');
        $bonus_l1 = $is_reservation ? 1000 : 100;
        $bonus_l2 = $is_reservation ? 500 : 50;

        $this->insert_bonus_transaction(
            'BONUS1-' . $trx->invoice,
            (int)$cafe->id_cafe,
            $member->upline,
            'Bonus Affiliate Lvl 1',
            $bonus_l1,
            'Bonus Lvl 1 dari belanja downline ' . $trx->members
        );

        $upline_l2 = $this->db->select('upline')->where('reff', $member->upline)->limit(1)->get('members')->row();
        if ($upline_l2 && !empty($upline_l2->upline)) {
            $this->insert_bonus_transaction(
                'BONUS2-' . $trx->invoice,
                (int)$cafe->id_cafe,
                $upline_l2->upline,
                'Bonus Affiliate Lvl 2',
                $bonus_l2,
                'Bonus Lvl 2 dari cucu downline ' . $trx->members
            );
        }
    }

    private function insert_bonus_transaction($invoice, $id_cafe, $member_reff, $product, $amount, $description)
    {
        $exists = $this->db->where('invoice', $invoice)->count_all_results('transaksi') > 0;
        if ($exists) {
            return;
        }

        $this->db->insert('transaksi', array(
            'invoice' => $invoice,
            'id_cafe' => $id_cafe,
            'members' => $member_reff,
            'product' => $product,
            'sale' => 0,
            'price' => $amount,
            'admin' => 0,
            'status' => 1,
            'status_update' => 0,
            'type' => 2,
            'desc' => $description,
            'created_at' => date('Y-m-d H:i:s'),
            'date' => date('Y-m-d'),
        ));
    }

    private function send_paid_notifications($trx, $cafe, $nominal)
    {
        $chat_ids = array_filter(array(
            isset($cafe->id_telegram_owner) ? $cafe->id_telegram_owner : '',
            isset($cafe->id_telegram_kasir) ? $cafe->id_telegram_kasir : '',
            isset($cafe->id_telegram_dapur) ? $cafe->id_telegram_dapur : '',
        ));

        if (empty($chat_ids)) {
            return;
        }

        $invoice_url = site_url('invoice/' . rawurlencode($trx->invoice));
        $message = "<b>PEMBAYARAN LUNAS (AUTOMATIC)</b>\n";
        $message .= "=============================\n";
        $message .= "<b>Kafe:</b> " . html_escape($cafe->cafe_name) . "\n";
        $message .= "<b>Invoice:</b> <code>" . html_escape($trx->invoice) . "</code>\n";
        $message .= "=============================\n";
        $message .= "<b>Rincian Pesanan:</b>\n" . html_escape($trx->desc) . "\n";
        $message .= "<b>Total Bayar:</b> " . format_rupiah($nominal) . "\n";
        $message .= "=============================\n\n";
        $message .= "<b>Tautan Struk Pembeli:</b>\n" . $invoice_url;

        foreach ($chat_ids as $chat_id) {
            send_telegram_notification($chat_id, $message);
        }
    }

    private function write_pending_candidates($id_cafe)
    {
        $rows = $this->db
            ->select('invoice, price, status, created_at')
            ->where('id_cafe', $id_cafe)
            ->where('status', 0)
            ->order_by('created_at', 'DESC')
            ->limit(5)
            ->get('transaksi')
            ->result();

        if (empty($rows)) {
            $this->write_log('Tidak ada transaksi pending untuk cafe: ' . $id_cafe);
            return;
        }

        foreach ($rows as $row) {
            $this->write_log('Pending kandidat: ' . $row->invoice . ' / ' . $row->price . ' / ' . $row->created_at);
        }
    }

    private function json_response($status, $message, $http_code = 200, $extra = array())
    {
        $this->output->set_status_header($http_code);
        $payload = array_merge(array(
            'status' => (int)$status,
            'message' => $message,
        ), $extra);

        $this->output->set_output(json_encode($payload));
    }

    private function write_log($message)
    {
        $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . "\n";
        @file_put_contents(APPPATH . 'logs/callback-' . date('Y-m-d') . '.log', $line, FILE_APPEND);
    }
}
