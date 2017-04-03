<?php
/**
* The Expression Database.
*       view upload/import_toolbox.php
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package expressionWeb
*/
?>
<!-- //////////////    upload/import_toolbox  //////////////  -->
<div class="container-fluid">
<a href="../create_table">back to Admin Db</a><br />
    <div class="row">
        <div class="col-md-12 col-lg-12 ">
        <h2>Upload your Toolbox</h2>
        File will be converted to mysql table and will upgrade the organism Toolbox Table.
        </div> 
    </div>
    
    
    <div class="row"><!--DIV RIGHT -->
        <div id="edit_form_div" class="form-group col-md-12 col-lg-12">
            <!-- FORM -->
            <form id="edit_annot_form" class="form-inline" method="post" enctype= "multipart/form-data" action="<?php echo base_url()?>toolbox/load_toolbox" >
                <div class="form-group edit-group">
                        <select id="selectOrg" class="form-control" name="selectID"></select>	
                </div>
                <div class="form-group edit-group">
                        <input type="file" id="annotFile" name="upload_file" required>
                </div>
                 <br /><br />
                <input type="checkbox" id="header" name="header" value="1"> check if header<br />
        
                <input  type="checkbox" name="Force_Update" value="1"/> Replace existing values <strong>(Force update)</strong>  <br />
                
                <br /><br />
                <button id="submitAnnot" type="submit" class="btn btn-info btn-sm">UPLOAD</button>
            </form>
            <div class="col-md-10 col-lg-10 col-md-offset-1 col-lg-offset-1" id="tableDiv" style="margin-top:10vh">
                <p><b>Please structure your Toolbox file this way before upload</b></p>Use tabulation as separator's field. <br /><br />
                <table class="table table-hover" style="text-align:left">
                    <tr>    
                        <th>Toolbox Name</th>
                        <th>Gene Name</th>
                        <th>Annotation</th>
                        <th>Functional Class</th>
                        <th>Biological Activity</th>
                        <th>Presence in WallProt Database</th>
                    </tr>
                    <tr>
                        <td>Flavonoid biosynthesis</td>
                        <td>AT2G37040</td>
                        <td>PAL1, AtPAL1</td>
                        <td>Flavonoid metabolism</td>
                        <td>Phe ammonia lyase</td>
                        <td>NO</td>
                    </tr>
                </table>
            </div>

            <div id="errorDiv" class="alert alert-danger row row-centered col-md-6 col-lg-6" style="display:none;margin-top:10px;margin-left:25%;">
                <strong>Oups</strong> Please select the organism and load your file.
            </div>
            <div id="successDiv" class="alert alert-success row row-centered col-md-6 col-lg-6" style="display:none;margin-top:10px;margin-left:25%;">
                <strong>Success</strong> Your toolbox have been uploaded !
                
            </div>
        </div><!-- END FORM  -->      
    </div><!--END DIV RIGHT -->
</div>

<script type="text/javascript">
$(function()
    {
	var success='<?php print_r($success); ?>';
	if(success == 'success'){ $('#successDiv').fadeIn() }
	var organisms=JSON.parse('<?php print_r($organisms); ?>');
	$('#selectOrg').append('<option value=none>Choose Organism</option>')
	for (var org in organisms)
	{
		current=organisms[org]
		id=current['idOrganisms']
		name=current['Organism']
		$('#selectOrg').append('<option value='+id+'>'+name+'</option>')
	}
	$('#submitAnnot').click(function(e)
        {
            if( $('#selectOrg').val() == 'none' )
            {
                    $('#errorDiv').fadeOut()
                    $('#errorDiv').fadeIn()
                    e.preventDefault();
                    return false
            }
	})
})
</script>
<!-- //////////////    upload/import_toolbox  //////////////  -->
