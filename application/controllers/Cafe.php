<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cafe extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Cafe_model');
    }

    public function detail($id_cafe)
    {
        $id_cafe = (int)$id_cafe;
        $cafe = $this->Cafe_model->get_cafe($id_cafe);

        if (!$cafe) {
            show_404();
        }

        $categories = $this->Cafe_model->get_categories($id_cafe);
        $menus = array();
        foreach ($categories as $category) {
            $menus[$category->id_kategori] = array(
                'category' => $category,
                'items' => $this->Cafe_model->get_menus($id_cafe, $category->id_kategori)
            );
        }

        $member_reff = $this->session->userdata('member_reff');
        $logged_in = !empty($member_reff);
        $member_name = $this->db->select('name')->where('reff', $member_reff)->get('members')->row('name');

        $this->load->view('cafe_detail', array(
            'cafe' => $cafe,
            'menu_groups' => $menus,
            'member_reff' => $member_reff,
            'member_name' => $member_name,
            'logged_in' => $logged_in,
        ));
    }
}
