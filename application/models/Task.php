<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Task extends CI_Model {
    private $task_id = null;

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function create_task($email, $ip, $time_created, $exam_name, $attendance_month) {
        $task_data = [
            'created_by'        => $email,
            'ip'                => $ip,
            'time_created'      => $time_created,
            'exam_name'         => $exam_name,
            'attendance_month'  => $attendance_month
        ];
        $this->db->insert('task', $task_data);
        $task_data['id'] = $this->db->insert_id();
        return (object) $task_data;
    }

    public function increase_sent_email($task_id) {
        $this->db->query("UPDATE task SET emails_sent   = emails_sent   + 1 WHERE id = $task_id");
    }

    public function increase_failed_email($task_id) {
        $this->db->query("UPDATE task SET emails_failed = emails_failed + 1 WHERE id = $task_id");
    }

    public function insert_status_msg($task_id, $msg) {
        $this->db->insert('task_status', [
            'task_id' => $task_id,
            'msg' => $msg
        ]);
    }

    public function set_task_status($task_id, $status) {
        $this->db->where('id', $task_id);
        $this->db->update('task',[
            'status' => $status
        ]);
    }

    public function get_task_status($task_id) {
        $result = $this->db->get_where('task', ['id' => $task_id]);
        return $result->row();
    }

    public function get_task_status_msg($task_id) {
        $this->db->select('msg');
        $query = $this->db->get_where('task_status', ['task_id' => $task_id]);
        $result = $query->result();
        $val = [];
        if ($result) {
            foreach($result as $row) {
                $val[] = $row->msg;
            }
        }
        return $val;
    }
}
