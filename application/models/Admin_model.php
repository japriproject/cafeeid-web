<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model
{
    public function get_all_cafes()
    {
        return $this->db->order_by('id_cafe', 'DESC')->get('cafes')->result();
    }

    public function insert_cafe($data)
    {
        $this->db->insert('cafes', $data);
        return $this->db->insert_id();
    }

    public function delete_cafe($id_cafe)
    {
        return $this->db->where('id_cafe', (int)$id_cafe)->delete('cafes');
    }

    public function get_cafe_transactions($id_cafe)
    {
        return $this->db->where('id_cafe', (int)$id_cafe)
            ->order_by('id', 'DESC')
            ->get('transaksi')
            ->result();
    }

    public function get_cafe_products($id_cafe)
    {
        return $this->db
            ->select('menus.*, kategori_produk.nama_kategori')
            ->from('menus')
            ->join('kategori_produk', 'kategori_produk.id_kategori = menus.id_kategori', 'left')
            ->where('menus.id_cafe', (int)$id_cafe)
            ->order_by('menus.id_menu', 'DESC')
            ->get()
            ->result();
    }

    public function get_cafe_product($id_menu, $id_cafe)
    {
        return $this->db->get_where('menus', array(
            'id_menu' => (int)$id_menu,
            'id_cafe' => (int)$id_cafe,
        ))->row();
    }

    public function insert_cafe_product(array $data)
    {
        return $this->db->insert('menus', $data);
    }

    public function update_cafe_product($id_menu, $id_cafe, array $data)
    {
        return $this->db
            ->where('id_menu', (int)$id_menu)
            ->where('id_cafe', (int)$id_cafe)
            ->update('menus', $data);
    }

    public function delete_cafe_product($id_menu, $id_cafe)
    {
        return $this->db
            ->where('id_menu', (int)$id_menu)
            ->where('id_cafe', (int)$id_cafe)
            ->delete('menus');
    }
}
