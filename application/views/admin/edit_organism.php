<?php
print "<div class=\"row\">\n";
print "     <div  id=\"param\" class=\"col-md-8 left-block form-horizontal\"> \n";

print "         <a href=\"".base_url()."admin/manage_organism\">back to Admin Express Db</a><br /><br />\n";

print form_open(uri_string());
print form_hidden('idOrganisms',$idOrganisms)."<br /> ";
print " <div class=\"form-group \">\n";
print form_label('Organism','Organism',array('class' =>"col-sm-2 control-label"))." ";  
print " <div class=\"col-sm-8\">\n";
print form_input('Organism' , $Organism,'required');
print "</div>\n";
print "</div>\n";
print " <div class=\"form-group\">\n";
print form_label('Max transcript lenght: ','Max_transcript_Size',array('class' =>"col-sm-2 control-label"))." ";    
print " <div class=\"col-sm-10\">\n";
print form_input('Max_transcript_size' , $Max_transcript_size,'required')."<br /> <br /> ";
print "</div>\n";
print "</div>\n";
print  form_submit('submit', "update organism info")." ";
print  form_submit('reset', "Reset")." ";
print form_close();
print "</div>\n";
print "</div>\n";
?>
