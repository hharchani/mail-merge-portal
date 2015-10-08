<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email_wrapper {
    private $subj = 'Marks and Attendance Details [IIIT Hyderabad]';

    public function __construct() {
        $this->ci = & get_instance();
        $config = [];
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'students.iiit.ac.in';
        $config['smtp_user'] = 'harshit.harchani';
        $config['smtp_pass'] = '';
        $config['smtp_port'] = 25;
        $config['crlf'] = "\r\n";
        $config['newline'] = "\r\n";
        $config['mailtype'] = 'html';
        $this->ci->load->library('email', $config);
    }

    public function send($student_data, $course_data, $task_data) {
        $this->ci->email->from('harshit.harchani@students.iiit.ac.in', 'Harshit Harchani');
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
        return $this->get_msg($s, $c, $t);
    }

    private function get_msg($student_data, $course_data, $task_data) {
        $msg = "<!DOCTYPE html>
                <html>
                <head>
                    <title>Marks and Attendance Details [IIIT Hyderabad]</title>
                    <meta charset='UTF-8' />
                    <style>
                    table { border-collapse:collapse;}
                    td, th { padding: .5em; text-align:left;}
                    </style>
                </head>
                <body>
                    <p>Dear Parent,</p>
                    <p>
                        Please find below the attendance for $task_data->attendance_month
                        and $task_data->exam_name marks of your ward $student_data->name</p>
                    <table>
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
            <p>Regards,</p>
            <p>IIIT Hyderabad</p>
            <script>
                function getDocHeight() {
                    return document.documentElement.offsetHeight;
                }
            </script>
        </body>
        </html>";

        return $msg;
    }
}
