<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<h2 class="text-center">Email Portal</h2>
<ul class="nav nav-tabs nav-justified">
    <li><a href="<?php echo base_url('main/submit'); ?>">Send Email</a></li>
    <li class="active"><a href="<?php echo base_url('main/status'); ?>">Check Status</a></li>
    <li><a href="<?php echo base_url('users'); ?>">Manage Users</a></li>
</ul>
<div class="success-msg alert alert-success" style="display:none;">
    Successfully uploaded files.
</div>
<?php if ($task_details): ?>
<div class="well well-small">
    <div class="row">
        <div class="col-sm-3">Sent By</div>
        <div class="col-sm-9"><?php echo $task_details->created_by; ?></div>
    </div>
    <div class="row">
        <div class="col-sm-3">On Date</div>
        <div class="col-sm-9"><?php echo $task_details->time_created; ?></div>
    </div>
    <div class="row">
        <div class="col-sm-3">Emails sent</div>
        <div class="col-sm-9"><span class="emails-sent"><?php echo $task_details->emails_sent; ?></span></div>
    </div>
    <div class="row">
        <div class="col-sm-3">Emails failed</div>
        <div class="col-sm-9"><span class="emails-failed"><?php echo $task_details->emails_failed; ?></span></div>
    </div>
    <div class="row">
        <div class="col-sm-3">Exam Name</div>
        <div class="col-sm-9"><?php echo $task_details->exam_name; ?></div>
    </div>
    <div class="row">
        <div class="col-sm-3">Attendance Month</div>
        <div class="col-sm-9"><?php echo $task_details->attendance_month; ?></div>
    </div>
    <div class="row">
        <div class="col-sm-3">Status</div>
        <div class="col-sm-9"><span class="task-status"><?php echo $task_details->status; ?></span></div>
    </div>
</div>
<h2 class="open-sample" style="cursor: pointer;">Sample Email <span class="glyphicon glyphicon-plus"></span></h2>
<iframe style="display:none;" width="100%" src="<?php echo base_url("main/sample_email/$task_details->exam_name/$task_details->attendance_month");?>"></iframe>
<script>
    $(function(){
        $('.open-sample').click(function(){
            $('iframe').slideDown(function(){
                $(this).animate({
                    'height': this.contentWindow.getDocHeight() + 10
                }, 100);
            });
        });
    });
</script>
<?php endif ?>
<h2>Status</h2>
<div class="output well"></div>
<script>
    var base_url = '<?php echo base_url(); ?>';
    function fetchStatus() {
        $.ajax({
            url: base_url + 'main/get_task_status/<?php echo $task_id; ?>' ,
            dataType : 'json',
            success : function(response) {
                if (typeof response.message === typeof '') {
                    $('.output').text(response.message);
                }
                else if (Array.isArray(response.message)) {
                    $output = $('.output').empty();
                    response.message.forEach(function(msg){
                        var c = '';
                        if (/^Warning:/.test(msg)) {
                            c = 'text-danger';
                        }
                        else if (/^Info:/.test(msg)) {
                            c = 'text-info';
                        }
                        else if (/^Success:/.test(msg)) {
                            c = 'text-success';
                        }
                        $output.append(
                            $('<p>').text(msg).addClass( c )
                        );
                    });
                    $('.emails-sent').text( response.emails_sent );
                    $('.emails_failed').text( response.emails_failed );
                }
                if (response.status && response.status !== 'completed' ) {
                    setTimeout(fetchStatus, 1000);
                }
                if (response.status === 'completed') {
                    $('.task-status').text('Completed');
                }
            }
        });
    }
    $(fetchStatus);
    $(function(){
        if (sessionStorage.getItem('show_form_success_msg') === 'true') {
            $('.success-msg').show();
            sessionStorage.removeItem('show_form_success_msg');
        }
    });
</script>
