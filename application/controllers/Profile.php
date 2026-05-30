<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model');
    }

    public function index()
    {
        $reff = (string)$this->session->userdata('member_reff');
        if ($reff === '') {
            redirect(site_url('auth?redirect=' . rawurlencode(site_url('profile'))));
        }

        if ($this->input->method(TRUE) === 'POST') {
            $name = trim((string)$this->input->post('name', TRUE));
            $phone = trim((string)$this->input->post('phone', TRUE));

            if ($name !== '' && preg_match('/^[0-9+ ]{8,20}$/', $phone)) {
                $this->Auth_model->update_member_profile($reff, array(
                    'name' => $name,
                    'phone' => $phone,
                ));
                $this->session->set_flashdata('success', 'Profil berhasil disimpan.');
            } else {
                $this->session->set_flashdata('error', 'Nama dan nomor HP wajib diisi dengan format yang valid.');
            }

            redirect(site_url('profile'));
        }

        $this->load->view('profile', array(
            'member' => $this->Auth_model->get_member_by_reff($reff),
            'reff' => $reff,
        ));
    }
}
