<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model
{
    public function check_member($phone, $password)
    {
        $member = $this->db->get_where('members', array('phone' => $phone))->row();
        if (!$member) {
            return null;
        }

        if (password_verify($password, $member->password)) {
            return $member;
        }

        if (hash_equals((string)$member->password, md5($password))) {
            $this->db->where('reff', $member->reff)->update('members', array(
                'password' => password_hash($password, PASSWORD_DEFAULT),
            ));
            return $member;
        }

        return null;
    }

    public function check_owner($username, $password)
    {
        $owner = $this->db->get_where('cafes', array('username' => $username))->row();
        if (!$owner) {
            return null;
        }

        if (password_verify($password, $owner->password)) {
            return $owner;
        }

        if (hash_equals((string)$owner->password, (string)$password)) {
            $this->db->where('id_cafe', (int)$owner->id_cafe)->update('cafes', array(
                'password' => password_hash($password, PASSWORD_DEFAULT),
            ));
            return $owner;
        }

        return null;
    }

    public function get_member_by_reff($reff)
    {
        return $this->db->get_where('members', array('reff' => $reff))->row();
    }

    public function get_member_by_phone($phone)
    {
        return $this->db->get_where('members', array('phone' => $phone))->row();
    }

    public function create_member($data)
    {
        $this->db->insert('members', $data);
        return $this->db->insert_id();
    }

    public function generate_member_reff()
    {
        do {
            $reff = 'CF' . date('ymd') . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
            $exists = $this->db->where('reff', $reff)->count_all_results('members') > 0;
        } while ($exists);

        return $reff;
    }

    public function update_member_profile($reff, $data)
    {
        return $this->db->where('reff', $reff)->update('members', $data);
    }
}
