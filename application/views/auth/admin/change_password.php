<?php
/**
* The Expression Database.
*       view auth/admin/change_password.php
*       change password form
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage views
*/
?>
<!-- //////////////    auth/admin/change_password      //////////////  -->
<div class="row">
    <div  id="param" class="page-header col-md-6 center-block"> 
        <h1><?php echo lang('change_password_heading');?></h1>
        
        <div id="infoMessage"><?php echo $message;?></div>
        
        <?php echo form_open("auth/change_password");?>
        
              <p>
                    <?php echo lang('change_password_old_password_label', 'old_password');?> <br />
                    <?php echo form_input($old_password);?>
              </p>
        
              <p>
                    <label for="new_password"><?php echo sprintf(lang('change_password_new_password_label'), $min_password_length);?></label> <br />
                    <?php echo form_input($new_password);?>
              </p>
        
              <p>
                    <?php echo lang('change_password_new_password_confirm_label', 'new_password_confirm');?> <br />
                    <?php echo form_input($new_password_confirm);?>
              </p>
        
              <?php echo form_input($user_id);?>
              <p><?php echo form_submit('submit', lang('change_password_submit_btn'));?></p>
        
        <?php echo form_close();?>
    </div><!--  End Div param -->
</div><!--  End Div row  -->
<!-- //////////////    End auth/admin/change_password      //////////////  -->
