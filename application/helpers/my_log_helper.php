<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function my_log($message) {
    log_message('error', 'LOG: '.$message);
}
