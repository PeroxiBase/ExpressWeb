<?php
/**
* The Expression Database.
*       view upload/error.php
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package expressionWeb
*@subpackage view
*/
?>
<!-- //////////////    upload/error  //////////////  -->
<div id="left">
    <h2>ERROR</h2>
    <h3>Your file </h3>
    data uploaded<br />
    
    <?php echo anchor("create_table/update_annot_page","new file"); print validation_errors(); ?>
</div> 

<!--DIV RIGHT -->
<div id="right">
oops

<?php print $error; ?>
</div>
<!-- //////////////    End upload/error  //////////////  -->
