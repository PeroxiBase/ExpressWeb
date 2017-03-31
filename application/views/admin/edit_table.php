<?php
/**
* The Expression Database.
*       view admin/edit_table
*       operations on tables stored in Db
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage view
*/
print "<!-- //////////////    admin/edit_table  //////////////  -->\n";
print "<div class=\"row\">\n";
print "     <div  id=\"param\" class=\"col-md-8 left-block form-horizontal\"> \n";
print "         <a href=\"".base_url()."admin/manage_tables\">back to Admin </a><br />\n";

print form_open(uri_string());
print form_hidden('IdTables',$IdTables)."<br />\n ";
##### sub_tables resolution
if($disabled =="disabled") 
{
    print form_hidden('disabled',1)."<br />\n ";
}

print "         <div class=\"form-group\">".form_label('Table name','TableName',array('class' =>"col-sm-2 control-label"))."\n";
### Never rename Annotation or Toolbox  table . Used internaly by reference on organism!
if(!preg_match("/Annotation_|Toolbox_/",$TableName))
{
    print "                 <div class=\"col-sm-8\">".form_input('TableName',$TableName,"size =30")."</div>\n";
}
else
{
    print "                 <div class=\"col-sm-8\">".form_input('TableName',$TableName,"size =30 disabled")."</div>\n";
    print "                 <div class=\"col-sm-8\">".form_hidden('TableName',$TableName)."</div>\n";
}
print "         </div>\n";

print "         <div class=\"form-group\">".form_label('MasterGroup','',array('class' =>"col-sm-2 control-label"))."\n"; 
print "                 <div class=\"col-sm-8\">".form_dropdown('MasterGroup',$options_group,$MasterGroup)."</div>\n "; 
print "         </div>\n";

print "         <div class=\"form-group\">".form_label('Organism','',array('class' =>"col-sm-2 control-label"))."\n";
print "                 <div class=\"col-sm-8\">".form_dropdown('Organism' ,$options_organisms, $Organism)."</div>\n ";
print "         </div>\n";

print "         <div class=\"form-group\">".form_label('Submitter','',array('class' =>"col-sm-2 control-label"))."\n";
print "                 <div class=\"col-sm-8\">".form_input('Submitter' , $Submitter)."</div>\n ";
print "         </div>\n";

print "         <div class=\"form-group\">".form_label('Version','',array('class' =>"col-sm-2 control-label"))."\n";
print "                 <div class=\"col-sm-8\">".form_input('version', $version)."</div>\n";
print "         </div>\n";

print "         <div class=\"form-group\">".form_label('Comment','',array('class' =>"col-sm-2 control-label"))."\n";
print "                 <div class=\"col-sm-8\">".form_textarea('comment', $comment)." </div>\n";
print "         </div>\n";

 # print "<pre>".print_r($groups,1)."</pre>\n";   
if ($this->ion_auth->is_admin())
{
    #################  forbidden group assignment for Annotation tables ##################
    if(!preg_match("/Annotation_|Toolbox_/",$TableName))
    {
    
        ?>
        <h3><?php echo lang('edit_user_groups_heading');?></h3>
        <div class=" col-md-12">
        
        <?php 
    
    
        if($missing)
        {
            print "<p class=\"alert alert-danger\">No rights assigned for this table...</p>\n";
        }
        foreach ($groups as $group)
        {
        ?>
            <!--  <label class="checkbox">-->
            <?php
            $gID=$group['id'];
            $checked = null;
            $item = null;
            #### prevent case (!) where table is not in tables_groups...
            if($currentGroups->nbr >0)
            {
                foreach($currentGroups->result as $grp) 
                {                
                    $table_grp = $grp['group_id'];
                    $table_id  = $grp['id'];
                    
                    if ($gID == $table_grp) 
                    {
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
                print  "<input type=hidden name ='currentGroups[$table_id]' value=\"$table_grp\" />";
            }
            else
            {
                $checked = null;                      
                $group_name =  htmlspecialchars($group['name'],ENT_QUOTES,'UTF-8');
            }
            
            
            print "<input type=\"checkbox\" name=\"groups[]\" value=\"$gID\" $checked  $disabled /> $group_name ";
        } #<!-- End foreach($group) --> 

    print "         <br /></div><br />\n";
    } #<!-- End if not "Annotation_" --> 
}

print           form_submit('submit', "update table info")." ";
print           form_submit('reset', "Reset")." ";
print           form_close();
print "         <br />";
print "    </div><!-- End DIV param -->";
print " </div><!-- End DIV rows -->\n";
print "<!-- //////////////    End admin/edit_table  //////////////  -->\n";
