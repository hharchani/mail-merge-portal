<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('auth_lib');
        if ( ! $this->auth_lib->is_valid_user() ) {
            $email = $this->auth_lib->user()->email;
            $ip = $this->input->ip_address();
            my_log("User `$email` tried to login from $ip");
            $error_msg = 'You are not allowed to view this page.<br/>
            Contact Administrator.<br/>
            You can logout from <a href="'.base_url('logout').'">here</a>';
            show_error($error_msg, 403, 'Permission Denied');
        }
    }

    public function index() {
        $this->load->view('partial/header', array(
            'email' => $this->auth_lib->user()->email
        ));
        $this->load->model('user');
        $this->load->view('users', array(
            'users' => $this->user->get()
        ));
        $this->load->view('partial/footer');
    }

    public function add() {
        $email = $this->input->post('email');
        if ($email) {
            $this->load->model('user');
            $this->user->add($email);
        }
        redirect( base_url('users') );
    }

    public function delete() {
        $email = $this->input->post('email');
        if ($email) {
            $this->load->model('user');
            $this->user->delete($email);
        }
        redirect( base_url('users') );
    }
}
