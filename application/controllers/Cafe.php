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

        $shared_referral = strtoupper(trim((string)($this->input->get('reff', TRUE) ?: $this->input->get('ref', TRUE))));
        if ($shared_referral !== '' && $this->db->where('reff', $shared_referral)->count_all_results('members') > 0) {
            $this->input->set_cookie(array(
                'name' => 'referral',
                'value' => $shared_referral,
                'expire' => 60 * 60 * 24 * 30,
                'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                'httponly' => TRUE,
                'samesite' => 'Lax',
            ));
        } else {
            $shared_referral = '';
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
            'shared_referral' => $shared_referral,
        ));
    }
}
