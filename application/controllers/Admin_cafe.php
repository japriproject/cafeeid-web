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

    public function settlement()
    {
        $id_cafe = (int)$this->session->userdata('owner_cafe_id');
        $this->Admin_model->ensure_tables_table();

        if (strtoupper($this->input->method()) === 'POST') {
            $this->handle_product_action($id_cafe);
            redirect(site_url('admin_cafe/settlement#kelola-produk'));
        }

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
        ));
    }

    private function handle_product_action($id_cafe)
    {
        $action = (string)$this->input->post('action', TRUE);
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
