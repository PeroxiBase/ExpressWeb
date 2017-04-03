<?php
/**
* The Expression Database.
*       view admin/edit_organisms.php
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage view
*/
print "<!-- //////////////    admin/edit_organisms  //////////////  -->\n";
print "<div class=\"row\">\n";
print "     <div  id=\"param\" class=\"col-md-8 left-block form-horizontal\"> \n";

print "         <a href=\"".base_url()."admin/manage_organism\">back to Manage organisms</a><br /><br />\n";

print form_open(uri_string());
print form_hidden('idOrganisms',$idOrganisms)."<br />\n ";
print "         <div class=\"form-group \">\n";
print form_label('Organism','Organism',array('class' =>"col-sm-2 control-label"))."\n ";  
print "                 <div class=\"col-sm-8\">\n";
print form_input('Organism' , $Organism,'required')."\n";
print "                 </div>\n";
print "         </div>\n";
print "         <div class=\"form-group\">\n";
print form_label('Max transcript lenght: ','Max_transcript_Size',array('class' =>"col-sm-2 control-label"))." \n";    
print "                 <div class=\"col-sm-10\">\n";
print form_input('Max_transcript_size' , $Max_transcript_size,'required')."<br /> <br />\n ";
print "                 </div>\n";
print "         </div>\n";
print  form_submit('submit', "update organism info")." \n";
print  form_submit('reset', "Reset")."\n ";
print form_close();
print "     </div><!-- End DIV param -->\n";
print "</div><!-- End DIV rows -->\n";
print "<!-- //////////////    End admin/edit_organisms  //////////////  -->\n"; 
