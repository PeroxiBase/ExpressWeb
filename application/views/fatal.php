<?php
/**
* The Expression Database.
*       view fatal.php
*       Used by CTRL Visual. fatal error on clustering
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage 
*/
?>
<!-- //////////////    fatal     //////////////  -->
<div id="error" class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-lg-12"> 
            <h1><span class="glyphicon glyphicon-exclamation-sign" style="color:red"></span>
            <?php print $this->session->flashdata('message')."<br />\n"; ?>
            
        </div>
        
    </div><br /><?php print $back; ?>
</div>
<!-- //////////////    End fatal     //////////////  -->