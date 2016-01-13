<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bg_processing {

    public function send_and_close($value='') {
        @ob_end_clean();
        header("Connection: close\r\n");
        header("Content-Encoding: none\r\n");
        ignore_user_abort(true); // optional
        ob_start();

        echo $value;

        $size = ob_get_length();
        header("Content-Length: $size");
        ob_end_flush();     // Strange behaviour, will not work
        flush();            // Unless both are called !
        @ob_end_clean();
        if (session_id()) session_write_close();
    }
}
