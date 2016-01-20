<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Course extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_or_create($course_code, $course_name=null, $credits=null) {
        $this->db->select('id');
        $r = $this->db->get_where('course', array('code' => $course_code));
        $row = $r->row();
        if ($row === null) {
            $insert_data = array(
                'code' => $course_code
            );
            if ($course_name) {
                $insert_data['name'] = ucwords(strtolower(trim($course_name)));
            }
            if ($credits !== null) {
                $insert_data['credits'] = $credits;
            }
            $this->db->insert('course', $insert_data);
            return $this->db->insert_id();
        }
        else {
            $update_data = array();
            if ($course_name) {
                $update_data['name'] = ucwords(strtolower(trim($course_name)));
            }
            if ($credits) {
                $update_data['credits'] = $credits;
            }
            if ( count($update_data) ) {
                $this->db->where('id', $row->id);
                $this->db->update('course', $update_data);
            }
            return intval($row->id);
        }
    }

    public function get_data($task_id, $student_id) {
        $q = $this->db->query(
            "SELECT `max_marks`,
                    `marks_secured`,
                    `classes_total`,
                    `classes_missed`,
                    `grade`,
                    `name` as `course_name`,
                    `code` as `course_code`,
                    `credits` as `course_credits`
            FROM marks_attendance_info, course
            WHERE marks_attendance_info.course_id = course.id AND
                task_id = $task_id AND
                student_id = $student_id"
        );
        return $q->result();
    }

    public function insert_marks_info($details) {
        $this->db->insert('marks_attendance_info', $details);
    }

}
