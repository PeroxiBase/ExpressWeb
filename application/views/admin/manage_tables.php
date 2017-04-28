<?php
/**
* The Expression Database.
*       view admin/manage_tables.php
*       view Dataset stored in Db
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage views
*/
print "<!-- //////////////    admin/manage_tables  //////////////  -->\n";
print "<div class=\"row\">\n";

print "     <div  id=\"param\" class=\"col-md-10 center-block\"> \n";
print "         <a href=\"../create_table\">back to Admin Express Db</a><br /><br />\n";

print "         <table class=\"table table-hover table-condensed table-bordered\" >\n";
print "             <thead>\n";
print "                 <tr><th>TableName</th><th>Organism</th><th>Submitter</th><th>version</th><th>Groups</th><th>Level</th><th>Action</th></tr>\n";
$same_table = "";
#print "groups: $groups->sql <br>";
foreach($tables->result as $row)
{
    $IdTable = $row['IdTables'];
    $TableName = $row['TableName'];
    $MasterGroup = $row['MasterGroup'];
    $GroupName = $row['name'];
    $Organism = $row['Organism'];
    $Submitter = $row['Submitter'];
    $version = $row['version'];
    $Root = $row['Root'];
    $Child = $row['Child'];
    if($Child ==0) $ChildRef = $IdTable;
    else $ChildRef = $Child;
     $anc_action =anchor("admin/edit_table/$IdTable/$ChildRef","Ed."); 
    $Groups =  "[$GroupName]";
    print "               <tr>\n";
    if($Root)
    print "                 <th class=info>$TableName</th>\n";
    else
    print "                 <td>$TableName</td>\n";
    foreach($groups->result as $grp)
    {
        $group = $grp['name'];
        $grp_id = $grp['group_id'];
        $grp_tbl = $grp['table_id'];
        if($grp_tbl ==$IdTable)
        {
            if($grp_id == $MasterGroup )
            {
               # print "<br />$group";
            $Groups = preg_replace("/\[$GroupName\]/","<b>[$GroupName]</b>",$Groups);
            }
            else $Groups .="<br />$group";
        }
    }
   // print "             </td>\n";
    
    print "                 <td>$Organism</td>\n";
    print "                 <td>$Submitter</td>\n";
    print "                 <td>$version</td>\n";
    print "                 <td>$Groups</td>\n";
    if($Root == 1 && $Child ==0)
    {
        print "                 <td>Root</td>\n";
    }
    elseif($Root == 1 && $Child !=0)
    {
        print "                 <td>Sub_Root</td>\n";
    }
    elseif($Child !=0)
    {
        print "                 <td>Child</td>\n";
    }
    else 
    {
        print "                 <td></td>\n";
    }
    print "                 <td>$anc_action</td>\n";
    print "             </tr>\n";
}

print "         </table>\n";
print "     </div><!-- End DIV param -->\n";
print "</div><!-- End DIV rows -->\n";
print "<!-- //////////////    admin/manage_tables  //////////////  -->\n";
