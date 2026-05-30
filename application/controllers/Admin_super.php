<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_super extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin_model');

        if (!$this->session->userdata('super_admin')) {
            redirect(site_url('auth'));
        }
    }

    public function manage_cafe()
    {
        $message = null;

        if ($this->input->post('submit')) {
            $username = trim((string)$this->input->post('username', TRUE));
            $password = (string)$this->input->post('password', TRUE);
            $cafe_name = trim((string)$this->input->post('cafe_name', TRUE));
            $status_meja = (string)$this->input->post('status_meja', TRUE);
            $latitude = (float)$this->input->post('latitude');
            $longitude = (float)$this->input->post('longitude');

            if (!preg_match('/^[a-zA-Z0-9_.-]{3,50}$/', $username)) {
                $message = 'Username hanya boleh huruf, angka, titik, underscore, dan strip.';
            } elseif (strlen($password) < 8) {
                $message = 'Password minimal 8 karakter.';
            } elseif ($cafe_name === '') {
                $message = 'Nama kafe wajib diisi.';
            } elseif (!in_array($status_meja, array('buka', 'penuh'), TRUE)) {
                $message = 'Status meja tidak valid.';
            } elseif ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
                $message = 'Koordinat tidak valid.';
            } else {
            $data = array(
                'username' => $username,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'cafe_name' => $cafe_name,
                'address' => $this->input->post('address', TRUE),
                'kota' => $this->input->post('kota', TRUE),
                'latitude' => $latitude,
                'longitude' => $longitude,
                'prefix_invoice' => $this->input->post('prefix_invoice', TRUE),
                'status_meja' => $status_meja,
                'created_at' => date('Y-m-d H:i:s'),
            );

            $this->Admin_model->insert_cafe($data);
            $message = 'Kafe berhasil ditambahkan.';
            }
        }

        if ($delete_id = $this->input->post('delete_id')) {
            $this->Admin_model->delete_cafe((int)$delete_id);
            $message = 'Kafe berhasil dihapus.';
        }

        $cafes = $this->Admin_model->get_all_cafes();

        $this->load->view('admin_super', array(
            'cafes' => $cafes,
            'message' => $message,
        ));
    }
}
