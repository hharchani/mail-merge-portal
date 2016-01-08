<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email_wrapper {
    private $subj = 'Marks and Attendance Details [IIIT Hyderabad]';

    public function __construct() {
        $this->ci = & get_instance();
        $config = array();
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'mail.iiit.ac.in';
        $config['smtp_port'] = 25;
        $config['crlf'] = "\r\n";
        $config['newline'] = "\r\n";
        $config['mailtype'] = 'html';
        $this->ci->load->library('email', $config);
    }

    public function send($student_data, $course_data, $task_data) {
        $this->ci->email->from('ipis_noreply@iiit.ac.in', 'IIIT H Parents portal');
        $this->ci->email->bcc('harshit.harchani@students.iiit.ac.in');
        $this->ci->email->subject($this->subj);
        $this->ci->email->to($student_data->parent_email);
        $this->ci->email->message($this->get_msg($student_data, $course_data, $task_data));
        return $this->ci->email->send();
    }

    public function get_sample_email($exam, $month) {
        if ( ! $exam) {
            $exam = 'Exam';
        }
        if ( ! $month) {
            $month = 'Month Name';
        }

        $s = new stdClass();
        $s->name = 'Harshit Harchani';
        $c = array();
        $d = new stdClass();
        $d->course_name     = 'Compilers';
        $d->course_code     = 'CSE419';
        $d->course_credits  = 4;
        $d->classes_total   = 6;
        $d->classes_missed  = 1;
        $d->max_marks       = 25;
        $d->marks_secured   = 20;
        $d->position        = 'Top-1/3';
        $c[] = $d;

        $d = new stdClass();
        $d->course_name     = 'Research in Information Security';
        $d->course_code     = 'CSE540';
        $d->course_credits  = 4;
        $d->classes_total   = 6;
        $d->classes_missed  = 0;
        $d->max_marks       = 100;
        $d->marks_secured   = 55;
        $d->position        = 'Middle-1/3';
        $c[] = $d;

        $t = new stdClass();
        $t->attendance_month = $month;
        $t->exam_name = $exam;

        $op = '<script>
                function getDocHeight() {
                    return document.documentElement.offsetHeight;
                }
            </script>';
        return $this->get_msg($s, $c, $t, $op);
    }

    private function get_msg($student_data, $course_data, $task_data, $op="") {
        $msg = "<!DOCTYPE html>
                <html>
                <head>
                    <title>Marks and Attendance Details [IIIT Hyderabad]</title>
                    <meta charset='UTF-8' />
                    <style>
                    table, td, th { border-color: #999; }
                    table { border-collapse:collapse; }
                    td, th { padding: .5em; text-align:left;}
                    </style>
                </head>
                <body>
                    <p>Dear Parent,</p>
                    <p>
                        Please find below the attendance for $task_data->attendance_month
                        and $task_data->exam_name marks of your ward $student_data->name</p>
                    <table border='1'>
                        <thead>
                            <tr>
                                <th>Course name</th>
                                <th>Course code</th>
                                <th>Course credits</th>
                                <th>Total classes</th>
                                <th>Classes missed</th>
                                <th>Maximum marks</th>
                                <th>Marks secured</th>
                                <th>Position</th>
                            </tr>
                        </thead>
                        <tbody>
                ";
        foreach($course_data as $d) {
            if( ! $d->course_credits) $d->course_credits = "";
            if( ! $d->classes_total) $d->classes_total = "";
            if( ! $d->max_marks) $d->max_marks = "";
            if( ! $d->marks_secured) $d->marks_secured = "";

            $msg .= "<tr>";
            $msg .= "<td>$d->course_name</td>";
            $msg .= "<td>$d->course_code</td>";
            $msg .= "<td>$d->course_credits</td>";
            $msg .= "<td>$d->classes_total</td>";
            $msg .= "<td>$d->classes_missed</td>";
            $msg .= "<td>$d->max_marks</td>";
            $msg .= "<td>$d->marks_secured</td>";
            $msg .= "<td>$d->position</td>";
            $msg .= "</tr>";
        }
        $msg .= "</tbody>
            </table>
            <p>
                Regards,<br/>
                Academic office<br/>
                IIIT Hyderabad
            </p>
            $op
        </body>
        </html>";

        return $msg;
    }
}
