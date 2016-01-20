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

    public function get_sample_email() {

        $s = new stdClass();
        $s->name = 'Harshit Harchani';
        $s->sgpa = 9.5;
        $c = array();
        $d = new stdClass();
        $d->course_name     = 'Compilers';
        $d->course_code     = 'CSE419';
        $d->course_credits  = 4;
        $d->classes_total   = 6;
        $d->classes_missed  = 1;
        $d->max_marks       = 25;
        $d->marks_secured   = 20;
        $d->grade           = 'B';
        $c[] = $d;

        $d = new stdClass();
        $d->course_name     = 'Research in Information Security';
        $d->course_code     = 'CSE540';
        $d->course_credits  = 4;
        $d->classes_total   = 6;
        $d->classes_missed  = 0;
        $d->max_marks       = 100;
        $d->marks_secured   = 55;
        $d->grade           = 'A';
        $c[] = $d;

        $t = new stdClass();

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
                        Please find below the Monsoon 2015 semester's attendance and grades for your ward $student_data->name
                    </p>
                    <table border='1'>
                        <thead>
                            <tr>
                                <th>Course name</th>
                                <th>Course code</th>
                                <th>Course credits</th>
                                <th>Total classes</th>
                                <th>Classes missed</th>
                                <th>Grade</th>
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
            $msg .= "<td>$d->grade</td>";
            $msg .= "</tr>";
        }
        $msg .= "</tbody>
            </table>
            <br/>
            <p>
                SGPA for the Semester Monsoon 2015: $student_data->sgpa
            </p>
            <br/>
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
