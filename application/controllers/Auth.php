<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model');
    }

    public function index()
    {
        $redirect = $this->input->get('redirect', TRUE);
        if (!$redirect || !$this->is_safe_redirect($redirect)) {
            $redirect = site_url('home');
        }
        $this->load->view('login', array('redirect' => $redirect));
    }

    public function register()
    {
        $redirect = $this->input->get('redirect', TRUE);
        if (!$redirect || !$this->is_safe_redirect($redirect)) {
            $redirect = site_url('home');
        }

        $referral = strtoupper(trim((string)($this->input->get('reff', TRUE) ?: $this->input->get('ref', TRUE))));
        if ($referral !== '' && $this->Auth_model->get_member_by_reff($referral)) {
            $this->input->set_cookie(array(
                'name' => 'referral',
                'value' => $referral,
                'expire' => 60 * 60 * 24 * 30,
                'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                'httponly' => TRUE,
                'samesite' => 'Lax',
            ));
        } elseif ($referral === '') {
            $cached_referral = strtoupper(trim((string)$this->input->cookie('referral', TRUE)));
            if ($cached_referral !== '' && $this->Auth_model->get_member_by_reff($cached_referral)) {
                $referral = $cached_referral;
            }
        }

        if (strtoupper($this->input->method()) === 'POST') {
            $name = trim((string)$this->input->post('name', TRUE));
            $phone = preg_replace('/\s+/', '', trim((string)$this->input->post('phone', TRUE)));
            $password = (string)$this->input->post('password', TRUE);
            $password_confirm = (string)$this->input->post('password_confirm', TRUE);
            $referral = strtoupper(trim((string)$this->input->post('referral', TRUE)));

            if ($name === '' || strlen($name) > 100) {
                $this->session->set_flashdata('error', 'Nama wajib diisi maksimal 100 karakter.');
            } elseif (!preg_match('/^[0-9+]{8,20}$/', $phone)) {
                $this->session->set_flashdata('error', 'Nomor HP tidak valid.');
            } elseif ($this->Auth_model->get_member_by_phone($phone)) {
                $this->session->set_flashdata('error', 'Nomor HP sudah terdaftar. Silakan login.');
            } elseif (strlen($password) < 8) {
                $this->session->set_flashdata('error', 'Password minimal 8 karakter.');
            } elseif ($password !== $password_confirm) {
                $this->session->set_flashdata('error', 'Konfirmasi password tidak sama.');
            } elseif ($referral !== '' && !$this->Auth_model->get_member_by_reff($referral)) {
                $this->session->set_flashdata('error', 'Kode referral tidak ditemukan.');
            } else {
                $reff = $this->Auth_model->generate_member_reff();
                $this->Auth_model->create_member(array(
                    'reff' => $reff,
                    'upline' => $referral,
                    'name' => $name,
                    'phone' => $phone,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'saldo' => 0,
                    'status' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                ));

                $this->session->sess_regenerate(TRUE);
                $this->session->set_userdata('member_reff', $reff);
                redirect($redirect);
            }

            redirect(site_url('auth/register?redirect=' . rawurlencode($redirect) . ($referral ? '&reff=' . rawurlencode($referral) : '')));
        }

        $this->load->view('register', array(
            'redirect' => $redirect,
            'referral' => $referral,
        ));
    }

    public function login()
    {
        $redirect = $this->input->get('redirect', TRUE);
        if (!$redirect || !$this->is_safe_redirect($redirect)) {
            $redirect = site_url('home');
        }

        if (strtoupper($this->input->method()) !== 'POST') {
            $this->load->view('login', array('redirect' => $redirect));
            return;
        }

        $username = $this->input->post('username', TRUE);
        $password = $this->input->post('password', TRUE);

        if ($this->is_rate_limited($username)) {
            $this->session->set_flashdata('error', 'Terlalu banyak percobaan login. Coba lagi beberapa menit.');
            redirect(site_url('auth/index?redirect=' . rawurlencode($redirect)));
        }

        $member = $this->Auth_model->check_member($username, $password);
        if ($member) {
            $this->clear_login_attempts($username);
            $this->session->sess_regenerate(TRUE);
            $this->session->set_userdata('member_reff', $member->reff);
            redirect($redirect);
        }

        $owner = $this->Auth_model->check_owner($username, $password);
        if ($owner) {
            $this->clear_login_attempts($username);
            $this->session->sess_regenerate(TRUE);
            $this->session->set_userdata('owner_cafe_id', (int)$owner->id_cafe);
            redirect(site_url('admin_cafe/settlement'));
        }

        if ($this->is_super_admin($username, $password)) {
            $this->clear_login_attempts($username);
            $this->session->sess_regenerate(TRUE);
            $this->session->set_userdata('super_admin', 1);
            redirect(site_url('admin_super/manage_cafe'));
        }

        $this->record_failed_login($username);
        $this->session->set_flashdata('error', 'Identitas nomor HP/username atau kata sandi salah!');
        redirect(site_url('auth/index?redirect=' . rawurlencode($redirect)));
    }

    public function logout()
    {
        if (strtoupper($this->input->method()) !== 'POST') {
            redirect(site_url('home'));
        }

        $this->session->sess_destroy();
        redirect(site_url('home'));
    }

    private function is_safe_redirect($url)
    {
        $parts = parse_url($url);
        if (!$parts) {
            return FALSE;
        }

        if (!empty($parts['scheme']) && !in_array(strtolower($parts['scheme']), array('http', 'https'), TRUE)) {
            return FALSE;
        }

        if (empty($parts['host'])) {
            return isset($url[0]) && $url[0] === '/';
        }

        return isset($_SERVER['HTTP_HOST']) && strtolower($parts['host']) === strtolower($_SERVER['HTTP_HOST']);
    }

    private function is_rate_limited($username = '')
    {
        $locked_until = (int)$this->session->userdata('login_locked_until');
        if ($locked_until > time()) {
            return TRUE;
        }

        $record = $this->get_login_attempt_record($username);
        return !empty($record['locked_until']) && (int)$record['locked_until'] > time();
    }

    private function record_failed_login($username = '')
    {
        $attempts = (int)$this->session->userdata('login_attempts') + 1;
        $this->session->set_userdata('login_attempts', $attempts);

        if ($attempts >= 5) {
            $this->session->set_userdata('login_locked_until', time() + 300);
        }

        $record = $this->get_login_attempt_record($username);
        $record['attempts'] = (int)($record['attempts'] ?? 0) + 1;
        $record['last_attempt'] = time();

        if ($record['attempts'] >= 5) {
            $record['locked_until'] = time() + 300;
        }

        $this->write_login_attempt_record($username, $record);
    }

    private function clear_login_attempts($username = '')
    {
        $this->session->unset_userdata(array('login_attempts', 'login_locked_until'));
        $file = $this->get_login_attempt_file($username);
        if (is_file($file)) {
            @unlink($file);
        }
    }

    private function is_super_admin($username, $password)
    {
        $super_user = $this->config->item('cafeeid_super_user') ?: 'admin';
        $super_hash = $this->config->item('cafeeid_super_pass_hash');
        $super_pass = $this->config->item('cafeeid_super_pass') ?: 'admin';

        if (!hash_equals($super_user, (string)$username)) {
            return FALSE;
        }

        if ($super_hash) {
            return password_verify($password, $super_hash);
        }

        return hash_equals($super_pass, (string)$password);
    }

    private function get_login_attempt_key($username)
    {
        $ip = $this->input->ip_address();
        return hash('sha256', $ip . '|' . strtolower((string)$username));
    }

    private function get_login_attempt_file($username)
    {
        return APPPATH . 'cache/login_' . $this->get_login_attempt_key($username) . '.json';
    }

    private function get_login_attempt_record($username)
    {
        $file = $this->get_login_attempt_file($username);
        if (!is_file($file)) {
            return array('attempts' => 0, 'locked_until' => 0);
        }

        $data = json_decode((string)file_get_contents($file), TRUE);
        if (!is_array($data)) {
            return array('attempts' => 0, 'locked_until' => 0);
        }

        if (!empty($data['last_attempt']) && (int)$data['last_attempt'] < time() - 900) {
            return array('attempts' => 0, 'locked_until' => 0);
        }

        return $data;
    }

    private function write_login_attempt_record($username, array $record)
    {
        file_put_contents($this->get_login_attempt_file($username), json_encode($record), LOCK_EX);
    }
}
