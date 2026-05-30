<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cari extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Cafe_model');
    }

    public function index()
    {
        $q = trim((string)$this->input->get('q', TRUE));
        $loc = trim((string)$this->input->get('loc', TRUE));

        $this->load->view('cari', array(
            'cafes' => $this->Cafe_model->get_cafes(100, 0, $q, $loc),
            'cities' => $this->Cafe_model->get_cities(),
            'q' => $q,
            'loc' => $loc,
        ));
    }
}
