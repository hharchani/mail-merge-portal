<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html>
<head>
    <title>Send Emails</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css" >
    <style>
    .container {
        max-width: 860px;
        padding-left: 10px;
        padding-right: 10px;
    }
    header {
        height: 121px;
        padding: 24px 0 36px 0;
        border-bottom: 1px solid #AACCEE;
    }
    nav img {
        height: 60px;
        width: auto;
    }
    nav p {
        line-height: 30px;
    }
    main {
        min-height: 400px;
    }
    label, input[type="file"] {
        cursor: pointer;
    }
    footer {
        padding-top: 1em;
        border-top: 1px solid #AACCEE;
    }
    iframe {
        border: 1px solid #ccc;
        height: 250px;
    }
    </style>
    <script src="<?php echo base_url(); ?>assets/bower_components/jquery/dist/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
</head>
<body class="container">
    <header>
        <nav>
            <a href="<?php echo base_url(); ?>">
                <img src="<?php echo base_url(); ?>assets/img/logo.png" >
            </a>
        </nav>
    </header>
    <main>
        <p class="text-right alert">
            <?php echo $email; ?>
            <a class="btn btn-default" href="<?php echo base_url('logout'); ?>">Logout</a>
        </p>
