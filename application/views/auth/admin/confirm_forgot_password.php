<?php
/**
* The Expression Database.
*       view auth/admin/confirm_forgot_password.php
*       confirm_forgot_password form
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage views
*/
?>
<!-- //////////////    auth/admin/confirm_forgot_password      //////////////  -->
<div class="row">
    <div  id="param" class="page-header col-md-16 center-block">
        <h1><?php echo lang('forgot_password_successful');?></h1>
        
        <div id="infoMessage"><?php echo $message;?></div>
        
        
              <p>
                <label for="identity"><?php echo lang('forgot_password_validation_email_label');?></label> <br />
                <?php echo print_r($email,1);?>
              </p>        
             
        <?php echo $message;?>
        
    </div><!--  End Div param -->
</div><!--  End Div row  -->

<!-- //////////////    End auth/admin/confirm_forgot_password      //////////////  -->
