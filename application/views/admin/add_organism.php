<?php
/**
* The Expression Database.
*       view admin/add_organism.php
*       edit organism
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage view
*/
print "<!-- //////////////    admin/add_organism  //////////////  -->\n";
print "<div class=\"row\">\n";
print "     <div  id=\"param\" class=\"col-md-8 left-block form-horizontal\"> \n";

print "         <a href=\"".base_url()."admin/manage_organism\">back to Admin Express Db</a><br /><br />\n";


print $this->session->flashdata('message')."<br />";
print form_open(uri_string());
 
print "         <div class=\"form-group \">".form_label('New Organism: ','Organism',array('class' =>"col-sm-2 control-label"))."\n ";               
#print form_dropdown('Organism' ,$options_organisms, $Organism)."<br /> ";
print "                 <div class=\"col-sm-8\">".form_input('Organism' ,"",'required')."</div>\n";
print "         </div>\n";
print "         <div class=\"form-group\">".form_label('Max transcript lenght: ','Max_transcript_Size',array('class' =>"col-sm-2 control-label"))."\n";    
print "                 <div class=\"col-sm-10\">".form_input('Max_transcript_Size' , "",'required')." </div>\n";
print "         </div>\n";
print  form_submit('submit', "create organism")." \n";
print  form_submit('reset', "Reset")." \n";
print form_close();
print "    </div><!-- End DIV param -->\n";
print "</div><!-- End DIV rows -->\n";
print "<!-- //////////////    End admin/add_organism  //////////////  -->\n";
?>
