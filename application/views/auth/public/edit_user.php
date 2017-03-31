<?php
/**
* The Expression Database.
*       view auth/public/edit_user.php
*       edit user account
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage views
*/
?>
<!-- //////////////    auth/public/edit_user      //////////////  -->
<div class="row">
    <div  id="param" class=" col-md-16 center-block "> <br />
        <a href="update_account">Back to Profile management</a><br />
       
        <h1><?php print lang('edit_user_heading'); ?></h1>
        <p><?php print lang('edit_user_subheading'); ?></p>
        
        <div id="infoMessage"><?php print $message; ?></div>
        <?php if ($first_login)
                print "<span style=\"color:red;\"><b>This is your first login as Admin.</b> </span> Please change your password!!<br /><hr />";
        ?>
        <div class="form-horizontal">
            <?php print form_open(uri_string()); ?>
        
              <div class="form-group">
                    <?php print lang('edit_user_fname_label', 'first_name',array('class' =>"col-sm-4 control-label") ); ?>  
                    <?php print form_input($first_name); ?>
              </div>
        
              <div class="form-group">
                    <?php print lang('edit_user_lname_label', 'last_name',array('class' =>"col-sm-4 control-label") ); ?>  
                    <?php print form_input($last_name); ?>
              </div>
        
              <div class="form-group">
                    <?php print lang('edit_identity_lname_label', 'identity',array('class' =>"col-sm-4 control-label") ); ?>  
                    <?php print form_input($username); ?>
              </div>
        
              <div class="form-group">
                    <?php print lang('create_user_email_label', 'email',array('class' =>"col-sm-4 control-label") ); ?>  
                    <?php print form_input($email); ?>
             </div>
        
              <div class="form-group">
                    <?php print lang('edit_user_company_label', 'company',array('class' =>"col-sm-4 control-label") ); ?> 
                    <?php print form_input($company); ?>
              </div>
        
              <div class="form-group">
                    <?php print lang('edit_user_phone_label', 'phone',array('class' =>"col-sm-4 control-label") ); ?>  
                    <?php print form_input($phone); ?>
              </div>
        
              <div class="form-group">
                    <?php print lang('edit_user_password_label', 'password',array('class' =>"col-sm-4 control-label") ); ?>  
                    <?php print form_input($password); ?>
              </div>
        
              <div class="form-group">
                    <?php print lang('edit_user_password_confirm_label', 'password_confirm',array('class' =>"col-sm-4 control-label") ); ?> 
                    <?php print form_input($password_confirm); ?>
              </div>
          
    
          <?php print form_hidden('id', $user->id); ?>
          <?php print form_hidden($csrf); ?>
          <br />
          <p><?php print form_submit('submit', lang('edit_user_submit_btn')); ?></p>
    
          <?php print form_close(); ?>
        
        </div> <!--  End Div form -->
    </div><!--  End Div param -->
</div><!--  End Div row  -->
<!-- //////////////    End auth/public/edit_user      //////////////  -->
