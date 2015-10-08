<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Student extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_or_create($roll_no, $name=null, $parent_email=null) {
        $result = $this->db->get_where('student', ['roll_no' => $roll_no]);
        $row = $result->row();
        if ($row === null) {
            $insert_data = [
                'roll_no' => $roll_no,
            ];
            if ($name) {
                $insert_data['name'] = ucwords(strtolower(trim($name)));
            }
            if ($parent_email) {
                $insert_data['parent_email'] = trim($parent_email);
            }
            $this->db->insert('student', $insert_data);
            $insert_data['id'] = $this->db->insert_id();
            return (object) $insert_data;
        }
        else {
            $update_data = [];
            if ($name) {
                $update_data['name'] = ucwords(strtolower(trim($name)));
                $row->name = $update_data['name'];
            }
            if ($parent_email) {
                $update_data['parent_email'] = trim($parent_email);
                $row->parent_email = $update_data['parent_email'];
            }
            if ( count($update_data) ) {
                $this->db->where('id', $row->id);
                $this->db->update('student', $update_data);
            }
            return $row;
        }
    }
}
