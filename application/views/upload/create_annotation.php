<?php
/**
* The Expression Database.
*       view upload/create_annotation.php
* create annotation for Dataset
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package expressionWeb
*@subpackage views
*/
?>
<!-- //////////////    upload/create_annotation  //////////////  -->
 
    <br />
    <a href="../create_table">back to Admin Express Db</a><br />
    <div id="form-group">
        <h2>Create annotation..</h2>
        Provide an annotation file.<br />
        File will be converted to mysql table.<br />
        File must be formated as this:<br /><br />
       
        
        <table class="table-condensed table-bordered" >
            <tr>
                    <th>GeneName</th><th>Analysis</th><th>Signature</th><th>Description</th><th>Misc</th>
            </tr>
            <tr>
                    <td>Ref12345</td><td>GO</td><td>GO:0008152</td><td>metabolic process</td><td>P</td>
            </tr>
            <tr>
                    <td>Ref12345</td><td>IPRSCAN</td><td>IPR016039</td><td>Thiolase-like</td><td></td>
            </tr>
        </table> 
        
        <div class="label-danger col-md-8"><strong>WARNING</strong><br />
            Organism annotation table are used by Toolbox and Dataset annotation !<br />
            Don't forget to regenerate existing annotation table !  
            (<?php print anchor ("create_table/update_tables_annot","Regenerate Annotation","target='_blank'"); ?>)<br /><br />
        </div><br />
    </div> 
    
    <!--DIV RIGHT -->
    <div class="form-group" style="margin-left:15px;padding-left:15px;">
            <!-- FORM -->
            <?php
            print validation_errors(); 
            print $this->session->flashdata('message');
            print "<div id=\"form-group col-xs-9\">\n";
            print form_open_multipart("create_table/load_annot",array('id' =>"edit_annot_form"));
            print "<table class=\"table-condensed  table-bordered\" style=\"margin:10px;border-collapse:collapse;border: solid thin; width:600px \">\n";
            print "   <tr>\n";
            print "         <td>".form_label('Select Organism: *','organism');
            print "         <td>".form_dropdown('selectID',$organisms,'',array('id'=>'selectOrg'))." &nbsp;&nbsp;";
            print anchor("admin/add_organism","Add organism",'target="_orga"')."</td>\n";
            print "   </tr>\n";            
            ?>
            <tr>
                    <th>File Name </th>
                    <td><input type="file" id="annotFile" name="upload_file" required></td>
             </tr>
             <tr>
                    <th>Header</th>
                    <td><input type="checkbox" id="header" name="header" value="1"> check if header</td>
             </tr>
             <tr>
                    <th>Separator</th>
                    <td><input type="radio"  name="separator" value='csv_dv' required /> ; &nbsp;&nbsp;
                    <input type="radio"  name="separator" value='tab' checked/> tabulation<br /></td>
             </tr>
             <tr>
                    <th>Force update</th>
                    <td><input name="Force_Update" id="Force_Update" value='1' type="checkbox" /> Replace existing values</td>
             </tr>
             
           <?php  print "<tr><td colspan=\"2\">".form_submit( 'submit', 'Upload Annotation',array('id'=>"submitAnnot",'class'=>"btn btn-info btn-sm"))." "; 
             print " ".form_reset( 'reset', 'Reset',array('id'=>"resetAnnot",'class'=>"btn btn-info btn-sm"))."</td></tr>";?>
             </table>
             
             *: required!<br />
             !! : annotation for this organism already exist !!. Force update selected by default. Otherwise, data will be add at end of table. 
        </form>    
            
            <div id="errorDiv" class="alert alert-danger row row-centered col-md-6 col-lg-6" style="display:none;margin-top:10px;margin-left:25%;">
                <strong>Oups</strong> Please select the organism and load your file.
            </div>
            
            <div id="successDiv" class="alert alert-success row row-centered col-md-6 col-lg-6" style="display:none;margin-top:10px;margin-left:25%;">
                <strong>Success</strong> Your annotation file have been uploaded !
            </div>
            
        </div><!-- END FORM  -->
    </div><!--END DIV RIGHT -->
</div><!--END DIV container -->

<script>
$(function(){
    $('#selectOrg').change(function(){
        var val=$(this).find("option:selected").text().match(/!!$/);
        
        if(val)
        {
            alert("Annotation for this organism already exist!!")   
            $('#Force_Update').prop('checked',true);
        }
        else
        {
            $('#Force_Update').prop('checked',false);
        }
    });
	$('#submitAnnot').click(function(e){
		if( $('#selectOrg').val() == '0' ){
		    $('#MsgOrga').html("Select organism")
                    $('#errorDiv').fadeOut()
                    $('#errorDiv').fadeIn()
                    e.preventDefault();
                    return false
		}
	})
})
</script>
<!-- //////////////    End upload/create_annotation  //////////////  -->
