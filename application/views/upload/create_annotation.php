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
<div class="container-fluid">
  <div class="row">
  <div class="col-md-12 col-lg-12 ">
    <h2>Create annotation</h2>
    Provide an annotation file.<br />
    File will be converted to mysql table.<br />
    File must be formated as this:<br /><br />
    
    <table class="tablename table-bordered" >
        <tr>
            <th>GeneName</th><th>Analyse</th><th>Signature</th><th>Description</th><th>Misc</th>
        </tr>
        <tr>
            <td>Ref12345</td><td>GO</td><td>GO:0008152</td><td>metabolic process</td><td>P</td>
        </tr>
        <tr>
            <td>Ref12345</td><td>IPRSCAN</td><td>IPR016039</td><td>Thiolase-like</td><td></td>
        </tr>
        </table>
  </div> 
  </div>
  <!--DIV RIGHT -->
  <div class="row row-centered">
      <div id="edit_form_div" class="form-group col-md-12 col-lg-12">
        <!-- FORM -->
        <form id="edit_annot_form" class="form-inline" method="post" enctype= "multipart/form-data" action="<?php echo base_url()?>create_table/load_annot" >
          <div class="form-group edit-group">
            <select id="selectOrg" class="form-control" name="selectID"></select>	
          </div>
          <div class="form-group edit-group">
            <input type="file" id="annotFile" name="upload_file">
          </div>
           
          <button id="submitAnnot" type="submit" class="btn btn-info btn-sm">Upload Annotation</button>
          <br />
          <div class="form-group edit-group">
          <label>Header</label>  <input type="checkbox" id="header" name="header" value="1"> check if header<br />
           <label>Separator</label>  <input type="radio"  name="separator" value='csv_dv' required /> ; &nbsp;&nbsp;
            <input type="radio"  name="separator" value='tab' checked/> tabulation<br />

          </div>
        </form>
      <div id="errorDiv" class="alert alert-danger row row-centered col-md-6 col-lg-6" style="display:none;margin-top:10px;margin-left:25%;">
          <strong>Oups</strong> Please select the organism and load your file.
      </div>
      <div id="successDiv" class="alert alert-success row row-centered col-md-6 col-lg-6" style="display:none;margin-top:10px;margin-left:25%;">
          <strong>Success</strong> Your annotation file have been uploaded !
      </div>
    <!-- END FORM  -->
      </div>
  </div>
<!--END DIV RIGHT -->
</div>
<script>
$(function(){
	var success='<?php print_r($success); ?>';
	if(success == 'success'){ $('#successDiv').fadeIn() }
	var organisms=JSON.parse('<?php print_r($organisms); ?>');
	$('#selectOrg').append('<option value=none>Choose Organism</option>')
	for (var org in organisms){
		current=organisms[org]
		id=current['idOrganisms']
		name=current['Organism']
		$('#selectOrg').append('<option value='+id+'>'+name+'</option>')
	}
	$('#submitAnnot').click(function(e){
		if( $('#selectOrg').val() == 'none' ){
			$('#errorDiv').fadeOut()
			$('#errorDiv').fadeIn()
			e.preventDefault();
			return false
		}
	})
})
</script>
