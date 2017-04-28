<?php
/**
* The Expression Database.
*       view corrPage.php
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage view
*/
?>
<!-- //////////////    corrPage  //////////////  -->
<?php
$this->load->view("templates/pillsCorr");
?>
<div class="row" id="resContent">
    <div id="sideBar" class="col-md-2">
        <p id="fileP">File : <b>'<?php print $filename; ?>'</b></p>
        <p id="geneP">Gene : <b>'<?php print $geneName; ?>'</b></p>
        <p>---------------------------------------</p>
        <h4>Tools</h4>
        
        <div id="seuilChooser" class=input-group>
            <label for="corS"> Change Correlation Threshold : </label>
            <input type="range" name="corrSeuil" id="corS" value="0.8" min="0" max="1" step="0.02" oninput="outputUpdate(value)" style="width:100px">
            <output for="corrSeuil" id="showRange" >0.8</output>
            <button type="button" class="btn btn-success btn-md" id="corrRun">Filter</button>
        </div>
        
        <div id="searcher" class="input-group">
            <span id="geneSearchLink" class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>
            <input type="text" id="search" class="typeahead form-control" placeholder="Search Gene by Name"/>
        </div> 
        
        <!--  Annot Search -->
        <div id="annotSel" class="form-group">
            <label for="selAnalyse">Select Analysis to test :</label>
            <select class="form-control" id="selAnalyse">
                <option id="default" value="none" selected>Choose an Analysis</option>
            </select>
        </div>
        
        <div id="annotDiv" class="input-group" style="Display:none">
            <span id="annotSearchLink" class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>
            <input type="text" id="annotSearch" class="typeahead form-control" placeholder="Search Gene by Annotation"/>
        </div>
                
    </div><!-- End DIV side-bar -->    
        
    <div id="displayDiv" class="container-fluid col-md-8">
    <div class="loader"></div> 
    
</div><!-- End DIV row -->

<script type="text/javascript">
document.onkeydown = function() 
{
    switch (event.keyCode) 
    {
        case 116 : //F5 button
            event.returnValue = false;
            event.keyCode = 0;
            return false;
        case 82 : //R button
            if (event.ctrlKey) 
            {
                event.returnValue = false;
                event.keyCode = 0;
                return false;
            }
    }
}
function outputUpdate(vol) {
     	document.querySelector('#showRange').value = vol;
}

$(function(){
	// FUNCTIONS //
	function highlightGenes(geneList){
               	$('*').removeClass('activeGene')
               	$('*').removeClass('activePanel')
	        for(var i in geneList){
                        gene=geneList[i]
              	        $('.list-group-item:contains('+gene+')').addClass('activeGene')
                }
	}
	function drawList(seuil,simList,filename){
		geneList=[]
		geneIDs=[]
		console.log(simList)
		for(i=2;i<simList.length;i++){
			if( simList[i] >= seuil){
				geneList[i-1]=simList[i]
				var id = i-1
				geneIDs.push(id)
			}	
		}
		console.log(geneIDs)
		$('#displayDiv').append('<div id=geneDiv></div>')
		$('#geneDiv').append('<ul id=geneList class="list-group"></ul>')
		$.ajax({
			url: '<?php echo base_url('display/getNamefromID'); ?>',
			type:'POST',
			data:{geneID:geneIDs,filename:filename},
			success:function(data){
				if(data)
				data=JSON.parse(data)
				tags=[]
				j=0
				for(var i in geneList){
					$('#geneList').append('<li id='+geneIDs[j]+' class="list-group-item" data-sort='+geneList[i]+'>'+data[j]+' - Correlation Score :'+geneList[i]+' </li>')	
					tags.push(data[j])
					j++
				}
				function sort_li(a, b){
	    				return ($(b).data('sort')) > ($(a).data('sort')) ? 1 : -1;    
				}
				$(".list-group-item").sort(sort_li).appendTo('#geneList');
				$( "#search" ).autocomplete({
	                		source: tags
                		});
				
				$('#geneSearchLink').click(function(){
                        		if($('#search').val() != ""){
                                		var gene=$('#search').val()
                                		if($.inArray(gene,tags) != -1 ) {
                                        		if($('#listLink').parent('li').hasClass('active')){
								genes=[]
								genes.push(gene)
                                                		highlightGenes(genes)
                                        		}
                                		}
                        		}
                		})


			}
		})
		return geneIDs
	}

	// MAIN CALL //
	var simFile='<?php echo $simFile; ?>';
	var geneID='<?php echo $geneID; ?>';
	var seuilClus='<?php echo $seuil; ?>';
	var filename='<?php echo $filename; ?>';
	$.ajax({
        	url: '<?php echo base_url('display/CSV_to_JSON'); ?>',
        	type:'POST',
	        data:{
			filename:simFile,
			geneID:geneID
		},
		success:function(data){
		if(data)
		$('.loader').fadeOut()
		simList=JSON.parse(data)
		var seuil=0.8
		geneID=drawList(seuil,simList,filename)
		// PILLS CLICKS //
		$('#corrRun').click(function(){
			seuil=parseFloat($('#corS').val())
			console.log(seuil)
			$('#geneList').remove()
			geneID=drawList(seuil,simList,filename)
		})
		$('#listLink').click(function(){
			$('.Container').remove()
			$('#searcher').fadeIn()
			$('#annotSel').fadeIn()
			drawList(seuil,simList,filename)
	                $('.nav-pills li').removeClass('active')
	                $(this).parent('li').addClass('active')
		})
		$('#heatLink').click(function(){
			$('#geneList').remove()
			$('#searcher').fadeOut()
			$('#annotSel').fadeOut()
	                $('.Container').remove()
                  	$('#displayDiv').append('<div class="Container" id="container"></div>');	
			console.log('161 corrPage drawHeatmap(geneID %s ,filename %s,seuilClus %s )',geneID,filename,seuilClus)
			
                  	drawHeatmap(geneID,filename,seuilClus)
	                $('.nav-pills li').removeClass('active')
	                $(this).parent('li').addClass('active')

		})
		$('#profLink').click(function(){
			$('#geneList').remove()
			$('#searcher').fadeOut()
			$('#annotSel').fadeOut()
	                $('.Container').remove()
                  	$('#displayDiv').append('<div class="Container" id="container"></div>');
			$('.Container').css('height','400px')
			drawProfiles(geneID,filename,seuilClus)
	                $('.nav-pills li').removeClass('active')
	                $(this).parent('li').addClass('active')

		})

		        // Add annotation analyse to select // 
        	var analyse=<?php print_r($analyse);?>;
        	for(var i in analyse){
                	$('#selAnalyse').append("<option value="+analyse[i]+">"+analyse[i]+"</option>")
       	 	}
        
		// Annotation Names //
        	$('#selAnalyse').change(function(){
                	$('.cluster-cb').prop('checked',false)
                	$('#geneNamesBox').fadeOut('slow')
	                $('#geneNamesBox').remove()
        	        $('*').removeClass('activeGene')
                	$('*').removeClass('activePanel')
	                if($(this).val() != "none"){
	                        $('#annotSearch').val("");
        	                $('#annotDiv').fadeIn('slow');
                	        var anTest=$(this).val()
	                        var file='<?php echo $filename; ?>';
        	                $.ajax({
	                                url: '<?php echo base_url('display/getSignature'); ?>',
        	                        type: 'POST',
                	                data:{ analyse:anTest,file:file },
                        	        success: function(data) {
                                	        if(data)
	                                        signatures=[]
        	                                res=(JSON.parse(data))
                	                        for(var i in res){
                        	                        if(signatures.indexOf(res[i]['Signature']) == -1){
                                	                        if(res[i]['Signature'] != undefined ){
                                                	                signatures.push(res[i]['Signature'])
                                        	                }
	                                                }
	                                        }
        	                                $('#annotSearch').autocomplete({
                	                                source:signatures
                        	                });
                                	        $('#annotSearch').attr('placeholder',signatures[0])
	                                }       	                               
        	                });
	                }
        	        else{
	                        $('#annotDiv').fadeOut('slow');
        	        }
	        })
        	

        	$('#annotSearchLink').click(function(){
                	if($('#annotSearch').val() != ""){
	                        var annot=$('#annotSearch').val()
        	                var file='<?php echo $filename; ?>';
                	        $.ajax({
                        	        url: '<?php echo base_url('display/getGenesAnnot'); ?>',
                                	type: 'POST',
	                                data:{ file:file,signature:annot },
        	                        success: function(data) {
                	                        if(data)
                        	                var genes=[]
                                	        res=(JSON.parse(data))
                                        	$('#geneNamesBox').fadeOut('slow')
	                                        $('#geneNamesBox').remove()
        	                                $('#sideBar').append('<div id=geneNamesBox></div>')
                	                        $('#geneNamesBox').append('<table class="table table-hover" id="genesTable"></table>')
                        	                $('#genesTable').append('<tr><td><b>Annotated Genes</b></td></tr>')
                                	        for(var i in res){
                                        	        if(res[i]['Gene_Name'] != undefined && $('.list-group-item:contains('+res[i]['Gene_Name']+')').length > 0){
                                                	        genes.push(res[i]['Gene_Name'])
                                                        	$('#genesTable').append('<tr><td>'+res[i]['Gene_Name']+'</td></tr>')
	                                                }
        	                                }
                	                        highlightGenes(genes)
                        	        }
	                        });     
                	}
        	});

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
	}
	})
})
</script>
<!-- //////////////    EndcorrPage  //////////////  -->
