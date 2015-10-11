<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<h2 class="text-center">Email Portal</h2>
<ul class="nav nav-tabs nav-justified">
    <li class="active"><a href="<?php echo base_url('main/submit'); ?>">Send Email</a></li>
    <li><a href="<?php echo base_url('main/status'); ?>">Check Status</a></li>
    <li><a href="<?php echo base_url('users'); ?>">Manage Users</a></li>
</ul>
<?php echo form_open_multipart(
    base_url('main/upload'),
    array('method'=>'post', 'class'=>'form-horizontal well'));
?>
    <div class="form-group">
        <label class="col-sm-5 control-label" for="emails">Email Ids*</label>
        <div class="col-sm-7">
            <input type="file" id="email" name="email" required>
            <span class="email_msg"></span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-5 control-label" for="marks">Marks and attendance file*</label>
        <div class="col-sm-7">
            <input type="file" id="marks" name="marks" required>
            <span class="marks_msg"></span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-5 control-label" for="marks">Exam Name*</label>
        <div class="col-sm-7">
            <input type="text" id="exam_name" name="exam_name" required>
            <span class="exam_name_msg"></span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-5 control-label" for="marks">Attendance Month*</label>
        <div class="col-sm-7">
            <input type="text" id="attendance_month" name="attendance_month" required>
            <span class="attendance_month_msg"></span>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-2 col-sm-offset-5">
            <button type="submit" class="btn btn-default btn-success form-control">Send</button>
        </div>
    </div>
</form>
<script>
    var base_url = '<?php echo base_url(); ?>';
    $(function(){
        $('input').on('input propertychange change paste', function(){
            $('.'+ this.name +'_msg').text('');
        });
        $('form').on('submit', function(e){
            var formData = new FormData(this);
            $.ajax({
                url: this.action,
                type: 'POST',
                data: formData,
                async: true,
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    var success = true;
                    $('input').each(function(){
                        var name = this.name;
                        if ( data[name].success) {
                            $('.'+name+'_msg').text('');
                        }
                        else {
                            $('.'+name+'_msg')
                            .addClass('text-danger')
                            .text(data[name].errors);
                            success = false;
                        }
                    });
                    if (success) {
                        sessionStorage.setItem('show_form_success_msg', 'true');
                        window.location = base_url + 'main/status/' + data.task_id;
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });
            e.preventDefault();
        });
    });
</script>
