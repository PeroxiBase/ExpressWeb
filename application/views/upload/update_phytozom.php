<?php
/**
* The Expression Database.
*       view upload/upload_phytozom.php
*       import Phytozom annotation file in Db
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package expressionWeb
*@subpackage views
*/
?>
<!-- //////////////    upload/upload_phytozom  //////////////  -->
<br />

    <a href="../create_table">back to Admin Db</a><br />
    <div id="form-group">
        <h2>Update Phytozom annotation</h2>
        <h3>Enter Phytozom annotation file</h3>
        File will be converted to mysql table<br />
        File must contains 13 fields:<br />
        #pacid  locusName  transcriptName	peptideName  Pfam  Panther  KOG  KEGG  KO  GO  Best-hit  symbol  defline<br /><br />
     </div> 
    
    <div class="label-danger col-md-8"><strong>WARNING</strong><br />
                Organism annotation table are used by Toolbox and Dataset annotation !<br />
                Don't forget to regenerate existing annotation table !  
                (<?php print anchor ("create_table/update_tables_annot","Regenerate Annotation","target='_blank'"); ?>)<br /><br />
            </div><br />
    <!--DIV RIGHT -->
    <div class="form-group" style="margin-left:15px;padding-left:15px;">
        <!-- FORM -->
        <?php #print "debug request $pid <hr />";
            print validation_errors(); 
            print $this->session->flashdata('message');
            print " </div>\n";
            print "<div id=\"form-group col-xs-9\">\n";
            print form_open_multipart("create_table/convert_phytozome_annot"); 
            print form_hidden('pid',$pid);
            
            print "<table class=\"table-condensed table-bordered\" style=\"margin:10px;border-collapse:collapse;border: solid thin; width:600px \">\n";
            print "   <tr>\n";
            print "         <td>".form_label('Select Organism: *','organism');
            print "         <td>".form_dropdown('organism',$organism,'',array('id'=>'selectOrg'))." &nbsp;&nbsp;";
            print anchor("admin/add_organism","Add organism",'target="_orga"')."</td>\n";
            print "   </tr>\n";
            ?>
             <tr>
                    <th>File Name </th>
                    <td><input class="formInputText" id="import_file" name="import_file" type="file" required /></td>
             </tr>
             <tr>
                    <th>Force update</th>
                    <td><input name="Force_Update" id="Force_Update" value='1' type="checkbox" checked="checked"/> Replace existing values</td>
             </tr>
           <?php  print "<tr><td colspan=\"2\">".form_submit( 'submit', 'Submit',array('id'=>"submitAnnot" ))." "; 
             print " ".form_reset( 'reset', 'Reset')."</td></tr>";?>
             </table>
             
             *: required!<br />
             Force update selected by default. Otherwise, data will be add at end of table. 
        </form>    <!-- END FORM  -->
    <div id="errorDiv" class="alert alert-danger row row-centered col-md-6 col-lg-6" style="display:none;margin-top:10px;margin-left:25%;">
              <strong>Oups</strong> Please select the organism and load your file.
            </div>
    </div><!--END DIV RIGHT -->
<script type="text/javascript">
$(function(){
	$('#submitAnnot').click(function(e){
		if( $('#selectOrg').val()=='0' ){
		    $('#MsgOrga').html("Select organism")
			$('#errorDiv').fadeOut()
			$('#errorDiv').fadeIn()
			e.preventDefault();
			return false
		}
	})
}) 
</script>
<!-- //////////////    End upload/upload_phytozom  //////////////  -->
