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
}
