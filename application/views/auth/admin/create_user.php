<?php
$this->view_path = "sylvain";
$base_url= base_url().$this->view_path;
?>
<h1><?php echo lang('create_user_heading');?></h1>
<p><?php echo lang('create_user_subheading');?></p>

<div id="infoMessage"><?php echo $message;?></div>

<?php echo form_open("auth/create_user");?>

      <table>
          <tr>
               <td><?php echo lang('create_user_fname_label', 'first_name');?> </td>
               <td><?php echo form_input($first_name);?></td>
          </tr>
          <tr>
                <td>
                    <?php echo lang('create_user_lname_label', 'last_name');?> 
                </td>
                <td>
                    <?php echo form_input($last_name);?>
                </td>
          </tr>
          <tr>
                <td><?php echo lang('create_user_identity_label', 'identity'); ?>
                </td>
                <td>
                    <?php  echo form_input($identity); ?>
                </td>
         </tr>
        <tr>
                <td>
                    <?php echo lang('create_user_company_label', 'company');?> </td><td>
                    <?php echo form_input($company);?>
                </td>
        </tr>
        
        <tr><td>
            <?php echo lang('create_user_email_label', 'email');?> </td><td>
            <?php echo form_input($email,array('size' =>'50','name'=>'email'));?>
        </td></tr>
        
        <tr><td>
            <?php echo lang('create_user_phone_label', 'phone');?> </td><td>
            <?php echo form_input($phone);?>
        </td></tr>
        
        <tr><td>
            <?php echo lang('create_user_password_label', 'password');?> </td><td>
            <?php echo form_input($password);?>
        </td></tr>
        
        <tr><td>
            <?php echo lang('create_user_password_confirm_label', 'password_confirm');?> </td><td>
            <?php echo form_input($password_confirm);?>
        </td></tr>


      <tr><td colspan=2><?php echo form_submit('submit', lang('create_user_submit_btn'));?></td></tr>
      </table>
      
<?php echo form_close();?>
