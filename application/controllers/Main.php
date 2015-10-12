<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

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
        redirect(base_url('main/submit'));
    }

    public function submit() {
        $this->load->view('partial/header', array(
            'email' => $this->auth_lib->user()->email
        ));
        $this->load->view('submit');
        $this->load->view('partial/footer');
    }

    public function status($task_id = null) {
        if ( ! $task_id ) {
            $this->load->library('session');
            if ($this->session->task_id) {
                redirect(base_url('main/status/' . $this->session->task_id));
            }
        }
        $this->load->view('partial/header',array(
            'email' => $this->auth_lib->user()->email
        ));
        $this->load->model('task');
        $task_details = $this->task->get_task_status($task_id);
        $this->load->view('status', array(
            'task_id' => $task_id,
            'task_details' => $task_details
        ));
        $this->load->view('partial/footer');
    }

    public function upload() {
        // Creating response to send
        $response = new stdClass();

        $current_time = date('Y-m-d H:i:s');

        $this->load->library('upload');

        $files_data = array(
            'marks' => null,
            'email' => null
        );

        // Validating files
        foreach($files_data as $file_name => $data) {
            $response->$file_name = new stdClass();
            if ( $_FILES AND isset($_FILES[ $file_name ]) AND $_FILES[ $file_name ]['name'] ) {
                $e  = str_replace('.', '-', $this->auth_lib->user()->email);
                $ip = str_replace('.', '-', $this->input->ip_address());
                $tmp_file_name = 'tmp_' . $file_name . '_file_by_' . $e . '_from_' . $ip . '_at_' . $current_time;
                $this->upload->initialize(array(
                    'file_name'     => $tmp_file_name,
                    'upload_path'   => $this->config->item('upload_dir'),
                    'allowed_types' => 'csv'
                ));
                $response->$file_name->success = $this->upload->do_upload( $file_name );
                $response->$file_name->errors  = $this->upload->display_errors('', '');
                $response->$file_name->data = $this->upload->data();
                $files_data[$file_name] = $this->upload->data();
            }
            else {
                $response->$file_name->success = false;
                $response->$file_name->errors  = "Please select $file_name file";
            }
        }
        // Validating input data
        $task_data = array('attendance_month' => null, 'exam_name' => null);
        foreach($task_data as $name => $data) {
            $response->$name = new stdClass();
            if ( ! $this->input->post($name) ) {
                $response->$name->success = false;
                $response->$name->errors = 'Please fill this field';
            }
            else {
                $response->$name->success = true;
            }
        }
        $form_validation_success = true;
        foreach(array_merge($task_data, $files_data) as $name => $data) {
            if ( ! $response->$name->success) {
                $form_validation_success = false;
                break;
            }
        }
        if ( ! $form_validation_success ) {
            echo json_encode($response);
            exit;
        }

        // Reading files to check the fields are proper.
        $this->load->library('excel_reader');

        // Marks file
        $marks = $this->excel_reader->read($files_data['marks']['full_path']);
        $marks_fields = $marks->get_fields();
        $marks_diff = array_diff(array(
            'roll_no',
            'course_code',
            'max-marks',
            'marks-obtained'
        ), $marks_fields);
        if (count($marks_diff) > 0) {
            $response->marks->success = false;
            $response->marks->errors = 'Reading marks file failed. Fields ' . implode(', ', $marks_diff) . ' not found.';
        }

        // Email file
        $emails = $this->excel_reader->read($files_data['email']['full_path']);
        $email_fields = $emails->get_fields();
        $email_diff = array_diff(array(
            'roll_no',
            'father_email_id',
        ), $email_fields);
        if (count($email_diff) > 0) {
            $response->email->success = false;
            $response->email->errors = 'Reading email file failed. Fields ' . implode(', ', $email_diff) . ' not found.';
        }

        if ( ! $response->marks->success OR ! $response->email->success) {
            echo json_encode($response);
            exit;
        }

        // Everything seems fine now. Creating task to start sending emails
        $this->load->model('task');

        $task = $this->task->create_task(
            $this->auth_lib->user()->email,
            $this->input->ip_address(),
            $current_time,
            $this->input->post('exam_name'),
            $this->input->post('attendance_month')
        );
        $task_id = $task->id;

        $this->load->library('session');
        $this->session->set_userdata('task_id', $task_id);

        $response->task_id = $task_id;

        // send response with task_id and start background processing
        $this->load->library('bg_processing');
        $this->bg_processing->send_and_close( json_encode($response) );

        $this->task->set_task_status($task_id, 'processing');
        // Renaming uploaded files
        foreach($files_data as $file_name => $data) {
            $new_name =   $file_name . '_file_for_task_' . $task_id . $data['file_ext'];
            $new_full_path = $data['file_path'] . $new_name;
            rename($data['full_path'], $new_full_path);
            $data['file_name'] = $new_name;
            $files_data[$file_name]['full_path'] = $new_full_path;
        }

        $this->load->model('student');
        $this->load->model('course');
        $months = array( 'jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec');
        $months_available = array();
        foreach($months as $month) {
            if ( in_array( $month . '-classes' , $marks_fields) ) {
                $months_available[] = $month;
            }
        }

        $CI = $this;
        $CI->task->insert_status_msg($task_id, "Info: Started sending emails");
        $marks->each( function( $a ) use ($task_id, $months_available, $CI) {
            if ( ! $a->roll_no) {
                return;
            }
            $student = $CI->student->get_or_create( $a->roll_no, $a->name );
            $course_id  = $CI->course->get_or_create( $a->course_code, $a->course_name, $a->credits );
            $classes_total = 0;
            $classes_missed = 0;
            foreach($months_available as $month) {
                $classes_total += $a->get($month.'-classes');
                $classes_missed += $a->get($month.'-absents');
            }
            $CI->course->insert_marks_info(array(
                'task_id'       => $task_id,
                'student_id'    => $student->id,
                'course_id'     => $course_id,
                'max_marks'     => $a->get('max-marks'),
                'marks_secured' => $a->get('marks-obtained'),
                'classes_total' => $classes_total,
                'classes_missed'=> $classes_missed,
                'position'      => $a->get('position')
            ));
        });

        $this->load->library('email_wrapper');
        $emails->each( function( $a ) use ($task, $CI) {
            $task_id = $task->id;
            $student = $CI->student->get_or_create( $a->roll_no, null, $a->father_email_id );
            $course_data = $CI->course->get_data($task_id, $student->id);
            if (count($course_data)) {
                $email_success = $CI->email_wrapper->send($student, $course_data, $task);
                if ($email_success) {
                    $CI->task->increase_sent_email($task_id);
                }
                else {
                    $CI->task->increase_failed_email($task_id);
                    $CI->task->insert_status_msg($task_id, "Warning: Email to $student->parent_email failed");
                }
            }
            else {
                $CI->task->insert_status_msg($task_id, "Info: No marks or attendance found for student with roll_no $student->roll_no");
            }
        });
        $CI->task->insert_status_msg($task_id, "Info: Completed sending emails");
        $this->task->set_task_status($task_id, 'completed');
    }

    public function get_task_status($task_id = null) {
        $response = new stdClass();

        if ( ! $task_id ) {
            $this->load->library('session');
            $task_id = $this->session->task_id;
        }

        if ( ! $task_id ) {
            $response->status = false;
            $response->message = 'No task exists';
        }
        else {
            $this->load->model('task');
            $status = $this->task->get_task_status($task_id);
            if ( $status === null ) {
                $response->status = false;
                $response->message = 'No task exists';
            }
            else {
                $response->status  = $status->status;
                $response->message = $this->task->get_task_status_msg($task_id);
            }
        }
        echo json_encode($response);
    }

    public function sample_email($exam = null, $month = null) {
        $this->load->library('email_wrapper');
        echo $this->email_wrapper->get_sample_email(urldecode($exam), urldecode($month));
    }
}
