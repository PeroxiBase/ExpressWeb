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
        <h2>Process Update annotation</h2>
        
        <?php echo anchor("create_table/update_tables_annot","Procees a new file");
         $this->session->set_userdata('updated_annotation', $table_name);
        ?>;
   </div>
   
   <div class="form-group">

<?php 
 


if(isset($info->nbr))
{
    print "<h3>Annotation for  $table_name have been created .</h3>";
    print "Records created : $info->nbr<br />";
    print "process $process";
    
}
else
{ 
    print "<pre>".print_r($info,1)."</pre>";
} 
?>
        </div>
</div>
<!-- //////////////    End upload/process_phytozom  //////////////  -->
