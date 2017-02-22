<?php
print "<div class=\"row\">\n";
print "     <div  id=\"param\" class=\"col-md-8 left-block form-horizontal\"> \n";
print "         <a href=\"".base_url()."admin/manage_tables\">back to Admin Express Db</a><br />\n";

print form_open(uri_string());
print form_hidden('IdTables',$IdTables)."<br /> ";
print " <div class=\"form-group\">\n";
print form_label('Table name','TableName',array('class' =>"col-sm-2 control-label"))." ";
print " <div class=\"col-sm-8\">\n";
print form_input('TableName',$TableName)."</div>";
print "</div>\n";
print " <div class=\"form-group\">\n";
print form_label('MasterGroup','',array('class' =>"col-sm-2 control-label"))." "; 
print " <div class=\"col-sm-8\">\n";
print form_dropdown('MasterGroup',$options_group,$MasterGroup)."</div> "; 
print "</div>\n";
print " <div class=\"form-group\">\n";
print form_label('Organism','',array('class' =>"col-sm-2 control-label"))." ";
print " <div class=\"col-sm-8\">\n";
print form_dropdown('Organism' ,$options_organisms, $Organism)."</div> ";
print "</div>\n";
print " <div class=\"form-group\">\n";
print form_label('Submitter','',array('class' =>"col-sm-2 control-label"))." ";
print " <div class=\"col-sm-8\">\n";
print form_input('Submitter' , $Submitter)."</div> ";
print "</div>\n";
print " <div class=\"form-group\">\n";
print form_label('Version','',array('class' =>"col-sm-2 control-label"))." ";
print " <div class=\"col-sm-8\">\n";
print form_input('version', $version)."</div>";
print "</div>\n";
print " <div class=\"form-group\">\n";
print form_label('Comment','',array('class' =>"col-sm-2 control-label"))." ";
print " <div class=\"col-sm-8\">\n";
print form_textarea('comment', $comment)." </div><br />";
print "</div>\n";

  print "<br />$currentGroups->nbr\n";   
  if ($this->ion_auth->is_admin()): ?>
      <h3><?php echo lang('edit_user_groups_heading');?></h3>
      <div class=" col-md-12">
      <?php foreach ($groups as $group):?>
        <!--  <label class="checkbox">-->
          <?php
              $gID=$group['id'];
              $checked = null;
              $item = null;
              foreach($currentGroups->result as $grp) {
                  
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
          print "<input type=\"checkbox\" name=\"groups[]\" value=\"$gID\" $checked />$group_name ";
           endforeach; 
      endif; 
    print "</div><br />\n";
    
print  form_submit('submit', "update table info")." ";
print  form_submit('reset', "Reset")." ";
print form_close();
print "<br />";
print " </div>";
print " </div>";
?>
