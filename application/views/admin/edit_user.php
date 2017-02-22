<div class="row">
        <div  id="param" class="col-md-16 center-block form-horizontal"> 
<div class="form-horizontal">
<?php

print form_open(uri_string());
print form_hidden('Id',$Id)."<br /> ";
print "<div class=\"form-group\">\n";
print form_label('Login Name')." ";     print form_input('username',$username)."<br /> ";
print form_label('first_name')." ";               print form_input('first_name' , $first_name)."<br /> ";
print form_label('last_name')." ";                print form_input('last_name', $last_name)."<br>";
print form_label('email')." ";                print form_input('email', $email)."<br>";

print form_label('company')." ";                print form_input('company', $company)."<br>";
print "</div>\n";
/* print "<pre>".print_r($options_group,1)."</pre>"; */
 //print "<pre>".print_r($currentGroups,1)."</pre>";
//print "<pre>".print_r($groups[2],1)."</pre>"; /**/

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
                  print  "<input type=hidden name ='currentGroups[$table_id]' value=\"$table_grp\" /> ";
              print "<input type=\"checkbox\" name=\"groups[]\" value=\"$gID\" $checked />$group_name \n";
              print "   </label>\n";
              endforeach; 
              print "</div>";
          endif; 
    print "<br />\n";
     print  form_submit('submit', "update user info")." ";
    print  form_submit('reset', "Reset")." ";
echo form_close();?>
</div>
</div>
</div>
