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

    public function get_cafe_for_admin($id_cafe)
    {
        return $this->db->get_where('cafes', array('id_cafe' => (int)$id_cafe))->row();
    }

    public function update_cafe($id_cafe, array $data)
    {
        return $this->db->where('id_cafe', (int)$id_cafe)->update('cafes', $data);
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

    public function get_pending_cafe_orders($id_cafe)
    {
        return $this->db
            ->where('id_cafe', (int)$id_cafe)
            ->where('status', 0)
            ->order_by('created_at', 'DESC')
            ->get('transaksi')
            ->result();
    }

    public function confirm_cafe_order($id_cafe, $invoice)
    {
        $this->db
            ->where('id_cafe', (int)$id_cafe)
            ->where('invoice', (string)$invoice)
            ->where('status', 0)
            ->update('transaksi', array(
                'status' => 1,
                'status_update' => 1,
            ));

        return $this->db->affected_rows() > 0;
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

    public function ensure_tables_table()
    {
        return $this->db->query("CREATE TABLE IF NOT EXISTS `meja` (
            `id_meja` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_cafe` INT NOT NULL,
            `nomor_meja` VARCHAR(30) NOT NULL,
            `kapasitas` INT NOT NULL DEFAULT 2,
            `status` ENUM('tersedia','terisi','nonaktif') NOT NULL DEFAULT 'tersedia',
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id_meja`),
            UNIQUE KEY `uniq_cafe_nomor` (`id_cafe`, `nomor_meja`),
            KEY `idx_meja_cafe` (`id_cafe`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
    }

    public function get_cafe_tables($id_cafe)
    {
        return $this->db->where('id_cafe', (int)$id_cafe)->order_by('nomor_meja', 'ASC')->get('meja')->result();
    }

    public function get_cafe_table($id_meja, $id_cafe)
    {
        return $this->db->get_where('meja', array('id_meja' => (int)$id_meja, 'id_cafe' => (int)$id_cafe))->row();
    }

    public function insert_cafe_table(array $data)
    {
        return $this->db->insert('meja', $data);
    }

    public function update_cafe_table($id_meja, $id_cafe, array $data)
    {
        return $this->db->where('id_meja', (int)$id_meja)->where('id_cafe', (int)$id_cafe)->update('meja', $data);
    }

    public function delete_cafe_table($id_meja, $id_cafe)
    {
        return $this->db->where('id_meja', (int)$id_meja)->where('id_cafe', (int)$id_cafe)->delete('meja');
    }
}
