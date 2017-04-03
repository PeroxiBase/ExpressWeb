<?php
/**
* The Expression Database.
*       view upload/upload_form.php
*
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package expressionWeb
*@subpackage view
*/
?>
<!-- //////////////    upload/upload_form  //////////////  -->
<div id="form-group">
    <h2>Convert file to Table</h2>
    <h3>Enter your file</h3>
    Submit csv or tab file to convert to mysql
</div> 

<!--DIV RIGHT -->
<div class="form-group">
<!-- FORM -->
<?php #print "debug request $pid <hr />";
    print validation_errors(); 
   #  print "error: ".print_r($master_group,1)."<br />";
    print $this->session->flashdata('message');
    print " </div>\n";
    print "<div class=\"form-group col-xs-9\">\n";
    print form_open_multipart("create_table/load_csv"); 
    print form_hidden('pid',$pid);
    
    print "<table class=\"table-condensed table-bordered\" style=\"width:600px;\" >\n";
    print "   <tr>\n";
    print "         <td>".form_label('Select master group: *','master_group')."</td>\n";
    print "         <td>$master_group ";
    print anchor("auth/create_group","Add group",'target="_group"')."</td>\n";
    print "   </tr>\n";
    print "   <tr>\n";
    print "         <td>".form_label('Select Organism: *','organism');
    print "         <td>$organism";
    print anchor("admin/add_organism","Add organism",'target="_orga"')."</td>\n";
    print "   </tr>\n";
    ?>
     <tr>
            <th>File Name </th>
            <td><input class="formInputText" id="import_file" name="import_file" type="file" required /></td>
     </tr>
     <tr>
            <th>Header </th>
            <td><input type="checkbox" id="fheader"  name="header" value='1' checked/> Check if file contains Header</td>
     </tr>
    <!-- <div class="nlines_header"><input type="input"  name="nlines_header" value='1' /> nbr lines in header </div> -->
     <tr>
            <th>Separator</th>
            <td>
                 Choose separator used between columns:<br />
                 <input type="radio"  name="separator" value='csv_dv' required /> ; &nbsp;&nbsp;
                 <input type="radio"  name="separator" value='csv_v' /> , &nbsp;&nbsp;
                 <input type="radio"  name="separator" value='tab' checked/> tabulation<br />
                 <input type="radio" id='other' name="separator" value='other ' /> other : 
                 <input type="input" title="Only one character! "id="separator_char" name="separator_char" value=''  maxlength="1" size="1"/>
            </td>
      </tr>
     <tr>
            <th>Post Processing</th>
            <td>
                 Define replicates   <input type="radio"  name="post_process" value='replicate' required/>  <br />
                 Define conditions   <input type="radio"  name="post_process" value='condition' />  <br />
                 None   <input type="radio"  name="post_process" value='none' checked />  <br />
            </td>
     </tr>
     <tr>
            <th>Type of Data</th>
            <td>
                 RPKM   <input type="radio"  name="type_data" value='RPKM' required/>  <br />
                 Fold Change   <input type="radio"  name="type_data" value='Fold_Change' />  <br />
                 Log     &nbsp;&nbsp;<input type="radio"  name="type_data" value='LOG' />  <br />
                 Normal distribution     <input type="radio"  name="type_data" value='STD' />  <br />
                 Raw data     &nbsp;&nbsp;<input type="radio"  name="type_data" value='RAW' /> 
            </td>
     </tr>
     <tr>
            <th>Limit (ex:for demo) </th>
            <td>Keep only first <input type="input" id="flimit"  name="limit" value='' size=5 /> lines</td>
     </tr>
   <?php  print "<tr><td colspan=\"2\">".form_submit( 'submit', 'Submit')." "; 
     print " ".form_reset( 'reset', 'Reset')."</td></tr>";?>
     </table>
     
     *: required!<br />
</form>
<!-- END FORM  -->

</div><!--END DIV RIGHT -->
<!-- //////////////    upload/upload_form  //////////////  -->
