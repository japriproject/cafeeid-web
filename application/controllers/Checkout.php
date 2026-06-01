<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Checkout extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Cafe_model');
        $this->load->model('Admin_model');
    }

    public function index()
    {
        $id_cafe = (int)$this->input->post('id_cafe');
        $redirect_target = $id_cafe > 0 ? site_url('cafe/detail/' . $id_cafe) : site_url('home');
        $cafe = $this->Cafe_model->get_cafe($id_cafe);

        if (!$cafe) {
            redirect(site_url('home'));
        }

        $session_member = (string)$this->session->userdata('member_reff');
        $reff_member = $session_member !== '' ? $session_member : (string)$this->input->post('reff_member', TRUE);

        if ($reff_member === '') {
            redirect(site_url('auth?redirect=' . rawurlencode($redirect_target)));
        }

        $order_type = $this->input->post('order_type', TRUE);
        $allowed_order_types = array('dine_in', 'reservation', 'take_away');
        if (!in_array($order_type, $allowed_order_types, TRUE)) {
            $order_type = 'dine_in';
        }

        $cart = $this->input->post('cart_items', TRUE);
        $cart_data = is_string($cart) ? json_decode($cart, TRUE) : array();
        if (!is_array($cart_data)) {
            $cart_data = array();
        }

        if (empty($cart_data) || $id_cafe <= 0) {
            redirect($redirect_target);
        }

        $subtotal = 0;
        $items = array();
        $verified_cart = array();
        $rincian_item = '';
        foreach ($cart_data as $item) {
            $id_menu = (int)($item['id'] ?? 0);
            $menu = $id_menu > 0 ? $this->db->get_where('menus', array(
                'id_menu' => $id_menu,
                'id_cafe' => $id_cafe,
                'status' => 1,
            ))->row() : null;

            if (!$menu) {
                continue;
            }

            $qty = min(99, max(1, (int)($item['qty'] ?? 1)));
            $price = (int)$menu->price;
            $subtotal += $price * $qty;
            $items[] = $qty . 'x ' . $menu->menu_name;
            $rincian_item .= '- ' . $menu->menu_name . ' x ' . $qty . ' (' . format_rupiah($price * $qty) . ')' . "\n";
            $verified_cart[] = array(
                'id' => (int)$menu->id_menu,
                'name' => $menu->menu_name,
                'price' => $price,
                'qty' => $qty,
            );
        }

        if ($subtotal <= 0 || empty($items)) {
            redirect($redirect_target);
        }

        $expiry_minutes = $this->Cafe_model->get_setting_expiry_time();
        $ppn = (int)round(0.11 * $subtotal);
        $one_percent_fee = 0.01 * $subtotal;
        $server_fee = $one_percent_fee >= 500 ? (int)round($one_percent_fee) : 500;
        $kode_unik = $one_percent_fee >= 500 ? rand(1, 99) : rand(1, 299);
        $total_price = $subtotal + $ppn + $server_fee + $kode_unik;
        $admin_fee = $total_price - $subtotal;

        $prefix = !empty($cafe->prefix_invoice) ? trim($cafe->prefix_invoice) : 'INV';
        $invoice = $prefix . '-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid('', TRUE)), 0, 4));

        $nomor_meja = (int)$this->input->post('nomor_meja');
        $kursi = max(1, (int)$this->input->post('kursi'));
        $durasi = max(1, (int)$this->input->post('durasi'));
        $desc_nota = 'Tipe Order: ' . strtoupper($order_type) . "\n";
        if ($order_type !== 'take_away' && $nomor_meja > 0) {
            $desc_nota .= 'Nomor Meja: ' . $nomor_meja . " \n";
            $desc_nota .= 'Jumlah Kursi: ' . $kursi . "\n";
            $desc_nota .= 'Durasi Sewa: ' . $durasi . " Jam\n";
        }
        $desc_nota .= "Detail Item:\n" . $rincian_item;

        $data = array(
            'invoice' => $invoice,
            'id_cafe' => $id_cafe,
            'members' => $reff_member,
            'product' => implode(', ', $items),
            'sale' => $subtotal,
            'price' => $total_price,
            'admin' => $admin_fee,
            'status' => 0,
            'status_update' => 0,
            'type' => 1,
            'order_type' => $order_type,
            'desc' => $desc_nota,
            'created_at' => date('Y-m-d H:i:s'),
            'expired_at' => date('Y-m-d H:i:s', time() + ($expiry_minutes * 60)),
            'date' => date('Y-m-d')
        );

        $this->db->insert('transaksi', $data);

        redirect(site_url('invoice/' . rawurlencode($invoice)));
    }
}
