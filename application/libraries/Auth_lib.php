<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_lib {
    private $ci;
    private $authenticated = false;
    private $user = null;

    //Force authentication as soon as library is loaded.
    public function __construct() {
        $this->ci = & get_instance();
        $this->ci->load->library('session');
        $this->ci->load->library('cas');
        if ($this->ci->cas->is_authenticated()) {
            $this->authenticated = true;
        }
        else {
            $this->ci->cas->force_auth();
        }
    }

    public function user() {
        if ($this->user === null) {
            $tmp_user = $this->ci->cas->user()->attributes;
            $this->user = new stdClass();
            $this->user->name  = $tmp_user['Name'];
            $this->user->email = $tmp_user['E-Mail'];
        }
        return $this->user;
    }

    public function is_valid_user() {
        $this->ci->load->database();
        return $this->ci->db->get_where('users', array(
            'email' => $this->user()->email
        ))->row() !== null;
    }

    public function logout() {
        $this->ci->cas->logout();
    }
}
