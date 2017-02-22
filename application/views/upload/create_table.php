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
       print "$debug <br />";
      # print "Table created <br />";
      ## print "_POST<pre>".print_r($_POST,1)."</pre>"; 
        print "createtable<pre>".print_r($createtable,1)."</pre>"; 
      /*   print "insertData<pre>".wordwrap(print_r($insertData,1),100)."</pre>"; 
     //  print "max_value_col<pre>".print_r($max_value_col,1)."</pre>"; 
   */    
    /*    print " <td>is_index<pre>".print_r($is_index,1)."</pre></td>"; 
        print " <td>SqlOption<pre>".print_r($SqlOption,1)."</pre></td>"; 
        print " <td>SqlType<pre>".print_r($SqlType,1)."</pre></td>"; */
        if(isset($replicate))
        {
            print "<table border=1>\n";
            print "<tr>\n";
        print " <td>replicate_cols<pre>".print_r($replicate_cols,1)."</pre></td>";
        print " <td>replicate<pre>".print_r($replicate,1)."</pre></td>"; 
        print "</tr>\n";
        print "</table>\n";
        print "max(replicate) :".max($replicate)."<br />";
    #   asort($replicate);
#$highestValue       = end($replicate);
$max =max(array_map('strlen', $replicate));
 print "max(replicate) :".$replicate[$max]."<br />";
}
        ?>
</div>
