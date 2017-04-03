<?php
/**
* The Expression Database.
*       view auth/login.php
*       login page
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage views
*/
?>
<!-- //////////////    login  //////////////  -->
<div class="row">
    <div  id="param" class="col-md-8 center-block form-horizontal"> 
        <div class="form-group">
            <h1><?php print lang('login_heading');?></h1>
            <p><?php print lang('login_subheading');?></p>
            <p>Want to try ? use 'demo' as username and 'password' as password</p>
            <div id="infoMessage"><?php print $message;?> </div>
        </div>
        <?php 
                if($first_login)
                {
                    print form_open("auth/login/Admin")."\n";
                    print form_hidden('first_login',1);
                }
                else
                {
                    print form_open("auth/login")."\n";
                    print form_hidden('first_login',0);
                }
        ?>
        <div class="form-group">
            <?php print lang('login_identity_label', 'identity','class="col-xs-3"')."\n";?>
            <?php print form_input($identity);?>
        </div>
        
        <div class="form-group">
            <?php print lang('login_password_label', 'password','class="col-xs-3"')."\n";?>
            <?php print form_password($password);?>
        </div>
        
        <div class="form-group">
            <?php print lang('login_remember_label', 'remember')."\n";?>
            <?php print form_checkbox('remember', '1', FALSE, 'id="remember"');?>
        </div>        
        
        <p><?php print form_submit('submit', lang('login_submit_btn'));?></p>
        
        <?php print form_close();?>
        
        <div class="form-group">
                <p><a href="forgot_password"><?php print lang('login_forgot_password');?></a></p>
        </div>
        
    </div> <!-- End div param -->
</div><!-- End div row -->
<!-- //////////////    End login  //////////////  -->
