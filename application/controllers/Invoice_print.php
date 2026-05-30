<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice_print extends CI_Controller
{
    public function index($invoice = null)
    {
        $invoice = trim((string)($invoice ?: $this->input->get('inv', TRUE)));
        if ($invoice === '') {
            show_404();
        }

        $trx = $this->db
            ->select('transaksi.*, cafes.cafe_name, cafes.address, cafes.bank_bca_rek, cafes.bank_bca_an, cafes.bank_bri_rek, cafes.bank_bri_an, cafes.bank_mandiri_rek, cafes.bank_mandiri_an, cafes.qris_image, cafes.qris_name')
            ->from('transaksi')
            ->join('cafes', 'cafes.id_cafe = transaksi.id_cafe')
            ->where('transaksi.invoice', $invoice)
            ->get()
            ->row();

        if (!$trx) {
            show_404();
        }

        $subtotal = (int)$trx->sale;
        $ppn = (int)round(0.11 * $subtotal);
        $server_fee = (0.01 * $subtotal) >= 500 ? (int)round(0.01 * $subtotal) : 500;
        $kode_unik = max(0, (int)$trx->price - ($subtotal + $ppn + $server_fee));

        $cafe = (object)array(
            'id_cafe' => $trx->id_cafe,
            'cafe_name' => $trx->cafe_name,
            'address' => $trx->address,
            'bank_bca_rek' => $trx->bank_bca_rek,
            'bank_bca_an' => $trx->bank_bca_an,
            'bank_bri_rek' => $trx->bank_bri_rek,
            'bank_bri_an' => $trx->bank_bri_an,
            'bank_mandiri_rek' => $trx->bank_mandiri_rek,
            'bank_mandiri_an' => $trx->bank_mandiri_an,
            'qris_image' => $trx->qris_image,
            'qris_name' => $trx->qris_name,
        );

        $this->load->view('checkout_success', array(
            'invoice' => $trx->invoice,
            'cafe' => $cafe,
            'subtotal' => $subtotal,
            'ppn' => $ppn,
            'server_fee' => $server_fee,
            'kode_unik' => $kode_unik,
            'total_price' => (int)$trx->price,
            'status' => (int)$trx->status,
            'created_at' => $trx->created_at,
            'member_reff' => $trx->members,
            'items' => array(),
            'order_type' => $trx->order_type,
            'id_cafe' => (int)$trx->id_cafe,
            'desc_nota' => $trx->desc,
            'expired_at' => $trx->expired_at,
        ));
    }
}
