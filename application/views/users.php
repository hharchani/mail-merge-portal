<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<h2 class="text-center">Email Portal</h2>
<ul class="nav nav-tabs nav-justified">
    <li><a href="<?php echo base_url('main/submit'); ?>">Send Email</a></li>
    <li><a href="<?php echo base_url('main/status'); ?>">Check Status</a></li>
    <li class="active"><a href="<?php echo base_url('users'); ?>">Manage Users</a></li>
</ul>
<?php echo form_open_multipart(
    base_url('users/add'),
    array('method'=>'post', 'class'=>'well'));
?>
<p>Add a user</p>
<input type="email" placeholder="Email" name="email" required=""/>
<button class="btn btn-success" type="submit">Add User</button>
</form>

<?php foreach($users as $user): ?>
    <div>
        <?php echo $user->email; ?>
        <form method="POST" action="<?php echo base_url('users/delete');?>" style="display: inline-block;">
            <input type="hidden" name="email" value="<?php echo $user->email;?>">
            <button class="btn btn-danger" type="submit">Delete</button>
        </form>
    </div>
<?php endforeach; ?>
