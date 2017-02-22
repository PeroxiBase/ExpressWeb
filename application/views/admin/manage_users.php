<?php
print "<div class=\"row\">\n";
print "    <div  id=\"param\" class=\"col-md-8 center-block\"> \n";
print "         <a href=\"create_table\">back to Admin Express Db</a><br /><br />\n";

print "<table class=\"table table-condensed table-hover  table-bordered\" >\n";
print " <thead>\n";
print "         <tr><th>UserName</th><th>Identity</th><th>Company</th><th>Groups</th><th>Action</th></tr>\n";
print " </thead>\n";
print " <tbody>\n";
$same_table = "";
#print "groups: $groups->sql <br>";
foreach($users->result as $row)
{
    $Id = $row['id'];
    $username = $row['username'];
    $first_name = $row['first_name'];
    $last_name = $row['last_name'];
    $company = $row['company'];
    $anc_action =anchor("admin/edit_user/$Id","Ed."); 
    $Groups = "";
    print "    <tr>\n";
    print "            <td>$username</td>\n";
    print "            <td>$first_name $last_name</td>\n";
    print "            <td>$company</td>\n";
    foreach($groups->result as $grp)
    {
        $group = $grp['name'];
        $grp_id = $grp['group_id'];
        $user_tbl = $grp['user_id'];
        if($user_tbl ==$Id)
        {
            
               # print "<br />$group";
            $Groups .="<br />$group";
            
        }
    }
   // print "             </td>\n";
    
   
    print "            <td>$Groups</td>\n";
    print "            <td>$anc_action</td>\n";
    print "    </tr>\n";
}
print " </tbody>\n";
print "</table>\n";
print " </div>\n";
print " </div>\n";
