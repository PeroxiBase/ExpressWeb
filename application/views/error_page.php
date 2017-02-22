<?php

//print '<div class="row">';
echo '<div class="col-md-6  col-md-offset-3 " id="formTitle">';
print " Ooops ... we got a problem ! <br /><br />\n";
if(is_array($message))
{
    print "<p>".$message['message']."</p>\n";
}
else

print "<p>$message</p><br /><br />\n";

if(isset($ReportFile))
{
    
    $anchorFile = anchor("${Path}$ReportFile","ReportFile",'target="_blank"');
    print "<p>$anchorFile </p><br /><br />\n";
}
print "$back<br />\n";
print "</div>\n";
//print "</div>\n";
?>
