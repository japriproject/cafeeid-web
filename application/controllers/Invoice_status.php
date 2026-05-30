<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice_status extends CI_Controller
{
    public function index()
    {
        $invoice = trim((string)$this->input->get('inv', TRUE));
        $row = $invoice !== ''
            ? $this->db->select('invoice, price, status, status_update')->where('invoice', $invoice)->get('transaksi')->row()
            : null;

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'status' => $row ? (int)$row->status : 0,
                'found' => (bool)$row,
                'invoice' => $row ? $row->invoice : $invoice,
                'price' => $row ? (int)$row->price : 0,
                'status_update' => $row ? (int)$row->status_update : 0,
            )));
    }
}
