<div class="row">
        <div  id="param" class="col-md-24 center-block form-horizontal"> 
    <h1><?php echo lang('edit_user_heading');?></h1>
    <p><?php echo lang('edit_user_subheading');?></p>
    
    <div id="infoMessage"><?php echo $message;?></div>
    <div class="form-horizontal">
    <?php echo form_open(uri_string());?>
    
          <div class="form-group">
                <?php echo lang('edit_user_fname_label', 'first_name');?>  
                <?php echo form_input($first_name);?>
          </div>
    
          <div class="form-group">
                <?php echo lang('edit_user_lname_label', 'last_name');?>  
                <?php echo form_input($last_name);?>
          </div>
    
          <div class="form-group">
                <?php echo lang('edit_identity_lname_label', 'identity');?>  
                <?php echo form_input($username);?>
          </div>
    
          <div class="form-group">
                <?php echo lang('create_user_email_label', 'email');?>  
                <?php echo form_input($email);?>
         </div>
    
          <div class="form-group">
                <?php echo lang('edit_user_company_label', 'company');?> 
                <?php echo form_input($company);?>
          </div>
    
          <div class="form-group">
                <?php echo lang('edit_user_phone_label', 'phone');?>  
                <?php echo form_input($phone);?>
          </div>
    
          <div class="form-group">
                <?php echo lang('edit_user_password_label', 'password');?>  
                <?php echo form_input($password);?>
          </div>
    
          <div class="form-group">
                <?php echo lang('edit_user_password_confirm_label', 'password_confirm');?> 
                <?php echo form_input($password_confirm);?>
          </div>
    </div>
          
    
          <?php echo form_hidden('id', $user->id);?>
          <?php echo form_hidden($csrf); ?>
    
          <p><?php echo form_submit('submit', lang('edit_user_submit_btn'));?></p>
    
    <?php echo form_close();?>
        
    </div>
</div>
