<?php
/**
* The Expression Database.
*       view admin/manage_users.php
*       view users account. create users...
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage views
*/
print "<!-- //////////////    admin/manage_users  //////////////  -->\n";
print "<div class=\"row\">\n";
print "    <div  id=\"param\" class=\"col-md-10 center-block\"> \n";
print "         <a href=\"../create_table\">back to Admin Express Db</a><br /><br />\n";
print "         <table class=\"table table-condensed table-hover  table-bordered\" >\n";
print "             <thead>\n";
print "                 <tr><th>UserName</th><th>Identity</th><th>Company</th><th>Groups</th><th>Action</th></tr>\n";
print "             </thead>\n";
print "             <tbody>\n";
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
    print "                 <tr>\n";
    print "                    <td>$username</td>\n";
    print "                    <td>$first_name $last_name</td>\n";
    print "                    <td>$company</td>\n";
    foreach($groups->result as $grp)
    {
        $group = $grp['name'];
        $grp_id = $grp['group_id'];
        $user_tbl = $grp['user_id'];
        if($user_tbl ==$Id)
        {
            $Groups .="<br />$group";            
        }
    }
    print "                    <td>$Groups</td>\n";
    print "                    <td>$anc_action</td>\n";
    print "                  </tr>\n";
}
print "                 </tbody>\n";
print "         </table>\n";
print "       </div><!-- End DIV param -->\n";
print " </div><!-- End DIV rows -->\n";
print "<!-- //////////////    admin/manage_users  //////////////  -->\n";
