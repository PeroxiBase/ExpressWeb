<?php
/**
* The Expression Database.
*       view restricted.php
*       if users not authentificated or don't belong to good groups
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage views
*/
?>
<!-- //////////////    restricted      //////////////  --> 
<!-- Start site_content -->
      <div id="site_content">
          <h3>Restricted area</h3>
          
         Your not allowed to access this data<br />
         <?php if (! empty($message)) { ?>
                <div id="message">
                        <?php echo $message; ?>
                </div>
        <?php } ?>
        </div><!-- Start site_content -->
<!-- //////////////    End restricted      //////////////  --> 
