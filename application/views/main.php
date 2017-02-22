<?php
/**
* The Expression Database.
* main view
* allow user to select and run experimental data.
* If threshold values exist, results are displayed. Otherwise, cluster job is launched
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package expressionWeb
*/
?>
<script type="text/javascript">
// Slider //
function outputUpdate1(vol) {
	document.querySelector('#showRange').value = vol;
}
function outputUpdate2(vol) {
	document.querySelector('#showCor').value = vol;
}
$(function(){
        $(document).scrollTop( $("#param").offset().top );
	$("input[name='geneSelect']").removeAttr('disabled')

// Fonction pour ajouter dynamiquement un input
        $("input[name='geneSelect']:checkbox").change(function(){
		if($(this).is(":checked")){
			$('#geneS').fadeIn('slow');
			$('#toggle1').css('background-color','lightgray');
			}
                else{
                        $('#geneS').fadeOut('slow');
                        $('#toggle1').css('background-color','white');
                }
        });
// 
	$(".filelink").on('click',function(){
		$(".filelink").css({'color':'black'});
		$(this).css({'color':'green'});
		var f=$(this).text()
		$("input[name='filprintoser']").attr('value',f);
	});
/////////////////////////////////////////////////////////////
	// AJAX LOAD TABLE VIEW //

	$('#showT').click(function(){
		var seuil=$('#cluS').val()
		var file=$('select[name="file"]').val()
		$('#block2').fadeIn('slow');
		$.ajax({
			url: '<?php print base_url('display/showTable'); ?>',
        		type: 'POST',
			data:{ seuil:seuil,file:file },
        		success: function(data) {
            			if(data)
				$("#block2").html(data);
				$('html,body').animate({
					scrollTop:$('#block2').offset().top -30
				},'slow');
        		}		
		});
	});
	$('#run').click(function(){
		$(this).prop('disabled', true);
		var file=$('select[name="file"]').val()
		var clusterSeuil=$('#cluS').val()
		 	
		$.ajax({
			url:'<?php print base_url('visual/load'); ?>',
			data:{
				file:file,
				clusterSeuil:clusterSeuil	
			},
			type:'POST',
			success:function(data){
				if(data)
				$('#block1').html(data);
				$('#block2').remove();
			}
		})	
	})
});
</script>
<div id="param" class="row">
<?php
//print_r($tables);
$attributes=array('id'=>'formParam','target'=>"_blank",'class'=>"form-horizontal");
    print form_open_multipart('visual/load',$attributes);
    
    print "  <div class=\"col-md-6 col-lg-6 col-md-offset-3 col-lg-offset-3\" id=\"formTitle\">\n";
    print "          <h2>Clustering Parameters</h2>\n";
    print "          <p> Here you can choose the table you want to work with and set the parameters for clustering.</p>\n";
    print "  </div>\n";
    print "  <div class=\"launch form-group col-md-6 col-md-offset-4 col-lg-6 col-lg-offset-4\">\n";
    
    $attLabel=array('title'=>'Choose the table you want to work with');
    print form_label('Choose a table : ','file',$attLabel);
    $options=array();
    foreach ($tables->result as $row)
    {
            $options[$row['TableName']]=$row['TableName'];
    }
    $attCtrl=array('class'=>'form-control');
    
    print form_dropdown('file',$options,'large',$attCtrl);
    
    $options=array('class'=>'btn btn-info btn-md','id'=>'showT');
    print form_button('showT','Show table',$options);
    print "  </div>\n";
    
    print "  <div class=\"launch form-group col-md-6 col-md-offset-4 col-lg-6 col-lg-offset-4\">\n";
    $options=array('id'=>'clusLabel','title'=>'Set a Threshold in order to build clusters');
    print form_label('Clustering threshold : ','cluS',$options);
?>

                <input type="range" name="clusterSeuil" id="cluS" value="0.9" min="0" max="1" step="0.02" oninput="outputUpdate1(value)" style="width:100px">
                <output for="clusterSeuil" id="showRange" >0.9</output>
         </div>
          </div>
         <div id="param" class="row">
<?php
    print "  <div class=\"launch form-group col-md-6 \">\n";
    print "          <button type=\"button\" class=\"btn btn-success btn-lg\" id=\"run\">Run</button>\n";
    print   form_hidden('pid', $pid);
    print "  </div>\n";
    print form_close();
?>	
      </div>
</div>
<?php
$path=base_url();
$userGroups=$this->session->userdata['groups'];
$userGroup=$userGroups[0];
?>
<script>
$(function(){
var userGroup='<?php print $userGroup; ?>';
if(userGroup == 'Demo'){
	console.log($('#cluS'))
	$('#cluS').attr('disabled', true);
	$('#cluS').css('cursor', 'not-allowed');
}
})
</script>

<div class="container-fluid" id='block2'></div>
