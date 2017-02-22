<div class="row">
    <div  id="param" class="col-md-8 center-block form-horizontal"> 
    
       <div class="form-group">
        <h1><?php echo lang('login_heading');?></h1>
        <p><?php echo lang('login_subheading');?></p>
        <p>Want to try ? use 'demo' as username and 'password' as password</p>
        <div id="infoMessage"><?php echo $message;?> </div>
        </div>
        <?php echo form_open("auth/login");?>
        
          <div class="form-group">
            <?php echo lang('login_identity_label', 'identity','class="col-xs-3"');?>
            <?php echo form_input($identity);?>
          </div>
        
          <div class="form-group">
            <?php echo lang('login_password_label', 'password','class="col-xs-3"');?>
            <?php echo form_password($password);?>
          </div>
        
          <div class="form-group">
            <?php echo lang('login_remember_label', 'remember');?>
            <?php echo form_checkbox('remember', '1', FALSE, 'id="remember"');?>
          </div>
        
        
          <p><?php echo form_submit('submit', lang('login_submit_btn'));?></p>
        
        <?php echo form_close();?>
         <div class="form-group">
            <p><a href="forgot_password"><?php echo lang('login_forgot_password');?></a></p>
        </div>
    </div>
</div>
