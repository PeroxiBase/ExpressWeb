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

<div id="form-group">
    <h2>Update annotation with TAIR annotation</h2>
    <legend>Enter TAIR annotation file</legend>
    Select an  annotation file to update existing annotation.<br />
    Upload size limited to 20Mo<br />
    
</div> 

<!--DIV RIGHT -->
<div class="form-group col-md-8 left-block" style="margin-left:15px;padding-left:15px;">
<!-- FORM -->
<?php #print "debug request $pid <hr />";
    print validation_errors(); 
     print " $error <br />";
    print $this->session->flashdata('message');
    print " </div>\n";
    print "<div id=\"form-group col-xs-9\">\n";
    print form_open_multipart("create_table/process_tair"); 
    print form_hidden('pid',$pid);
    print "<table class=\"table-condensed table-bordered\"   style=\"width:600px;\">\n";
    print "   <tr>\n";
    print "         <td>".form_label('Select Annotation: *','organism');
    print "         <td>".form_dropdown('organism',$organism)." &nbsp;&nbsp;";
    //print anchor("admin/add_organism","Add organism",'target="_orga"')."</td>\n";
    print "   </tr>\n";
    ?>
     <tr>
            <th>File Name </th>
            <td><input class="formInputText" id="import_file" name="import_file" type="file" required /></td>
     </tr>
     <tr>
            <th>Type of Data</th>
            <td>
                 Functional Desc   <input type="radio"  name="type_data" value='Func' />  <br />
                 GO annotation     &nbsp;&nbsp;<input type="radio"  name="type_data" value='GO' />  <br />
                 Plant Ontology assoc   <input type="radio"  name="type_data" value='PO' required/>  <br />
            </td>
     </tr>
   <?php  print "<tr><td colspan=\"2\">".form_submit( 'submit', 'Submit')." "; 
     print " ".form_reset( 'reset', 'Reset')."</td></tr>";?>
     </table>
     
     *: required!<br />
</form>
<br />
<!-- END FORM  -->
        <legend>Type of Data</legend>
        Download annotation from <a href="https://www.arabidopsis.org/download/index.jsp" target="_blank">TAIR</a>.<br />
            <ul>
                <li><a href="https://www.arabidopsis.org/download/index-auto.jsp?dir=%2Fdownload_files%2FPublic_Data_Releases" target="_blank">Functional Description</a>. In latest release.</li>
                <li><a href="https://www.arabidopsis.org/download/index-auto.jsp?dir=%2Fdownload_files%2FGO_and_PO_Annotations%2FGene_Ontology_Annotations" target="_blank">GO</a></li>
                <li><a href="https://www.arabidopsis.org/download/index-auto.jsp?dir=%2Fdownload_files%2FGO_and_PO_Annotations%2FPlant_Ontology_Annotations" target="_blank">PO</a>.</li>
             </ul>
</div>
<!--END DIV RIGHT -->
