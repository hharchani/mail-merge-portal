<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function add($email) {
        $data = array(
            'email' => $email
        );
        $q = $this->db->get_where('users', $data);
        if ($q->row() !== null) {
            return false;
        }
        $this->db->insert('users', $data);
        return true;
    }

    public function get() {
        return $this->db->get('users')->result();
    }

    public function delete($email) {
        $this->db->where('email', $email);
        $this->db->delete('users');
    }

}
