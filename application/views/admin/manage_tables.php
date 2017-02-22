<?php
#
print "<style>.mythumb{ padding:5px; border: 1px solid rgb(212, 212, 212);
box-shadow: 0px 1px 10px rgba(0, 0, 0, 0.1); 
background-color:white; width:250px; }
#sidebar{ margin-bottom:10px;} .likediv { margin-top:10px; } .affix{top:50px;}
</style>\n";
print "<div class=\"row\">\n";
print "    <div  id=\"param\" class=\"col-md-2 right-block\"> \n";
/*print "<div class=\"mythumb\" data-spy=\"affix\" data-offset-top=\"190\">\n";
print "<ul>
        <li>TableName</li><li>Organism</li><li>Submitter</li><li>version</li><li>M</li><li>Groups</li><li>Action</li>
        </ul>\n";
print "</div>\n";*/
print "</div>\n";
print "    <div  id=\"param\" class=\"col-md-8 center-block\"> \n";
print "         <a href=\"create_table\">back to Admin Express Db</a><br /><br />\n";

print "<table class=\"table table-hover table-condensed table-bordered\" >\n";
print " <thead>\n";
print "         <tr><th>TableName</th><th>Organism</th><th>Submitter</th><th>version</th><th>Groups</th><th>Action</th></tr>\n";
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
     $anc_action =anchor("admin/edit_table/$IdTable","Ed."); 
    $Groups =  "[$GroupName]";
    print "    <tr>\n";
    if($Root)
    print "            <th class=info>$TableName</th>\n";
    else
    print "            <td>$TableName</td>\n";
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
    
    print "            <td>$Organism</td>\n";
    print "            <td>$Submitter</td>\n";
    print "            <td>$version</td>\n";   
    print "            <td>$Groups</td>\n";
    print "            <td>$anc_action</td>\n";
    print "    </tr>\n";
}

print "</table>\n";
print " </div>\n";
print " </div>\n";
