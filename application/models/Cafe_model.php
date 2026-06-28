<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cafe_model extends CI_Model
{
    public function count_cafes($search = '', $city = '')
    {
        $this->db->from('cafes');
        if ($city) {
            $this->db->where('kota', $city);
        }
        if ($search) {
            $this->db->group_start()
                ->like('cafe_name', $search)
                ->or_like('kota', $search)
                ->group_end();
        }
        return $this->db->count_all_results();
    }

    public function get_cafes($limit = 10, $offset = 0, $search = '', $city = '')
    {
        $this->db->from('cafes');
        if ($city) {
            $this->db->where('kota', $city);
        }
        if ($search) {
            $this->db->group_start()
                ->like('cafe_name', $search)
                ->or_like('kota', $search)
                ->group_end();
        }
        $this->db->order_by('id_cafe', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
    }

    public function get_cities()
    {
        return $this->db
            ->select('kota')
            ->where('kota IS NOT NULL', NULL, FALSE)
            ->where('kota !=', '')
            ->group_by('kota')
            ->order_by('kota', 'ASC')
            ->get('cafes')
            ->result();
    }

    public function get_cafe($id_cafe)
    {
        return $this->db->get_where('cafes', array('id_cafe' => (int)$id_cafe))->row();
    }

    public function get_categories($id_cafe)
    {
        return $this->db->get('kategori_produk')->result();
    }

    public function get_menus($id_cafe, $id_kategori)
    {
        return $this->db->get_where('menus', array(
            'id_cafe' => (int)$id_cafe,
            'id_kategori' => (int)$id_kategori,
            'status' => 1
        ))->result();
    }

    public function get_setting_expiry_time()
    {
        $row = $this->db->select('expiry_time')->get('settings')->row();
        return $row ? (int)$row->expiry_time : 60;
    }

    public function get_available_tables($id_cafe)
    {
        if (!$this->db->table_exists('meja')) {
            return array();
        }
        return $this->db->where(array('id_cafe' => (int)$id_cafe, 'status' => 'tersedia'))->order_by('nomor_meja', 'ASC')->get('meja')->result();
    }
}
