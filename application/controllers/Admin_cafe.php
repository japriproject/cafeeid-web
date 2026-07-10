<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_cafe extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin_model');
        $this->load->model('Cafe_model');

        if (!$this->session->userdata('owner_cafe_id')) {
            redirect(site_url('auth'));
        }
    }

    public function index()
    {
        redirect(site_url('admin_cafe/dashboard'));
    }

    public function settlement()
    {
        redirect(site_url('admin_cafe/dashboard'));
    }

    public function dashboard()
    {
        $this->render_panel('dashboard');
    }

    public function konfirmasi_pesanan()
    {
        $id_cafe = (int)$this->session->userdata('owner_cafe_id');

        if (strtoupper($this->input->method()) === 'POST') {
            $this->confirm_pending_order($id_cafe);
            redirect(site_url('admin_cafe/konfirmasi_pesanan'));
        }

        $this->render_panel('orders');
    }

    public function kelola_meja()
    {
        $id_cafe = (int)$this->session->userdata('owner_cafe_id');
        $this->Admin_model->ensure_tables_table();

        if (strtoupper($this->input->method()) === 'POST') {
            $action = (string)$this->input->post('action', TRUE);
            $this->handle_table_action($id_cafe, $action);
            redirect(site_url('admin_cafe/kelola_meja'));
        }

        $this->render_panel('tables');
    }

    public function kelola_produk()
    {
        $id_cafe = (int)$this->session->userdata('owner_cafe_id');

        if (strtoupper($this->input->method()) === 'POST') {
            $action = (string)$this->input->post('action', TRUE);
            $this->handle_product_action($id_cafe, $action);
            redirect(site_url('admin_cafe/kelola_produk'));
        }

        $this->render_panel('products');
    }

    public function transaksi()
    {
        $this->render_panel('transactions');
    }

    public function setting()
    {
        $id_cafe = (int)$this->session->userdata('owner_cafe_id');

        if (strtoupper($this->input->method()) === 'POST') {
            $this->update_cafe_setting($id_cafe);
            redirect(site_url('admin_cafe/setting'));
        }

        $this->render_panel('settings');
    }

    private function render_panel($active_page)
    {
        $id_cafe = (int)$this->session->userdata('owner_cafe_id');
        $this->Admin_model->ensure_tables_table();
        $cafe = $this->Cafe_model->get_cafe($id_cafe);
        $transactions = $this->Admin_model->get_cafe_transactions($id_cafe);

        $total_earned = 0;
        foreach ($transactions as $transaction) {
            if ((int)$transaction->status === 1) {
                $total_earned += (int)$transaction->sale;
            }
        }

        $this->load->view('admin_cafe', array(
            'cafe' => $cafe,
            'transactions' => $transactions,
            'total_earned' => $total_earned,
            'categories' => $this->Cafe_model->get_categories($id_cafe),
            'products' => $this->Admin_model->get_cafe_products($id_cafe),
            'tables' => $this->Admin_model->get_cafe_tables($id_cafe),
            'pending_orders' => $this->Admin_model->get_pending_cafe_orders($id_cafe),
            'active_page' => $active_page,
            'dashboard_stats' => $this->build_dashboard_stats($transactions),
        ));
    }

    private function build_dashboard_stats($transactions)
    {
        $today = date('Y-m-d');
        $stats = array(
            'paid_orders' => 0,
            'pending_orders' => 0,
            'today_orders' => 0,
            'today_sales' => 0,
        );

        foreach ($transactions as $transaction) {
            $paid = (int)$transaction->status === 1;
            if ($paid) {
                $stats['paid_orders']++;
            } else {
                $stats['pending_orders']++;
            }

            if ((string)($transaction->date ?? '') === $today || strpos((string)$transaction->created_at, $today) === 0) {
                $stats['today_orders']++;
                if ($paid) {
                    $stats['today_sales'] += (int)$transaction->sale;
                }
            }
        }

        return $stats;
    }

    private function confirm_pending_order($id_cafe)
    {
        $action = (string)$this->input->post('action', TRUE);
        if ($action !== 'confirm_order') {
            return;
        }

        $invoice = trim((string)$this->input->post('invoice', TRUE));
        if ($invoice === '') {
            $this->session->set_flashdata('order_error', 'Invoice tidak valid.');
        } elseif ($this->Admin_model->confirm_cafe_order($id_cafe, $invoice)) {
            $this->session->set_flashdata('order_success', 'Pesanan berhasil dikonfirmasi lunas.');
        } else {
            $this->session->set_flashdata('order_error', 'Pesanan tidak ditemukan atau sudah dikonfirmasi.');
        }
    }

    private function update_cafe_setting($id_cafe)
    {
        $cafe_name = trim((string)$this->input->post('cafe_name', TRUE));
        $status_meja = (string)$this->input->post('status_meja', TRUE);
        $latitude = (float)$this->input->post('latitude');
        $longitude = (float)$this->input->post('longitude');
        $harga_reservasi = (int)preg_replace('/\D+/', '', (string)$this->input->post('harga_reservasi'));

        if ($cafe_name === '' || strlen($cafe_name) > 100) {
            $this->session->set_flashdata('setting_error', 'Nama cafe wajib diisi maksimal 100 karakter.');
            return;
        }
        if (!in_array($status_meja, array('buka', 'penuh'), TRUE)) {
            $this->session->set_flashdata('setting_error', 'Status meja tidak valid.');
            return;
        }
        if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
            $this->session->set_flashdata('setting_error', 'Koordinat tidak valid.');
            return;
        }

        $data = array(
            'cafe_name' => $cafe_name,
            'address' => trim((string)$this->input->post('address', TRUE)),
            'kota' => trim((string)$this->input->post('kota', TRUE)),
            'latitude' => $latitude,
            'longitude' => $longitude,
            'status_meja' => $status_meja,
            'harga_reservasi' => max(0, $harga_reservasi),
            'payment_info' => trim((string)$this->input->post('payment_info', TRUE)),
            'bank_bca_rek' => trim((string)$this->input->post('bank_bca_rek', TRUE)),
            'bank_bca_an' => trim((string)$this->input->post('bank_bca_an', TRUE)),
            'bank_bri_rek' => trim((string)$this->input->post('bank_bri_rek', TRUE)),
            'bank_bri_an' => trim((string)$this->input->post('bank_bri_an', TRUE)),
            'bank_mandiri_rek' => trim((string)$this->input->post('bank_mandiri_rek', TRUE)),
            'bank_mandiri_an' => trim((string)$this->input->post('bank_mandiri_an', TRUE)),
            'qris_name' => trim((string)$this->input->post('qris_name', TRUE)),
            'qris_image' => trim((string)$this->input->post('qris_image', TRUE)),
            'id_telegram_owner' => trim((string)$this->input->post('id_telegram_owner', TRUE)),
            'id_telegram_kasir' => trim((string)$this->input->post('id_telegram_kasir', TRUE)),
            'id_telegram_dapur' => trim((string)$this->input->post('id_telegram_dapur', TRUE)),
        );

        if ($this->Admin_model->update_cafe($id_cafe, $data)) {
            $this->session->set_flashdata('setting_success', 'Setting data cafe berhasil disimpan.');
        } else {
            $this->session->set_flashdata('setting_error', 'Setting data cafe gagal disimpan.');
        }
    }

    private function handle_product_action($id_cafe, $action)
    {
        $id_menu = (int)$this->input->post('id_menu');

        if (in_array($action, array('create_table', 'update_table', 'delete_table'), TRUE)) {
            $this->handle_table_action($id_cafe, $action);
            return;
        }

        if ($action === 'delete_product') {
            $product = $this->Admin_model->get_cafe_product($id_menu, $id_cafe);
            if (!$product) {
                $this->session->set_flashdata('product_error', 'Produk tidak ditemukan.');
                return;
            }
            if ($this->Admin_model->delete_cafe_product($id_menu, $id_cafe)) {
                $this->delete_product_image($product->image ?? '');
                $this->session->set_flashdata('product_success', 'Produk berhasil dihapus.');
            }
            return;
        }

        if (!in_array($action, array('create_product', 'update_product'), TRUE)) {
            return;
        }

        $product = $action === 'update_product'
            ? $this->Admin_model->get_cafe_product($id_menu, $id_cafe)
            : null;
        if ($action === 'update_product' && !$product) {
            $this->session->set_flashdata('product_error', 'Produk tidak ditemukan.');
            return;
        }

        $menu_name = trim((string)$this->input->post('menu_name', TRUE));
        $id_kategori = (int)$this->input->post('id_kategori');
        $price = (int)preg_replace('/\D+/', '', (string)$this->input->post('price'));
        $status = $this->input->post('status') === '0' ? 0 : 1;

        if ($menu_name === '' || strlen($menu_name) > 100) {
            $this->session->set_flashdata('product_error', 'Nama produk wajib diisi maksimal 100 karakter.');
            return;
        }
        if ($id_kategori <= 0 || $this->db->where('id_kategori', $id_kategori)->count_all_results('kategori_produk') === 0) {
            $this->session->set_flashdata('product_error', 'Kategori produk tidak valid.');
            return;
        }
        if ($price <= 0) {
            $this->session->set_flashdata('product_error', 'Harga produk harus lebih dari 0.');
            return;
        }

        $has_new_image = !empty($_FILES['product_image']['name']);
        if ($action === 'create_product' && !$has_new_image) {
            $this->session->set_flashdata('product_error', 'Foto produk wajib diunggah.');
            return;
        }

        $upload = $has_new_image ? $this->upload_product_image() : array('success' => TRUE, 'path' => null);
        if (!$upload['success']) {
            $this->session->set_flashdata('product_error', $upload['message']);
            return;
        }

        $data = array(
            'id_cafe' => $id_cafe,
            'id_kategori' => $id_kategori,
            'menu_name' => $menu_name,
            'price' => $price,
            'status' => $status,
        );
        if ($upload['path']) {
            $data['image'] = $upload['path'];
        }

        $saved = $action === 'create_product'
            ? $this->Admin_model->insert_cafe_product($data)
            : $this->Admin_model->update_cafe_product($id_menu, $id_cafe, $data);

        if (!$saved) {
            if ($upload['path']) {
                $this->delete_product_image($upload['path']);
            }
            $this->session->set_flashdata('product_error', 'Produk gagal disimpan.');
            return;
        }

        if ($action === 'update_product' && $upload['path'] && !empty($product->image)) {
            $this->delete_product_image($product->image);
        }
        $this->session->set_flashdata('product_success', $action === 'create_product' ? 'Produk berhasil ditambahkan.' : 'Produk berhasil diperbarui.');
    }

    private function upload_product_image()
    {
        if ((int)($_FILES['product_image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return array('success' => FALSE, 'message' => 'Upload foto produk gagal.');
        }
        if ((int)$_FILES['product_image']['size'] > 5 * 1024 * 1024) {
            return array('success' => FALSE, 'message' => 'Ukuran foto produk maksimal 5 MB.');
        }

        $types = array(IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp');
        $type = @exif_imagetype($_FILES['product_image']['tmp_name']);
        if (!isset($types[$type])) {
            return array('success' => FALSE, 'message' => 'Foto produk harus berupa JPG, PNG, atau WebP.');
        }

        $directory = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'products';
        if (!is_dir($directory) && !mkdir($directory, 0755, TRUE)) {
            return array('success' => FALSE, 'message' => 'Folder foto produk tidak dapat dibuat.');
        }
        $filename = 'product_' . date('YmdHis') . '_' . bin2hex(random_bytes(6)) . '.' . $types[$type];
        if (!move_uploaded_file($_FILES['product_image']['tmp_name'], $directory . DIRECTORY_SEPARATOR . $filename)) {
            return array('success' => FALSE, 'message' => 'Foto produk gagal disimpan.');
        }
        return array('success' => TRUE, 'path' => 'products/' . $filename);
    }

    private function handle_table_action($id_cafe, $action)
    {
        $id_meja = (int)$this->input->post('id_meja');
        $table = $id_meja > 0 ? $this->Admin_model->get_cafe_table($id_meja, $id_cafe) : null;
        if ($action === 'delete_table') {
            if ($table && $this->Admin_model->delete_cafe_table($id_meja, $id_cafe)) {
                $this->session->set_flashdata('table_success', 'Meja berhasil dihapus.');
            } else {
                $this->session->set_flashdata('table_error', 'Meja tidak ditemukan.');
            }
            return;
        }

        if ($action === 'update_table' && !$table) {
            $this->session->set_flashdata('table_error', 'Meja tidak ditemukan.');
            return;
        }
        $nomor = trim((string)$this->input->post('nomor_meja', TRUE));
        $kapasitas = (int)$this->input->post('kapasitas');
        $status = (string)$this->input->post('table_status', TRUE);
        if (!preg_match('/^[0-9]{1,4}$/', $nomor) || $kapasitas < 1 || $kapasitas > 100 || !in_array($status, array('tersedia', 'terisi', 'nonaktif'), TRUE)) {
            $this->session->set_flashdata('table_error', 'Data meja tidak valid.');
            return;
        }
        $duplicate = $this->db->where('id_cafe', $id_cafe)->where('nomor_meja', $nomor);
        if ($id_meja > 0) {
            $duplicate->where('id_meja !=', $id_meja);
        }
        if ($duplicate->count_all_results('meja') > 0) {
            $this->session->set_flashdata('table_error', 'Nomor meja sudah digunakan.');
            return;
        }
        $data = array('id_cafe' => $id_cafe, 'nomor_meja' => $nomor, 'kapasitas' => $kapasitas, 'status' => $status);
        $saved = $action === 'create_table'
            ? $this->Admin_model->insert_cafe_table($data)
            : $this->Admin_model->update_cafe_table($id_meja, $id_cafe, $data);
        $this->session->set_flashdata($saved ? 'table_success' : 'table_error', $saved ? 'Data meja berhasil disimpan.' : 'Data meja gagal disimpan.');
    }

    private function delete_product_image($path)
    {
        $path = str_replace('\\', '/', (string)$path);
        if (strpos($path, 'products/') !== 0 || strpos($path, '..') !== FALSE) {
            return;
        }
        $file = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
        if (is_file($file)) {
            @unlink($file);
        }
    }
}
