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
        $message_type = 'success';

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
                $thumbnail = $this->upload_thumbnail();
                if (!$thumbnail['success']) {
                    $message = $thumbnail['message'];
                    $message_type = 'error';
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
                        'image_2' => base_url('uploads/cafe_thumbnails/' . $thumbnail['file_name']),
                        'created_at' => date('Y-m-d H:i:s'),
                    );

                    if ($this->Admin_model->insert_cafe($data)) {
                        $message = 'Kafe berhasil ditambahkan.';
                    } else {
                        @unlink($thumbnail['full_path']);
                        $message = 'Kafe gagal disimpan. Pastikan username belum digunakan.';
                        $message_type = 'error';
                    }
                }
            }

            if ($message && $message !== 'Kafe berhasil ditambahkan.') {
                $message_type = 'error';
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
            'message_type' => $message_type,
        ));
    }

    private function upload_thumbnail()
    {
        if (empty($_FILES['thumbnail']['name']) || empty($_FILES['thumbnail']['tmp_name'])) {
            return array('success' => FALSE, 'message' => 'Thumbnail kafe wajib diunggah.');
        }

        if ((int)$_FILES['thumbnail']['error'] !== UPLOAD_ERR_OK) {
            return array('success' => FALSE, 'message' => 'Upload thumbnail gagal. Silakan coba lagi.');
        }

        if ((int)$_FILES['thumbnail']['size'] > 5 * 1024 * 1024) {
            return array('success' => FALSE, 'message' => 'Ukuran thumbnail maksimal 5 MB.');
        }

        $image_types = array(
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG => 'png',
            IMAGETYPE_WEBP => 'webp',
        );
        $image_type = @exif_imagetype($_FILES['thumbnail']['tmp_name']);
        if (!isset($image_types[$image_type])) {
            return array('success' => FALSE, 'message' => 'Thumbnail harus berupa JPG, PNG, atau WebP.');
        }

        $upload_dir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'cafe_thumbnails';
        if (!is_dir($upload_dir) && !mkdir($upload_dir, 0755, TRUE)) {
            return array('success' => FALSE, 'message' => 'Folder upload thumbnail tidak dapat dibuat.');
        }

        $file_name = 'cafe_' . date('YmdHis') . '_' . bin2hex(random_bytes(6)) . '.' . $image_types[$image_type];
        $full_path = $upload_dir . DIRECTORY_SEPARATOR . $file_name;
        if (!move_uploaded_file($_FILES['thumbnail']['tmp_name'], $full_path)) {
            return array('success' => FALSE, 'message' => 'Thumbnail gagal disimpan ke server.');
        }

        return array('success' => TRUE, 'file_name' => $file_name, 'full_path' => $full_path);
    }
}
