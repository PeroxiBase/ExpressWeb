<?php
/**
* The Expression Database.
*       view error_page.php
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage views
*/
?>
<!-- //////////////    error_page      //////////////  -->
<div class="row">

        <div class="col-md-6  col-md-offset-3 " id="formTitle">
        Ooops ... we got a problem ! <br /><br /> 
        <?php
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
            print $back; ?><br />
        </div>
        
</div><!-- End DIV row --> 
<!-- //////////////    End error_page      //////////////  -->
