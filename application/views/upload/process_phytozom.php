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
<!-- //////////////    upload/process_phytozom  //////////////  -->
<div class="row col-md-8 center-block">
    <div class="form-group">
        <h2>Process Phytozom</h2>
        
        <?php echo anchor("create_table/upload_phytozom","Procees a new file");
        $this->session->set_userdata('updated_table_phyto', $table_name);
        ?>;
   </div>
   
   <div class="form-group">

<?php 

if($info=="error")
{
    print "$debug<br />";
}
else
{
    print "<h3>Your file $file_name have been processed.</h3>";
    print "File lenght: $file_size<br />";
    print "Organism: $organism<br />";
    print "SQL table: $table_name<br />";
    print "$debug<br />";
}
?>
        </div>
</div>
<!-- //////////////    End upload/process_phytozom  //////////////  -->
