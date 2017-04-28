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
<!-- //////////////    upload/create_table  //////////////  -->
<div id="left">
    <h2>Table generation</h2>
    <h3>Your SQL table <?php $tableSql; ?></h3>
    data uploaded<br />
    
    <?php 
    echo anchor("create_table/upload_csv","new file");
    $this->session->set_userdata('updated_table', $tableSql);
    ?>;
</div> 

<!--DIV RIGHT -->
<div id="right">
        <?php 
        #print "$debug <br />";
        print "createtable<pre>".print_r($createtable,1)."</pre>"; 
        if(isset($replicate))
        {
            print "<table border=1>\n";
            print "<tr>\n";
        print " <td>replicate_cols<pre>".print_r($replicate_cols,1)."</pre></td>";
        print " <td>replicate<pre>".print_r($replicate,1)."</pre></td>"; 
        print "</tr>\n";
        print "</table>\n";
        print "max(replicate) :".max($replicate)."<br />";
        
$max =max(array_map('strlen', $replicate));
 print "max(replicate) :".$replicate[$max]."<br />";
}
        ?>
</div>
<!-- //////////////    upload/create_table  //////////////  -->
