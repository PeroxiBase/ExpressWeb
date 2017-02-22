<?php
/**
* The Expression Database.
*
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package expressionWeb
*/
?>

<div id="left">
    <h2>ERROR</h2>
    <h3>Your file </h3>
    data uploaded<br />
    
    <?php echo anchor("create_table/upload_csv","new file");?>;
</div> 

<!--DIV RIGHT -->
<div id="right">
oops

<?php print $error; ?>
</div>
