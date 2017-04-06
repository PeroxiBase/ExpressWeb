<?php
/**
* The Expression Database.
*  view resPage.php
*       allow user to select and run experimental data.
*       If threshold values exist, results are displayed. Otherwise, cluster job is launched
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package expressionWeb
*/
?>
<!-- //////////////    resPage      //////////////  --> 
<?php
$this->load->view("templates/pills");
?>
<div class="row" id="resContent">
    <div id="sideBar" class="col-md-3" >        <!-- //// SIDE BAR //// -->
        <p id="fileP">File : <b><?php print $filename; ?></b></p>
        <p id="seuilP">Threshold : <b><?php print "($seuil)"; ?></b></p>
        <input type="hidden" id="seuilhide" value="<?php print $seuilName; ?>">
        <h4>Tools</h4>

        <!--  Gene name search -->
        <p id="selP">Select / Unselect all Clusters<input class="checkbox-inline" type="checkbox" id="checkAll"></p>
        <div id="searcher" class="input-group">
                <span id="geneSearchLink" class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>
                <input type="text" id="search" class="typeahead form-control" placeholder="Search Gene by Name"/>
        </div>
        <br />
        
        <!--  SWITCH -->
        <div id="switchDiv" class="btn-group switch-div" data-toggle="buttons">
                <label id="swLabel1" class="btn btn-info active">
                        <input type="radio" id="switch1" name="inlineRadioOptions" class="switcher" value="annot"> Annotation
                </label>
                <label id="swLabel2" class="btn btn-info">
                        <input type="radio" id="switch2" name="inlineRadioOptions" class="switcher" value="toolbox"> Toolbox
                </label>
        </div>
        
        <!--  Annot Search -->
        <div id="annotSel" class="form-group ">
                <select class="form-control searcher" id="selAnalyse">
                        <option id="default" value="none" selected>Choose an Analysis</option>
                </select>
        </div>
        <div id="annotDiv" class="input-group" style="Display:none">
                <span id="annotSearchLink" class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>
                <input type="text" id="annotSearch" class="typeahead form-control" placeholder="Search Gene by Annotation"/>
        </div>
        
        <input type="hidden" id="netstock"/>
        
        <!-- Toolbox Search -->
        <div id="toolboxSel" class="form-group"  style="display:none">
                <select class="form-control searcher" id="selToolbox">
                        <option id="ChooseT" value="all" selected>Choose a Toolbox</option>
                </select>
        
                <select class="form-control searcher" id="selfClass">
                        <option id="ChooseF" value="none" selected>Functional Class</option>
                </select>
        
                <select class="form-control searcher" id="wpCheck">
                        <option id="ChooseP" value="all" selected>ALL Proteins</option>
                        <option value="YES">Present in WallProt DB</option>
                        <option value="NO">Absent in WallProt Db</option>
                </select>
        
                <button type="button" id="searchTB" class="btn btn-sm btn-success" style="margin-top:10px;float:right">Search</button>
        </div>
        
        <!--  Graph Legend -->
        <div id="legendDiv" style="display:none">
                <p>- Squares represent Clusters, <b>click</b> to open.</p>
                <p>- Circles represent Genes, <b>double click</b> to draw expression profiles of connected genes</p>
                <p>- Stars represent Clusters containing searched genes or gene that have been searched</p>
                <p>- You can move a node by dragging it</p>
                <p>- Colors represent low-levels clusters</p>
        </div>
        
        
            <ul class='custom-menu'>
              <li data-action = "first">Draw Expression Profiles</li>
              <li data-action = "second">Uniprot</li>
              <li data-action = "third">EMBL-EBI</li>
            </ul>
</div> <!-- END DIV  SIDE BAR //// -->

<!--  MAIN DIV -->
<div id="displayDiv" class="container-fluid col-md-8">
<?php print  $this->session->flashdata('dwnd'); ?>
    <div class="loader"></div>
    </div>
</div>

<script type="text/javascript">
var debug =0;
$(function(){

function extractValues(filename,orderName,seuil){
	geneDict=[]
	$('.cluster-cb:checked').each(function(){
		panel=$(this).parent('.panel-heading').parent('.panel')
		panel.children('.panel-collapse').children('div').each(function(){
			geneDict.push($(this).attr('id'))
		})
	})
	return geneDict
}

// Calls //
var filename='<?php echo $filename; ?>';
var orderName='<?php echo $orderName; ?>';
var seuil='<?php echo $seuil; ?>';
var url='<?php echo base_url(); ?>';
createList(url,filename,orderName)

var toolbox='';

// Links //

$('#listLink').click(function()
{
    $('#netcontainer').fadeOut('slow')
    $('#selP').fadeIn()
    $('#searcher').fadeIn()
    if( $('.switcher').val() == 'annot')
    {
        $('#annotSel').fadeIn()
        if(debug) { console.log('133 #listLink .switcher val '+ $('.switcher').val()); }
    }
    if( $('.switcher').val() == 'toolbox')
    {
        $('#toolboxSel').fadeIn()
    }
    $('#toolboxSel').fadeOut()
    $('.Container').remove()
    $('#dlnDIV').remove()
    $('#closeDiv').fadeOut()
    $('.switch-div').fadeIn()
    $('#accordion').fadeIn('slow')
    $('.nav-pills li').removeClass('active')
    $('#legendDiv').fadeOut()
    $(this).parent('li').addClass('active')
    if( $('#selAnalyse').val() != "none" )
    {
        $('#annotDiv').fadeIn()
        $('#geneNamesBox').fadeIn()
    }
});

$('#heatLink').click(function()
{
        $('#netcontainer').fadeOut('slow')
        $('#selP').fadeOut()
        $('#searcher').fadeOut()
        $('#annotSel').fadeOut()
        $('#toolboxSel').fadeOut()
        $('.switch-div').fadeOut()
        $('.loader').fadeIn('slow')
        $('#accordion').fadeOut()
        $('#dlnDIV').remove()
        $('#closeDiv').fadeOut()
        $('#legendDiv').fadeOut()
        $('.Container').remove()
        $('#displayDiv').append('<div class="Container" id="container"></div>');
        if ($('.cluster-cb:checked').length > 0 || $('.activePanel').length > 0 )
        {
            var filename='<?php echo $filename; ?>';
            var orderName='<?php echo $orderName; ?>';
            var seuil='<?php echo $seuil; ?>';
            if($('#genesTable').length > 0 && $('.cluster-cb:checked').length == 0)
            {  
                    ids=[]
                    $('#genesTable').find('td').each (function() 
                    {
                        gene=$(this).text()
                        id=$('.panel-body:contains('+gene+')').attr('id')
                        if(id != undefined)
                        {
                            ids.push(id)
                        }
                   })
                    if(debug) { console.log('187 #heatLink geneDict selected genes ids: '+ ids);}
                    geneDict=ids
            }
            else
            {
                var geneDict=extractValues(filename,orderName,seuil)
                if(debug) { console.log('193 #heatLink geneDict whole genes: '+ geneDict); }
            }
            drawHeatmap(geneDict,filename,seuil)
        }
        else
        {
            $('.loader').fadeOut('slow')
            $('.Container').append('<div " class="alert alert-danger"><strong>Error !</strong> Please select at least one Cluster.</div>')
            $('.alert-danger').append('<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>') 
        }
        $('.nav-pills li').removeClass('active')
        $(this).parent('li').addClass('active')
		
});
 
$('#netLink').click(function()
{
    $('#dlnDIV').remove()
    $('#searcher').fadeIn()
    $('#legendDiv').fadeIn()
    $('#annotSel').fadeOut()
    $('#annotDiv').fadeOut()
    $('.switch-div').fadeOut()
    $('#geneNamesBox').fadeOut()
    $('#selP').fadeOut()
    $('#toolboxSel').fadeOut()
    $('.loader').fadeIn('slow')
    $('#accordion').fadeOut()
    $('.Container').remove()
    
    $('#legendDiv').prepend('<div id="dlnDIV" class="form-group downloader"><a id="dlNetwork" download="Network.png" href="" class="btn btn-default btn-info role="button">Download PNG</a></div>')
    if( $('#closeDiv').length>0 )
    {
        $('#closeDiv').fadeIn()
    }
    else
    {
        $('#legendDiv').prepend('<div id="closeDiv" class="form-group"></div>')
        $('#closeDiv').append('<select id="closeSelect" class="form-control"><option value=0>Close a Cluster</option></select>')
        $('#closeDiv').append('<button id="closeCluster" type="button" class="btn btn-danger btn-sm" style="margin-left:0.5vh;">Close</button>')
    }
    var filename='<?php echo $filename; ?>';
    var orderName='<?php echo $orderName; ?>';
    var seuil='<?php echo $seuil; ?>';
    if($('#genesTable').length>0 )
    {  
            ids=[]
            $('#genesTable').find('td').each (function() {
                    gene=$(this).text()
                    id=$('.panel-body:contains('+gene+')').attr('id')
                    if(id != undefined){
                            ids.push(id)
                    }	
            })
            geneDict=ids
            if(debug) { console.log('248 #netLink geneDict selected genes ids: '+ geneDict); }
    }
    else
    {
            var geneDict=extractValues(filename,orderName,seuil)
            if(debug) { console.log('253 #netLink geneDict selected genes ids: '+ geneDict); }
    }
    if ( $('#netcontainer').length >0 && geneDict == $('#netstock').val() )
    {
            $('#netcontainer').fadeIn('slow')
            $('.loader').fadeOut('slow')
    }
    else
    {
            $('#netstock').val(geneDict)
            $('#netcontainer').remove()
            $('#displayDiv').append('<div class="netContainer" id="netcontainer"></div>');
            var nodesFile="<?php echo $nodesFile; ?>";
            var edgesFile="<?php echo $edgesFile; ?>";
            if(debug) { console.log('267 #netLink drawNetwork  edgesFile'+ edgesFile); }
            if(debug) { console.log('268 #netLink drawNetwork  edgesFile %s filename %s ', edgesFile,filename); }
            drawNetwork(geneDict,filename,seuil,nodesFile,edgesFile)
    }
    $('.nav-pills li').removeClass('active')
    $(this).parent('li').addClass('active')

    $('#dlNetwork').click(function(){
            canvas=$( "canvas" ).get(0).toDataURL();
            $(this).attr('href',canvas)
    })
});

$('#profLink').click(function()
{
    $('#netcontainer').fadeOut('slow')
    $('#selP').fadeOut()
    $('#searcher').fadeOut()
    $('#annotSel').fadeOut()
    $('#closeDiv').fadeOut()
    $('#toolboxSel').fadeOut()
    $('.switch-div').fadeOut()
    $('.loader').fadeIn('slow')
    $('#accordion').fadeOut()
    $('#dlnDIV').remove()
    $('.Container').remove()
    $('#legendDiv').fadeOut()
    $('#displayDiv').append('<div class="Container" id="container"></div>');
    $('.Container').css('height','400px')
    if ($('.cluster-cb:checked').length > 0 || $('.activePanel').length>0)
    {
        var filename='<?php echo $filename; ?>';
        var orderName='<?php echo $orderName; ?>';
        var seuil='<?php echo $seuil; ?>';
        if($('#genesTable').length>0 && $('.cluster-cb:checked').length == 0)
        {  
                ids=[]
                $('#genesTable').find('td').each (function() 
                {
                    gene=$(this).text()
                    id=$('.panel-body:contains('+gene+')').attr('id')
                    if(id != undefined)
                    {
                            ids.push(id)
                    }       
                })	
                geneDict=ids
        }	
        else
        {
                var geneDict=extractValues(filename,orderName,seuil)
        }

        drawProfiles(geneDict,filename,seuil)
        $('.Container').append('<p> You can click on a <b>line</b> to hide it. Click on <b>the gene name</b> to show it.</p>')
    }
    else
    {
        $('.loader').fadeOut('slow')
        $('.Container').append('<div " class="alert alert-danger"><strong>Error !</strong> Please select at least one Cluster.</div>')
        $('.alert-danger').append('<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>') 
    }
    $('.nav-pills li').removeClass('active')
    $(this).parent('li').addClass('active')
});

$('#downloadLink').click(function()
{
    $('.nav-pills li').removeClass('active')
    $(this).parent('li').addClass('active')
    $('#netcontainer').fadeOut('slow')
    $('#selP').fadeOut()
    $('.switch-div').fadeOut()
    $('#searcher').fadeOut()
    $('#annotSel').fadeOut()
    $('#toolboxSel').fadeOut()
    $('#accordion').fadeOut()
    $('#dlnDIV').remove()
    $('#legendDiv').fadeOut()
    $('#closeDiv').fadeOut()
    $('.Container').remove()
    $('#displayDiv').append('<div class="Container container-fluid row" id="container"></div>');
    $('.Container').css('height','400px')

    $('.Container').append('<div id="dlDiv" class="col-md-11 col-lg-11 col-lg-offset-1 col-md-offset-1"></div>')
    $('#dlDiv').append('<form id="dlForm" class="form-horizontal"></form>')
    $('#dlForm').append('<div class="form-group downloader"><input type="text" class="form-control" id="dlName" placeholder="File Name">.CSV</div>')
    $('#dlForm').append('<div class="form-group downloader"><button type="button" id="annotBTN" class="btn btn-info" style="margin-right:2vh;">Add Annotations</button><button type="button" id="dlBTN" class="btn btn-success">GENERATE</button><a id="dlLink" href="" class="btn btn-info btn-md" role="button">DOWNLOAD</a></div>')

    $('#dlForm').append('<div id="moreDiv" style="display:none"></div>')
    $('#moreDiv').append('<div id="addAnnot" class="col-md-5 col-lg-5 col-md-offset-1 col-lg-offset-1" style="margin-top:5vh"></div>')
    <?php if($this->ion_auth->in_group('members'))
        { ?>
    $('#moreDiv').append('<div id="addToolbox" class="col-md-5 col-lg-5" style="margin-top:5vh"></div>')
    <?php } ?>
    $('#addAnnot').append('<select class="form-control searcher" id="selAnalyse2"><option id="default" value="none" selected>Choose an Analysis</option></select>')
    $('#addAnnot').append('<textarea id="annotArea" class="form-control textareaPerso" rows="5" style="margin-top:2vh;"></textarea>')
    $('#addAnnot').append('<button type="button" id="resetAnnot" class="btn btn-danger btn-sm">RESET</button>')
    $('#annotArea').prop('disabled',true)
    
    // Add annotation analyse to select // 
    var analyse=<?php print_r($analyse);?>;
    for(var i in analyse)
    {
        if(analyse[i] !="")
        {
            $('#selAnalyse2').append("<option value="+analyse[i]+">"+analyse[i]+"</option>")
        }
    }
    
    $('#selAnalyse2').change(function()
    {
        var analyse=$(this).val()
        var areaVal=$('#annotArea').val()
        if(analyse != 'none' && areaVal.indexOf(analyse)==-1)
        {
            $('#annotArea').append(analyse+'\n')
        }
    });
    
    $('#resetAnnot').click(function()
        {
            $('#selAnalyse2').val('none')
            $('#annotArea').empty()
            $('#dlLink').fadeOut()
    });
    
    if(debug) { console.log('394 #downloadLink analyse ' + analyse); }
    <?php if($this->ion_auth->in_group('members'))
        { ?>
        $('#addToolbox').append('<select class="form-control searcher" id="selToolbox2"><option id="default" value="all" selected>Choose a Toolbox !</option></select>')
        $('#addToolbox').append('<textarea id="toolboxArea" class="form-control textareaPerso" rows="5" style="margin-top:2vh;"></textarea>')
        $('#addToolbox').append('<button type="button" id="resetToolbox" class="btn btn-danger btn-sm">RESET</button>')
        $('#toolboxArea').prop('disabled',true)
        
        $.ajax({
                url:'<?php echo base_url('toolbox/getToolboxes'); ?>',
                type:'POST',
                success:function(data){
                        if(data)
                            if(debug) { console.log('407 #downloadLink toolbox/getToolboxes data %s' + data); }
                            data=JSON.parse(data)
                            for(var i in data)
                            {
                                if(data[i]['toolbox_name']==undefined || data == "")
				{
					$('#selToolbox2').append('<option value="">There is no toolbox for this organism i '+i+'</option>')
					toolbox='';
				}
				else
				{
					toolbox=data[i]['toolbox_name']
					$('#selToolbox2').append('<option value="'+toolbox+'">'+toolbox+'</option>')
					if(debug) { console.log('420 toolbox/getToolboxes toolbox '+toolbox); }
				}                                    
                            }
                            if(toolbox =='')
                            {
                                $('#addToolbox').remove()
                            }
                }
        });
        
        $('#selToolbox2').change(function()
        {
            var analyse=$(this).val()
            var areaVal=$('#toolboxArea').val()
            if(analyse != 'all' && areaVal.indexOf(analyse)==-1)
            {
                    $('#toolboxArea').append(analyse+'\n')
            }
        });
        
        $('#resetToolbox').click(function()
        {
            $('selToolbox2').val('all')
            $('#toolboxArea').empty()
            $('#dlLink').fadeOut()	
        });
    <?php }
    else
    {
    ?>
        var toolbox = ''
         $('#toolboxArea').empty()
    <?php 
    } 
    ?>
    $('#dlForm').append('<div id="noname" class="alert alert-danger" role="alert" style="margin-top:5px;display:none" >Please give a name for your File</div>')
    
    //add annotation
    $('#dlBTN').click(function()
    {
            $('#noname').fadeOut()
            $('#dlLink').fadeOut()
            $('*').css('cursor','wait')
            filename=$('#dlName').val()
            if(filename=="")
            {
                    $('#noname').fadeIn()
            }
            else
            {
                    if(debug) { console.log('470 #downloadLink moreDiv len: ' ,$('#moreDiv').length); }
                    annot=$('#annotArea').val()
                    toolbox=$('#toolboxArea').val()
                    annot=annot.split('\n')
                    <?php if($this->ion_auth->in_group('members'))
                    { ?>
                    toolbox=toolbox.split('\n')
                    <?php } else {  ?> 
                        var toolbox = ''
                        $('#toolboxArea').empty()
                    <?php } ?>
                    if(debug) {
                        console.log('482  #downloadLink annot.length ' + annot.length);
                        console.log('483  #downloadLink annot  ' + annot );
                        console.log('484  #downloadLink toolbox  ' + toolbox );
                    }
                    if(annot.length > 1)
                    {
                            annot.pop()
                    }
                    else{ var annot=[] }
                    if(toolbox.length > 1)
                    {
                            toolbox.pop()
                    }
                    else
                    {
                        var toolbox=[] 
                    }
                    
                    $.ajax({
                            url: '<?php echo base_url('visual/download'); ?>',
                            type: 'POST',
                            data:{ 
                                    file:filename, 
                                    annot:annot,
                                    toolbox:toolbox,
                            },
                            success: function(data){
                                    if(data)
                                         if(debug) 
                                         {
                                             console.log('512 #downloadLink visual/download filename \%s\ annot \%s\ toolbox \%s\ ',filename,annot,toolbox );
                                             console.log('513 #downloadLink visual/download data %s ', data);
                                         }
                                    $('#dlLink').attr('href',data);
                                    $('#dlLink').fadeIn();
                                    $('*').css('cursor','auto')
                            }
                    });
                    
            }
    });
    
    $('#annotBTN').click(function(){
            $('#moreDiv').fadeIn()
    });

});
///////////////////////////////////FUNCTIONS///////////////////////////////////////////

        // select all checkboxes //
        $('#checkAll').change(function()
        {
            if($(this).is(':checked'))
            {
                    $('.cluster-cb').prop('checked',true)
            }
            if($(this).is(':not(:checked)'))
            {
                    $('.cluster-cb').prop('checked',false)
            }
        });
	
	// SWITCH SEARCHER // 
	$('.switcher').change(function()
        {
            sel=$(this).val()
            if( sel == "annot")
            {
                $('#toolboxSel').hide()
                $('#annotSel').fadeIn('slow')
                $('#selToolbox').val('all')
                $('.cluster-cb').prop('checked',false)
                $('#geneNamesBox').fadeOut('slow')
                $('#geneNamesBox').remove()
                $('*').removeClass('activeGene')
                $('*').removeClass('activePanel')
            }
            
            if( sel == "toolbox")
            {
                $('#annotSel').hide()
                $('#annotDiv').hide()
                $('#geneNamesBox').hide()
                $('#selAnalyse').val('none')
                $('#toolboxSel').fadeIn('slow')
                $('.cluster-cb').prop('checked',false)
                $('#geneNamesBox').fadeOut('slow')
                $('#geneNamesBox').remove()
                $('*').removeClass('activeGene')
                $('*').removeClass('activePanel')
            }
	});
		
	// Genes names //
	var filename='<?php echo $filename; ?>';
	$.ajax({
		url:'<?php echo base_url('display/getGenes'); ?>',
		type:'POST',
		data:{ filename:filename },
		success:function(data){
			if(data)
			    if(debug) { console.log('583 display/getGenes data '+data); }
			data=JSON.parse(data)
			tags=[]
			for(var i in data)
			{
				tags.push(data[i]['Gene_Name'])
			}
    			$( "#search" ).autocomplete({
      				source: tags
    			});
		}
	});

	// Add annotation analyse to select // 
	var analyse=<?php print_r($analyse);?>;
	for(var i in analyse)
	{
		$('#selAnalyse').append("<option value="+analyse[i]+">"+analyse[i]+"</option>")
	}
	// Annotation Names //
	$('#selAnalyse').change(function()
        {
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
                                    for(var i in res)
                                    {
                                            if(signatures.indexOf(res[i]['Signature']) == -1)
                                            {
                                                    if(res[i]['Signature'] != undefined )
                                                    {
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
            else
            {
                    $('#annotDiv').fadeOut('slow');
            }
	});
	
	// TOOLBOX AUTOCOMPLETE //	
	$.ajax({	        
		url:'<?php echo base_url('toolbox/getToolboxes'); ?>',
		type:'POST',
		success:function(data){
			if(data)
			data=JSON.parse(data)			
			//var toolbox='';
			for(var i in data)
			{
				if(data[i]['toolbox_name']==undefined || data == "")
				{
					$('#selToolbox').append('<option value="none">There is no toolbox for this organism i '+i+'</option>')
					toolbox='';
				}
				else
				{
					toolbox=data[i]['toolbox_name']
					$('#selToolbox').append('<option value="'+toolbox+'">'+toolbox+'</option>')
					if(debug) { console.log('665 toolbox/getToolboxes toolbox '+toolbox); }
				}
			}
			if(debug) { console.log('668 toolbox/getToolboxes data %s toolbox %s',data,toolbox); }
			// if no toolbox available (demo account) , remove toolbox button
			if (toolbox =='')
                        {
                            $('#swLabel2').remove()
                            
                        }
		}
	});
	
	$.ajax({
		url:'<?php echo base_url('toolbox/get_fClass'); ?>',
		type:'POST',
		data:{toolbox:toolbox},
		success:function(data){
			if(data)
			data=JSON.parse(data)
		            if(debug) { console.log('685 ajax toolbox/get_fClass data %s ',data); }
			for(var i in data)
			{
				if(data[i]['toolbox_name']==undefined)
				{
					$('#selfClass').append('<option value="none">There is no toolbox for this organism i '+i+'</option>')
				}
				else
				{
					fClass=data[i]['functional_class']
					$('#selfClass').append('<option value="'+fClass+'">'+fClass+'</option>')
					if(debug) { console.log('696 ajax toolbox/get_fClass fClass %s ',fClass); }
				}
			}
		}
	});

	$('#selToolbox').change(function()
        {
            var toolbox=$(this).val()
            $('#selfClass').empty()
            $('#selfClass').append('<option id="default" value="none" selected>Functional Class</option>')
            $.ajax({
                    url:'<?php echo base_url('toolbox/get_fClass'); ?>',
                    type:'POST',
                    data:{toolbox:toolbox},
                    success:function(data){
                            if(data)
                            data=JSON.parse(data)
                            if(debug) { console.log('714 #selToolbox toolbox/get_fClass data %s toolbox %s ',data,toolbox); }
                            for(var i in data)
                            {
                                    fClass=data[i]['functional_class']
                                    $('#selfClass').append('<option value="'+fClass+'">'+fClass+'</option>')
                            }
                    }
            });

	});
	/////////////////////////	

	// GENE NAME FOR TOOLBOX PARAMETERS //
	$('#searchTB').click(function()
        {
            $('*').css('cursor','wait')
            var tbName=$('#selToolbox').val()
            var fClass=$('#selfClass').val()
            var wpDB=$('#wpCheck').val()
            if(debug) { console.log("733 Toolbox : "+tbName+" - fClass : "+fClass+" - WB : "+wpDB); }
            $.ajax({
                    url:'<?php echo base_url('toolbox/get_Genes_Toolbox'); ?>',
                    type:'POST',
                    data:{
                            tbName:tbName,
                            fClass:fClass,
                            wpDB:wpDB
                    },
                    success:function(data)
                    {
                            if(data)
                            data=JSON.parse(data)
                            if(debug) { console.log('746 toolbox/get_Genes_Toolbox '+data); }
                            highlightToolbox(data)
                            $('#geneNamesBox').fadeOut('slow')
                            $('#geneNamesBox').remove()
                            $('#sideBar').append('<div id=geneNamesBox style="display:none"></div>')
                            $('#geneNamesBox').append('<table class="table table-hover" id="genesTable"></table>')
                            $('#genesTable').append('<tr><td><b>Annotated Genes</b></td></tr>')
                            for(var i in data)
                            {
                                    if(data[i]['gene_name'] != undefined)
                                    {
                                            $('#genesTable').append('<tr><td>'+data[i]['gene_name']+'</td></tr>')
                                    }
                            }
                            $('*').css('cursor','auto')
                    }
            });
	});

	//////////////////////////////////////
	$(document).keypress(function(e)
        {
            if($('#search').val() != "")
            {
                var gene=$('#search').val()
                availableTags=[]
                availableTags=$(".panel-body b").map(function() 
                {
                        return $(this).text();
                }).get();
                if(e.which == 13 && $.inArray(gene,availableTags) != -1 ) 
                {
                        if($('#listLink').parent('li').hasClass('active'))
                        {
                                findGeneList(gene);
                        }
                }
                else if(e.which == 13 && $.inArray(gene,availableTags) == -1 ) 
                {
                        $('#geneError').remove()
                        $('#sideBar').append('<div class="alert alert-danger" id="geneError"><strong> Error !</strong> Wrong Gene Name !</div>')
                        $('.alert-danger').append('<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>')   
                }
            }
	});
	
	$(document).on('click','#geneSearchLink',function()
        {
            if($('#search').val() != "")
            {
                var gene=$('#search').val()
                availableTags=[]
                availableTags=$(".panel-body b").map(function() 
                {
                        return $(this).text();
                }).get();
                if($.inArray(gene,availableTags) != -1 ) 
                {
                        if($('#listLink').parent('li').hasClass('active'))
                        {
                                findGeneList(gene);
                        }
                }
                else if($.inArray(gene,availableTags) == -1 ) 
                {
                        $('#geneError').remove()
                        $('#sideBar').append('<div class="alert alert-danger" id="geneError"><strong> Error !</strong> Wrong Gene Name !</div>')
                        $('.alert-danger').append('<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>')   
                }
            }
	});

	function highlightAnnot(geneList)
	{
            $('*').removeClass('activeGene')
            $('*').removeClass('activePanel')
            $('.annotP').remove()
            for(var i in geneList)
            {
                    gene=geneList[i]['Gene_Name']
                    if(geneList[i].hasOwnProperty('Description'))
                    {
                            annot=geneList[i]['Description']
                            $('.panel-body:contains('+gene+')').append('<p class="annotP"><b>'+annot+'</b></p>')
                    }
                    $('.panel-body:contains('+gene+')').addClass('activeGene')
                    $('.panel-body:contains('+gene+')').parent('.panel-collapse').parent('.panel').addClass('activePanel')
            }
	}
	
	function highlightToolbox(geneData)
	{
            $('*').removeClass('activeGene')
            $('*').removeClass('activePanel')
            $('.annotP').remove()
            for(var i in geneData)
            {
                    gene=geneData[i]['gene_name']
                    biol=geneData[i]['biological_activity']
                    annot=geneData[i]['annotation']
                    $('.panel-body:contains('+gene+')').addClass('activeGene')
                    $('.panel-body:contains('+gene+')').append('<p class="annotP"><b>'+annot+' - '+biol+'</b></p>')
                    $('.panel-body:contains('+gene+')').parent('.panel-collapse').parent('.panel').addClass('activePanel')
            }
	}
	

	$('#annotSearchLink').click(function()
        {
            if($('#annotSearch').val() != "")
            {
                var annot=$('#annotSearch').val()
                var file='<?php echo $filename; ?>';
                $.ajax({
                        url: '<?php echo base_url('display/getGenesAnnot'); ?>',
                        type: 'POST',
                        data:{ file:file,signature:annot },
                        success: function(data) {
                                if(data)
                                res=JSON.parse(data)
                                if(debug) { console.log('866 display/getGenesAnnot '+ res); }
                                $('#geneNamesBox').fadeOut('slow')
                                $('#geneNamesBox').remove()
                                $('#sideBar').append('<div id=geneNamesBox></div>')
                                $('#geneNamesBox').append('<table class="table table-hover" id="genesTable"></table>')
                                $('#genesTable').append('<tr><td><b>Annotated Genes</b></td></tr>')
                                for(var i in res)
                                {
                                        if(res[i]['Gene_Name'] != undefined){
                                                $('#genesTable').append('<tr><td>'+res[i]['Gene_Name']+'</td></tr>')
                                        }
                                }
                                highlightAnnot(res)
                        }
                });	
            }
	})
	
})
</script>

<!-- //////////////    End resPage      //////////////  -->
