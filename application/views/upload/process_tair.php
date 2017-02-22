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

#$this->CI =& get_instance();
?>

<div id="left">
    <h2>process_phytozom</h2>
    <h3>Your file </h3>
    data uploaded<br />
    
    <?php echo anchor("create_table/upload_phytozom","new file");?>;
</div> 

<!--DIV RIGHT -->
<div id="right">


<?php print "'debug' => $debug,
                       'file_name' =>$file_name,
                       'Path' => $Path,
                       'file_size' =>$file_size,
                       'info' => $info,                      
                       'organism' => $organism,";
           ?>
</div>
