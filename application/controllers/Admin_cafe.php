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
        ));
    }
}
