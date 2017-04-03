<?php
/**
* The Expression Database.
*       view auth/admin/create_user.php
*       create user account
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage views
*/
?>
<!-- //////////////    auth/admin/create_user      //////////////  -->
<div class="row">
    <div  id="param" class="page-header col-md-16 center-block"> 
        <a href="../auth/index">Back to Users management</a><br />
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
                <tr>
                    <td>
                        <?php echo lang('create_user_email_label', 'email');?> </td><td>
                        <?php echo form_input($email,array('size' =>'50','name'=>'email'));?>
                    </td>
                </tr>                
                <tr>
                    <td>
                        <?php echo lang('create_user_phone_label', 'phone');?> </td><td>
                        <?php echo form_input($phone);?>
                    </td>
                </tr>
                
                <tr>
                    <td>
                        <?php echo lang('create_user_password_label', 'password');?> </td><td>
                        <?php echo form_input($password);?>
                    </td>
                </tr>
                
                <tr>
                    <td>
                        <?php echo lang('create_user_password_confirm_label', 'password_confirm');?> </td><td>
                        <?php echo form_input($password_confirm);?>
                    </td>
                </tr>
                <tr>
                    <td colspan=2>
                        <?php echo form_submit('submit', lang('create_user_submit_btn'));?>
                    </td>
                </tr>
            </table>
              
        <?php echo form_close();?>
        </div> <!--  End Div form -->
    </div><!--  End Div param -->
</div><!--  End Div row  -->

<!-- //////////////    End auth/admin/create_user      //////////////  -->
