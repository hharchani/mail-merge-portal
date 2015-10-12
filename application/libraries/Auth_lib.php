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
            $this->user->email = $tmp_user['E-Mail'];
            my_log($this->user->email . " logged in");
        }
        return $this->user;
    }

    public function is_valid_user() {
        $this->ci->load->model('user');
        return $this->ci->user->exists($this->user()->email);
    }

    public function logout() {
        $this->ci->cas->logout();
    }
}
