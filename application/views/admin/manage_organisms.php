<?php
print "<div class=\"row\">\n";
print "     <div  id=\"param\" class=\"col-md-10 left-block form-horizontal\"> \n";
print "         <a href=\"".base_url()."create_table\">back to Admin Express Db</a><br /><br />\n";

print "<table class=\"tbale table-bordered table-condensed\">\n";
print " <thead>\n";
print "         <tr><th>Organism</th><th>Action</th></tr>\n";
$same_table = "";
#print "groups: $groups->sql <br>";
foreach($Organisms->result as $row)
{
    $idOrganisms = $row['idOrganisms'];
    $Organism = $row['Organism'];
     $anc_action =anchor("admin/edit_organism/$idOrganisms","Ed."); 
    print "    <tr>\n";
    print "            <td>$Organism</td>\n";
    print "            <td>$anc_action</td>\n";
    print "    </tr>\n";
}

print "</table>\n";

print anchor("admin/add_organism","Add organism");

print "  </div>\n";
print "</div>\n";
