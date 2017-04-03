<?php
/**
* The Expression Database.
*       view auth/admin/edit_user.php
*       edit user account
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage views
*/
?>
<!-- //////////////    auth/admin/edit_user      //////////////  -->
<div class="row">
    <div  id="param" class="page-header col-md-16 center-block"> 
        <a href="../index">Back to Users management</a><br />
      
        <h1><?php echo lang('edit_user_heading');?></h1>
        <p><?php echo lang('edit_user_subheading');?></p>
        
        <div id="infoMessage"><?php echo $message;?></div>
        
        <div class="form-horizontal">
            <?php echo form_open(uri_string());?>
                
              <div class="form-group">
                    <?php echo lang('edit_user_fname_label', 'first_name',array('class' =>"col-sm-4 control-label"));?>  
                    <?php echo form_input($first_name);?>
              </div>
        
              <div class="form-group">
                    <?php echo lang('edit_user_lname_label', 'last_name',array('class' =>"col-sm-4 control-label"));?>  
                    <?php echo form_input($last_name);?>
              </div>
              <div class="form-group">
                    <?php echo lang('edit_identity_lname_label', 'identity',array('class' =>"col-sm-4 control-label"));?>  
                    <?php echo form_input($username);?>
              </div>
              <div class="form-group">
                    <?php echo lang('create_user_email_label', 'email',array('class' =>"col-sm-4 control-label"));?>  
                    <?php echo form_input($email);?>
              </div>
              <div class="form-group">
                    <?php echo lang('edit_user_company_label', 'company',array('class' =>"col-sm-4 control-label"));?> 
                    <?php echo form_input($company);?>
              </div>
        
              <div class="form-group">
                    <?php echo lang('edit_user_phone_label', 'phone',array('class' =>"col-sm-4 control-label"));?>  
                    <?php echo form_input($phone);?>
              </div>
        
              <div class="form-group">
                    <?php echo lang('edit_user_password_label', 'password',array('class' =>"col-sm-4 control-label"));?>  
                    <?php echo form_input($password);?>
              </div>
        
              <div class="form-group">
                    <?php echo lang('edit_user_password_confirm_label', 'password_confirm',array('class' =>"col-sm-4 control-label"));?> 
                    <?php echo form_input($password_confirm);?>
              </div> 
                      
                      
              <?php if ($this->ion_auth->is_admin()): ?>
        
                  <h3><?php echo lang('edit_user_groups_heading',array('class' =>"col-sm-4 control-label"));?></h3>
                  <div class="form-inline">
                  <?php foreach ($groups as $group):?>
                      <label class="checkbox">
                      <?php
                          $gID=$group['id'];
                          $checked = null;
                          $item = null;
                          foreach($currentGroups as $grp) {
                              if ($gID == $grp->id) {
                                  $checked= ' checked="checked"';
                              break;
                              }
                          }
                      ?>
                      <input type="checkbox" name="groups[]" value="<?php echo $group['id'];?>"<?php echo $checked;?>>
                      <?php echo htmlspecialchars($group['name'],ENT_QUOTES,'UTF-8');?>
                      </label>
                  <?php endforeach; ?>
                  </div>
              <?php endif; ?>
        
              <?php echo form_hidden('id', $user->id);?>
              <?php echo form_hidden($csrf); ?>
              <br />
              <p><?php echo form_submit('submit', lang('edit_user_submit_btn'));?></p>
            
            <?php echo form_close();?>
        </div> <!--  End Div form -->
    </div><!--  End Div param -->
</div><!--  End Div row  -->

<!-- //////////////    End auth/admin/edit_user      //////////////  -->
