<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Config for CAS library
|--------------------------------------------------------------------------
| Code Igniter CAS Library
| https://github.com/eliasdorneles/code-igniter-cas-library
| License: The MIT License (MIT)
*/

/*
|--------------------------------------------------------------------------
| CAS Server URL
|--------------------------------------------------------------------------
*/

$config['cas_server_url'] = 'https://login.iiit.ac.in/cas';

/*
|--------------------------------------------------------------------------
| PATH to phpCAS
|--------------------------------------------------------------------------
*/

$config['phpcas_path'] = APPPATH . 'third_party/vendor/jasig/phpcas';

$config['cas_disable_server_validation'] = true;

/*
|--------------------------------------------------------------------------
| Debug mode
|--------------------------------------------------------------------------
| Use this to enable phpCAS debug mode
*/
$config['cas_debug'] = false;
