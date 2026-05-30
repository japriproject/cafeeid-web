<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Cafe_model');
    }

    public function index()
    {
        $search = trim((string)$this->input->get('search', TRUE));
        $city = trim((string)$this->input->get('city', TRUE));
        $page = max(1, (int)$this->input->get('page', TRUE));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $total = $this->Cafe_model->count_cafes($search, $city);
        $cafes = $this->Cafe_model->get_cafes($limit, $offset, $search, $city);
        $total_pages = max(1, ceil($total / $limit));

        $this->load->view('home', array(
            'cafes' => $cafes,
            'cities' => $this->Cafe_model->get_cities(),
            'search' => $search,
            'city' => $city,
            'page' => $page,
            'total_pages' => $total_pages,
            'total' => $total,
            'limit' => $limit,
        ));
    }
}
