<?php
/**
* The Expression Database.
*       view admin/edit_user.php
*       edit user account
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage views
*/
?>
<!-- //////////////    admin/edit_users  //////////////  -->

<div class="row">
        <div  id="param" class="col-md-8 left-block "> <br />
         <a href="../manage_users">back to Admin </a><br />
                <div class="form-horizontal">
<?php

print form_open(uri_string());
print form_hidden('Id',$Id)."\n ";
print "     <div class=\"form-group\">\n";
print lang('edit_identity_lname_label', 'identity',array('class' =>"col-sm-4 control-label") )." ";
print form_input('username',$username)."<br />\n ";
print "     </div>\n";
print "     <div class=\"form-group\">\n";
print lang('edit_user_fname_label', 'first_name',array('class' =>"col-sm-4 control-label") )." ";
print form_input('first_name' , $first_name)."<br />\n ";
print "     </div>\n";
print "     <div class=\"form-group\">\n";

print lang('edit_user_lname_label', 'last_name',array('class' =>"col-sm-4 control-label") )." ";
print form_input('last_name', $last_name)."<br />\n";
print "     </div>\n";
print "     <div class=\"form-group\">\n";

print lang('create_user_email_label', 'email',array('class' =>"col-sm-4 control-label") )." ";                
print form_input('email', $email)."<br />\n";
print "     </div>\n";
print "     <div class=\"form-group\">\n";

print lang('edit_user_company_label', 'company',array('class' =>"col-sm-4 control-label") )." ";
print form_input('company', $company)."<br />\n";
print "     </div>\n"; 

print "<br />\n";   

  if ($this->ion_auth->is_admin()): ?>

          <h3><?php echo lang('edit_user_groups_heading');?></h3>
          <div class="form-inline">
          <?php foreach ($groups as $group):?>
          
              <label class="checkbox">
              <?php
                  $gID=$group['id'];
                  $checked = null;
                  $item = null;
                  foreach($currentGroups as $grp) {
                      
                      $table_grp = $grp['group_id'];
                      $table_id  = $grp['id'];
                      
                      if ($gID == $table_grp) {
                          $checked= ' checked="checked"';
                          $group_name =  htmlspecialchars($group['name'],ENT_QUOTES,'UTF-8');
                          break;
                      }
                      else 
                      {
                          $checked = null;                      
                          $group_name =  htmlspecialchars($group['name'],ENT_QUOTES,'UTF-8');
                      }                      
                  }
                  print  "<input type=hidden name ='currentGroups[$table_id]' value=\"$table_grp\" />\n ";
              print "<input type=\"checkbox\" name=\"groups[]\" value=\"$gID\" $checked />$group_name \n";
              print "   </label>\n";
              endforeach; 
              print "</div>\n";
          endif; 
    print "<br />\n";
     print  form_submit('submit', "update user info")."\n ";
    print  form_submit('reset', "Reset")."\n ";
echo form_close();?>
        </div>
    </div><!-- End DIV param -->
</div><!-- End DIV rows -->
<!-- //////////////    End admin/edit_users  //////////////  -->
