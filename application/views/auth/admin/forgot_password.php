<?php
/**
* The Expression Database.
*       view auth/admin/forgot_password.php
*       forgot_password form
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage views
*/
?>
<!-- //////////////    auth/admin/forgot_password      //////////////  -->
<div class="row">
    <div  id="param" class="page-header col-md-16 center-block">
        <h1><?php echo lang('forgot_password_heading');?></h1>
        <p><?php echo sprintf(lang('forgot_password_subheading'), $identity_label);?></p>
        
        <div id="infoMessage"><?php echo $message;?></div>
        
        <?php echo form_open("auth/forgot_password");?>
        
              <p>
                <label for="identity"><?php echo (($type=='email') ? sprintf(lang('forgot_password_email_label'), $identity_label) : sprintf(lang('forgot_password_identity_label'), $identity_label));?></label> <br />
                <?php echo form_input($identity);?>
              </p>
        
              <p><?php echo form_submit('submit', lang('forgot_password_submit_btn'));?></p>
        <?php echo $message;?>
        <?php echo form_close();?>
    </div><!--  End Div param -->
</div><!--  End Div row  -->

<!-- //////////////    End auth/admin/forgot_password      //////////////  -->
