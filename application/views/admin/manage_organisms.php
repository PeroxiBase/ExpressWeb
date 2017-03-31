<?php
/**
* The Expression Database.
*       view admin/manage_organisms.php
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage view
*/
print "<!-- //////////////    admin/manage_organisms  //////////////  -->\n";
print "<div class=\"row\">\n";
print "     <div  id=\"param\" class=\"col-md-10 left-block form-horizontal\"> \n";
print "         <a href=\"".base_url()."create_table\">back to Admin Express Db</a><br /><br />\n";

print "         <table class=\"tbale table-bordered table-condensed\">\n";
print "            <thead>\n";
print "                 <tr><th>Organism</th><th>Action</th></tr>\n";
$same_table = "";
#print "groups: $groups->sql <br>";
foreach($Organisms->result as $row)
{
    $idOrganisms = $row['idOrganisms'];
    $Organism = $row['Organism'];
    $anc_action =anchor("admin/edit_organism/$idOrganisms","Ed."); 
    print "                 <tr>\n";
    print "                     <td>$Organism</td>\n";
    print "                     <td>$anc_action</td>\n";
    print "                 </tr>\n";
}

print "         </table>\n";

print anchor("admin/add_organism","Add organism");

print "  </div><!-- End DIV param -->\n";
print "</div><!-- End DIV rows -->\n";
print "<!-- //////////////    End admin/manage_organisms  //////////////  -->\n";
