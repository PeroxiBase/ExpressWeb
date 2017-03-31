// Functions //
// Create list of cluster //
var debug =0;

function createList(url,filename,orderTable){
$.ajax({
	url:url+'display/getClusters',
        type: 'POST',
        data:{ filename:filename,orderTable:orderTable },
        success: function(data) {
		if(data)
		groups=JSON.parse(data)
		var clusters=[]
		var solo=[]
		count=0
		for(i=0;i<groups.length;i++){
			var current = groups[i]['cluster']
			if($.isArray(clusters[current])){	
				clusters[current].push(groups[i])
			}	
			else{
				clusters[current]=[groups[i]]
			}
		}
		// Create accordion //
		$('#displayDiv').append('<div class="panel-group" id="accordion"><div>')
		for(i=1;i<clusters.length;i++){
			current=clusters[i]
			// If cluster contains more than one gene , create accordion element //
			if(current.length > 1){
				count+=1
				var cluSize=current.length
				$('#accordion').append('<div class="panel panel-default" id="panel'+i+'" data-sort="'+current.length+'"></div>')
				$('#panel'+i).append('<div class="panel-heading" id="heading'+i+'"></div>')
				$('#heading'+i).append('<h4 class="panel-title" id="title'+i+'"></h4>')
				$('#title'+i).append('Cluster n°:'+i)
				$('#title'+i).append('&nbsp;- &nbsp;Size : '+cluSize+' - ')
				$('#title'+i).append('<a data-toggle="collapse" data-parent="#accordion" href="#collapse'+i+'" class="openLink"><b>Open</b></a>')
				$('#title'+i).append('<a data-toggle="collapse" data-parent="#accordion" href="#collapse'+i+'" class="closeLink" style="display:none"><b>Close</b></a>')
				$('#heading'+i).append('<input class="cluster-cb checkbox-inline" type="checkbox" id="cb'+i+'" value="'+i+'">')
				$('#panel'+i).append('<div id="collapse'+i+'" class="panel-collapse collapse">')
				for(j=0;j<current.length;j++){
					var geneID=current[j]['Gene_ID']
					var geneName=current[j]['Gene_Name']
					if(geneName.split('Eucgr.').length>1){
						geneName=geneName.split('Eucgr.')[1]
					}
					$('#collapse'+i).append('<div class="panel-body" id='+geneID+'><b>'+geneName+'</b> - <a href="http://www.uniprot.org/uniprot/?query='+geneName+'&sort=score" target="_blank">Uniprot</a> - <a href="http://www.ebi.ac.uk/ebisearch/search.ebi?query='+geneName+'&submit1=Search&db=allebi" target="_blank">EMBL-EBI</a><span class="glyphicon glyphicon-new-window geneSearch" data-toggle="tooltip" title="Go To Correlation Page"></span></div>')
				}
			}
			else{
				solo.push(current[0])	
			}
		}
		if(solo.length >0)
		{
		    // don't create a div if we don't have solo genes
                    $('#accordion').append('<div class="panel panel-default" id="panelOut" data-sort=0></div>')
                    $('#panelOut').append('<div class="panel-heading" id="headingOut"></div>')
                    $('#headingOut').append('<h4 class="panel-title" id="titleOut"></h4>')
                    $('#titleOut').append('Alone Genes ')
                    $('#titleOut').append('&nbsp;- &nbsp;Size : '+solo.length+' - ')
                    $('#titleOut').append('<a data-toggle="collapse" data-parent="#accordion" href="#collapseOut" class="openLink"><b>Open</b></a>')
                    $('#titleOut').append('<a data-toggle="collapse" data-parent="#accordion" href="#collapseOut" class="closeLink" style="display:none"><b>Close</b></a>')
                    $('#headingOut').append('<input class="cluster-cb checkbox-inline" type="checkbox" id="cbOut" value="Out">')
                    $('#panelOut').append('<div id="collapseOut" class="panel-collapse collapse">')
                    for(j=0;j<solo.length;j++){
                            var geneID=solo[j]['Gene_ID']
                            var geneName=solo[j]['Gene_Name']
                            $('#collapseOut').append('<div class="panel-body" id='+geneID+'>'+geneName+'</div>')
                    }
		}
		$('.openLink').click(function(){
			$(this).next().fadeIn()
			$(this).fadeOut()
		})	
		$('.closeLink').click(function(){
			$(this).prev().fadeIn()
			$(this).fadeOut()
		})	
		// Sort by cluster size //
		var myArray=$("#accordion .panel").toArray()
		myArray.sort(function(a,b){
			a=parseInt($(a).attr("data-sort"))
			b=parseInt($(b).attr("data-sort"))
			if(a < b) {
				return 1;
			} else if(a > b) {
				return -1; 
			} else {
				return 0;
			}    
		});
		myArray.forEach(function(element){
			element.remove()
			$('#accordion').append(element)
		});
		$('#clusCount').remove()
		$('#sideBar').prepend('<p id="clusCount">Number of Clusters : <b>'+count+'</b></p>')
		$('.loader').fadeOut('slow')
		$('.glyphicon-new-window').click(function(){
			geneID=parseInt($(this).parent('div').attr('id'));
			geneName=$(this).parent('div').text(); 
			if(debug) { console.log('geneID %s geneName %s ' ,geneID,geneName) }
			$.ajax({
				url:url+'visual/coex',
				type:'POST',
				data:{geneID:geneID,geneName:geneName},
				success:function(data){
					var win=window.open('about:blank');
					with(win.document){
                                		open();
                                        	write(data);
                                        	close();
                                 	}
				}
			})
		})

	// END SUCCESS FUNCTION //
		}		
	});
	/// END AJAX ///
}

// Get Genes Names for Autocomplete //

function getNames(data){
	names=[]
	for(i=0;i<data.length;i++){
		names.push(data[i]['Gene_Name'])
	}
	return names
}

// Find gene in clusters List //
function findGeneList(gene){
	$('*').removeClass('activeGene')
	$('*').removeClass('activePanel')
	$('.panel-body:contains('+gene+')').addClass('activeGene')
        $('.panel-body:contains('+gene+')').parent('.panel-collapse').parent('.panel').addClass('activePanel')
	if(debug) { console.log('138 findGeneList '+$('.panel-body:contains('+gene+')').parent('.panel-collapse').parent('.panel').offset().top); }
	$('#accordion').animate({
		scrollTop:$('.panel-body:contains('+gene+')').parent('.panel-collapse').parent('.panel').offset().top - 323
		},'slow');
}



// Draw heatmap with selected clusters //

function drawHeatmap(geneDict,filename,seuil){
$.ajax({
url: '../display/getClustersValues',
type: 'POST',
data:{filename:filename,geneDict:geneDict,seuil:seuil},
success:function(data){
if(data)
data=JSON.parse(data)
// data conversion //
	if(data.length == 0 ){
		$('.Container').append('<div " class="alert alert-danger"><strong>Error !</strong> Please select at least one Cluster.</div>')
		$('.alert-danger').append('<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>')	
	}
	
	else{
	var conditions=Object.keys(data[0])
	conditions.pop()
	conditions.shift()
	var genes=[];
	var values=[];
	var min=0;
	var max=0;
	for(i=0;i<data.length;i++){
        	var dict=[]
		var j=0
        	for(var k in data[i]){
                	if(k == "Gene_Name"){
                        	genes.push(data[i][k])
                	}
                	else if(k != "Gene_Name" && k != "Gene_ID"){
                        	val=parseFloat(data[i][k])
				dict=[j,i,val]
				if(val > max){ max=val }
				if(val < min){ min=val }
				j+=1
				values.push(dict)
               	 	}
        	}
	}
// Convert to SVG //
   (function (H) {
        var Series = H.Series,
            each = H.each;
        Series.prototype.getContext = function () {
            if (!this.canvas) {
                this.canvas = document.createElement('canvas');
                this.canvas.setAttribute('width', this.chart.chartWidth);
                this.canvas.setAttribute('height', this.chart.chartHeight);
                this.image = this.chart.renderer.image('', 0, 0, this.chart.chartWidth, this.chart.chartHeight).add(this.group);
                this.ctx = this.canvas.getContext('2d');
            }
            return this.ctx;
        };
        Series.prototype.canvasToSVG = function () {
            this.image.attr({ href: this.canvas.toDataURL('image/png') });
        };
        H.wrap(H.seriesTypes.heatmap.prototype, 'drawPoints', function () {

            var ctx = this.getContext();

            if (ctx) {
                each(this.points, function (point) {
                    var plotY = point.plotY,
                        shapeArgs;

                    if (plotY !== undefined && !isNaN(plotY) && point.y !== null) {
                        shapeArgs = point.shapeArgs;

                        ctx.fillStyle = point.pointAttr[''].fill;
                        ctx.fillRect(shapeArgs.x, shapeArgs.y, shapeArgs.width, shapeArgs.height);
                    }
                });

                this.canvasToSVG();

            } else {
                this.chart.showLoading('Your browser doesn\'t support HTML5 canvas, <br>please use a modern browser');

            }
        });
        H.seriesTypes.heatmap.prototype.directTouch = false; 
    }(Highcharts));

// Draw inside the container //

$('.Container').highcharts({

    chart: {
      type: 'heatmap',
      margin: [60, 20, 80, 100],
      height:800,
      backgroundColor:'rgba(0, 0, 255, 0)',
    },
    credits: {
            enabled: false
    },
    title: {
      text: 'Coexpression Heatmap',
      align: 'center',
      x: 40,
      style:{"color":'black',"fontSize":"30px"}
    },
    xAxis: {
      categories:conditions,
      labels:{
      style:{"color":'black'}
      }
    },
    yAxis: {
      categories:genes,
      labels:{
      style:{"color":'black'}
      }
    },
    legend:{
      enabled:false
    },
    colorAxis: {
        stops: [
            [0, '#62999A'],
            [0.4, '#fffbbc'],
            [0.6, '#c4463a'],
            [1, '#c4463a']
        ],
        min: min,
        max: max,
        startOnTick: false,
        endOnTick: false,
        type:'logarrithmic',
    },
    tooltip: {
            formatter: function () {
                return '<b>' + "Condition : </b>" + this.series.xAxis.categories[this.point.x] + "<br><b> Gene :</b>"+ this.series.yAxis.categories[this.point.y] + "<br><b>Value :</b>"+ this.point.value      ;
            },
      borderWidth:5,
      style:{
        "fontSize":"15px"
      }
        },

    plotOptions: {
            series: {
                turboThreshold:0,
                cursor: 'pointer',
                point: {
                    events: {
                        click: function () {
                            var g=(this.series.yAxis.categories[this.y]);
                            $('#geneselector').text(g);
                            $("input[name='geneSelector']").attr('value',g);
                        }
                    }
                }
            }
        },
      series: [{
        data:values,
        borderWidth: 0,
        nullColor: '#EFEFEF',
        cursor:'pointer',
        }]

    });
}
$('.loader').fadeOut('slow')
}
});
}

// Expression Profiles //

function drawProfiles(geneDict,filename,seuil)
{
  //  console.log("url: " +url);
  
    $.ajax({        
    url:'../display/getClustersValues',
    type: 'POST',
    data:{filename:filename,geneDict:geneDict,seuil:seuil},
    success:function(data){
    if(data)
    {
        if(debug) { console.log("329 result.js drawProfiles data : " +data);} 
        data=JSON.parse(data)
    }
    if(data.length == 0 ){
            $('.Container').append('<div " class="alert alert-danger"><strong>Error !</strong> Please select at least one Cluster.</div>')
            $('.alert-danger').append('<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>')	
    }
    else{
    var conditions=Object.keys(data[0])
    conditions.pop()
    conditions.splice(0,1)	
    var values=[]
    for(i=0;i<data.length;i++){
            var dict={}
            dict.data=[]
            for(var k in data[i]){
                    if(k == "Gene_Name"){
                            dict.name=data[i][k]
                    }
                    else if(k != "Gene_Name" && k != "Gene_ID"){
                            dict.data.push(parseFloat(data[i][k]))
                    }
            }
            values.push(dict)
    }
    
    var buttons = Highcharts.getOptions().exporting.buttons.contextButton.menuItems;
    // CONSTRUCTION DU CHART //
            $('.Container').highcharts({
            title: {
                    text: 'Coexpression profile',
                    x: -20 //center
            },  
            xAxis:{
                    categories:conditions
            },  
            yAxis: {
                    title: {
                            text: 'Value',
                    },
            },  
            plotOptions: {
                    line: {
                            events: {
                                    'click': function () {
                                    this.hide();
                                    }
                            }
                    }
            },  
            legend: {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle',
                    borderWidth: 0
            },  
            credits: {
                    enabled: false
            },  
            series:values,
            exporting: {
                    buttons: {
                            contextButton: {
                                    menuItems: buttons
                            }
                    },
                    sourceWidth: 1000,
                    sourceHeight: 500,  
                    },
    });        
    }
    $('.loader').fadeOut('slow')
    }
    });
}
// Network //

function drawNetwork(geneDict,filename,seuil,nodesFile,edgesFile){
	$('*').css('cursor','wait')
	$.getJSON(nodesFile,function(data){
		var nodes=data
		$.getJSON(edgesFile,function(data){
			var edges=data
			// original nodes and edges //
			edges = new vis.DataSet(edges);
      			nodes = new vis.DataSet(nodes);
			var n = nodes.get({ fields:['id','cluster','label','title','group'] });
			// change node shape for selected Genes //
			for(i=0;i<geneDict.length;i++){
				geneID=parseInt(geneDict[i])
				for(j=1;j<nodes.length;j++){
					node=nodes['_data'][j]
					if(node['id'] == geneID){
						node['shape']='star'
						node['size']=70
					}
				}
			}
			// Draw // 
			var container = document.getElementById('netcontainer');
			var data = {
 			        nodes: nodes,
        		        edges: edges
    			};
    			var options = {
    				autoResize:true,
    				edges:{
      					hoverWidth: function (width) {return width+5;},
					labelHighlightBold: true,
				},
				nodes:{
      					shape:'dot',
      					size:40,
      					font:{
        					color:'black'
      					},
					borderWidth:0.2,
      					borderWidthSelected:6,
   				 },
    				layout:{
        				improvedLayout:false,
   			 	},
				physics:{
        				solver: 'barnesHut',
        				stabilization: {
          					enabled:false,
          				//	iterations:1500,
        				},
        				barnesHut: {
          					gravitationalConstant: -50000,
          					springConstant: 0.01,
          					springLength: 500,
						centralGravity:0.01,
						avoidOverlap:0.6
					},
        				maxVelocity:120,
        				minVelocity:20,
     	 			},
      				interaction:{
        				hover:true,
        				hideEdgesOnDrag: true
      				}
			}
			var network = new vis.Network(container, data, options);
			if(debug) { console.log("network ok"); }
			
			var nodesList=n
			var clusList=[]
			for(var x=0;x<nodesList.length;x++){
        			clusList.push(nodesList[x]['cluster'])
			}

			Array.prototype.max = function() {
  				return Math.max.apply(null, this);
			};

			var clusMax=clusList.max()
			function clusterByCid(max) {
        			for (var i = 0; i <= max; i++) {
                			var clusterOptionsByData = {
                        			joinCondition:function(childOptions) {
                                			return childOptions.cluster == i;
                       				},
                        			processProperties: function (clusterOptions, childNodes, childEdges) {
                                		var totalMass = 0;
						var select=false
                                		for (var j = 0; j < childNodes.length; j++) {
                                        		totalMass += childNodes[j].mass;
							if(childNodes[j].shape == "star"){
								select=true
							}
                               			 }
						clusterOptions.value=totalMass*100;
						clusterOptions.scaling={min:100,max:400}
                                		clusterOptions.color =childNodes[0].color.background;
                               	 		clusterOptions.label ='Cluster n° '+i+', Size:'+ childNodes.length;
                                		clusterOptions.title ="<p style='color:black' 'font-size:10px' >Cluster n° <b> "+i+"</b><br> Size: <b>"+ childNodes.length+"</b></p>";
                                		if(select == true){ 
							clusterOptions.shape = 'star'
						}
						else{ 
							clusterOptions.shape = 'square' 
							//clusterOptions.size = 100
						}
						return clusterOptions;
                        			},
						clusterNodeProperties: {id:'Cluster' + i, borderWidth:3}
                			}
                			network.cluster(clusterOptionsByData);  
        			}
				var options = {
					offset: {
						x:0,
						y:0
					},
				        duration: 1000,
				        easingFunction: "easeInOutQuad"
			     	 };
				var fit=true	
				network.on('stabilized', function (params) {
					if(fit==true){
						network.fit({animation:options});
						$('*').css('cursor','auto')
						$('#closeCluster').css('cursor','not-allowed')
						fit=false
					}
				});
  			}

// create clusters structure //
clusterByCid(clusMax)


// open cluster on click //
network.on("selectNode", function(params){
        if (params.nodes.length == 1) {
                if (network.isCluster(params.nodes[0]) == true && params.nodes[0].split('Cluster').length > 1) {
			$('*').css('cursor','wait')
			cID=params.nodes[0]
			split=cID.split('Cluster')
			i=parseInt(split[1])
      			var clusterOptionsByData = {
				joinCondition:function(childOptions) {
					return childOptions.cluster == i;
				},
         			processProperties: function(clusterOptions, childNodes) {
            				clusterOptions.label = "[" + childNodes.length + "]";
                                	clusterOptions.title ="<p style='color:black' 'font-size:10px' >Cluster n° <b> "+i+"</b><br> Size: <b>"+ childNodes.length+"</b></p>";
                                	var totalMass = 0;
					var select=false
					color=childNodes[0].color.background
					clusterOptions.color=color
					for (var j = 0; j < childNodes.length; j++) {
                                		totalMass += childNodes[j].mass;
						if(childNodes[j].shape == "star"){
							select=true
						}
                        	       	}
					if( totalMass*10 < 500){
                                		clusterOptions.size = totalMass*10;
					}
					else{ 
						clusterOptions.size = 200 
					}
            				return clusterOptions;
         			},
          			clusterNodeProperties: {borderWidth:3, shape:'triangle'}
      			};
                        network.openCluster(params.nodes[0])
      			//network.clusterByHubsize(5, clusterOptionsByData);
			$('#closeSelect').append('<option value='+params.nodes[0]+'>Close '+params.nodes[0]+'</option>')
                }
		else if(network.isCluster(params.nodes[0]) == true && params.nodes[0].split('cluster').length > 1){
			$('*').css('cursor','wait')
			cID=params.nodes[0]
			split=cID.split('cluster:')
			i=split[1]
			if(debug) { console.log('588 result.js network.on node i'+ i); }
			network.openCluster(params.nodes[0])
			$('#closeSelect').append('<option value='+params.nodes[0]+'>Close '+params.nodes[0]+'</option>')
		}
		else if(network.isCluster(params.nodes[0]) == false){
			if(params.event)
			{
			   $(".custom-menu").finish().toggle(100).css({top: (params.event.pageY -100),left: (params.event.pageX ),display: 'inline',position: 'absolute'});
			}
			$(".custom-menu li").unbind("click") .click(function(){
			        
				switch($(this).attr("data-action")){
					case "first":
						if( params.nodes.length > 0 ){
							params.event = "[original event]";
							var geneIDs=[]
							var geneValues=[]
							nodeId=params.nodes;
							n=nodes.get([nodeId]);
							sel=network.getConnectedEdges(nodeId);
							for(i=0;i<sel.length;i++){
								c=network.getConnectedNodes(sel[i]);
								if(geneIDs.indexOf(c[0]) == -1 && typeof(c[0]) == 'number'){
									geneIDs.push(c[0])
								}
								if(geneIDs.indexOf(c[1]) == -1 && typeof(c[1]) == 'number' ){
									geneIDs.push(c[1])
								}
							}
							//if we have more than one gene in the cluster
							if(geneIDs.length==0) { geneIDs = nodeId; }
							//console.log('geneIDs 614: "'+geneIDs+'" nodes '+nodeId)
							filename=$('#fileP').text()
							filename=filename.split(" ")[2]
							seuil=$('#seuilhide').val()
							$('#dlnDIV').remove()
							$('#closeDiv').fadeOut()
							$('#netcontainer').fadeOut('slow')
							$('#selP').fadeOut()
							$('#legendDiv').fadeOut()
							$('#searcher').fadeOut()
							$('#annotSel').fadeOut()
							$('#accordion').fadeOut()
							$('.Container').remove()
							$('#displayDiv').append('<div class="Container" id="container"></div>');
							$('.Container').css('height','400px')
							//console.log('geneIDs len: "'+geneIDs.length+'"')
                                                        drawProfiles(geneIDs,filename,seuil)
                                                        $('.loader').fadeOut()
                                                        $('.Container').append('<p> You can click on a <b>line</b> to hide it. Click on <b>the gene name</b> to show it.</p>')
							$('.nav-pills li').removeClass('active')
							$('#profLink').parent('li').addClass('active')
						}
					break;
				case "second": 
					id=params.nodes[0]
					var n=nodes.get({ fields:['id','cluster','label','title','group'] });
					var node=n[id-1]
					var label=node['label']
					if(label.split('Eucgr.').length>1){
						label=label.split('Eucgr.')[1]
					}
					window.open('http://www.uniprot.org/uniprot/?query='+label+'&sort=score')
					break;
				case "third": 
					id=params.nodes[0]
					var n=nodes.get({ fields:['id','cluster','label','title','group'] });
					var node=n[id-1]
					var label=node['label']
					if(label.split('Eucgr.').length>1){
						label=label.split('Eucgr.')[1]
					}
					window.open('http://www.ebi.ac.uk/ebisearch/search.ebi?query='+label+'&submit1=Search&db=allebi')
					break;
			}
			$(".custom-menu").hide(100);
		});

	}
	var fit=true	
		network.on('stabilized', function (params) {
				if(fit==true){
				//	network.fit({animation:options});
				$('*').css('cursor','auto')
				$('#closeCluster').css('cursor','not-allowed')
				fit=false
				}
				});
}
});
$(document).bind("mousedown", function (e) {
	if (!$(e.target).parents(".custom-menu").length > 0) {
		$(".custom-menu").hide(100);
	}
});

// Close Cluster //
$('#closeCluster').prop('disabled',true)
$('#closeCluster').css('cursor','not-allowed')
$('#closeSelect').change(function(){
		if( $(this).val()==0){
		$('#closeCluster').prop('disabled',true)
		$('#closeCluster').css('cursor','not-allowed')
		}
		else{ 
		$('#closeCluster').prop('disabled',false) 
		$('#closeCluster').css('cursor','pointer')
		}
		});

$(document).on('click', '#closeCluster', function()
{ 
    cID=$('#closeSelect').val()
    if(cID!=0)
    {
        split=cID.split('Cluster')
        i=split[1]
        if( split.length <= 1 )
        {
            split=cID.split('cluster:')
            i=split[1]
        }	
        var clusterOptionsByData = {
            joinCondition:function(childOptions) 
            {
                if(debug) { console.log('713 result.js #closeCluster childOptions.cluster:'+childOptions.cluster) ; }
                return childOptions.cluster == i;
            },
            processProperties: function (clusterOptions, childNodes, childEdges)
            {
                var totalMass = 0;
                var select=false
                for (var j = 0; j < childNodes.length; j++) 
                {
                    totalMass += childNodes[j].mass;
                    if(childNodes[j].shape == "star")
                    {
                        select=true
                    }
                }
                if( totalMass*10 < 300)
                {
                        clusterOptions.size = totalMass*10;
                }
                else
                {
                        clusterOptions.size = 300;
                }
                clusterOptions.color =childNodes[0].color.background;
                clusterOptions.label ='Cluster n° '+i+', Size:'+ childNodes.length;
                clusterOptions.title ="<p style='color:black' 'font-size:10px' >Cluster n° <b> "+i+"</b><br> Size: <b>"+ childNodes.length+"</b></p>";
                if(select == true)
                { 
                        clusterOptions.shape = 'star'
                }
                else
                { 
                        clusterOptions.shape = 'square' 
                                //clusterOptions.size = 100
                }
                return clusterOptions;
            },
            clusterNodeProperties: {id:'Cluster' + i, borderWidth:3}
        }
        network.cluster(clusterOptionsByData);
        $('#closeSelect option[value="'+cID+'"]').remove();
        $('#closeCluster').prop('disabled',true);
        $('#closeCluster').css('cursor','not-allowed')
        }
}); 

// Draw profile of connected nodes //
network.on("doubleClick",function(params){
		if( params.nodes.length > 0 ){
		params.event = "[original event]";
		var geneIDs=[]
		var geneValues=[]
		nodeId=params.nodes;
		n=nodes.get([nodeId]);
		sel=network.getConnectedEdges(nodeId);
		for(i=0;i<sel.length;i++){
		c=network.getConnectedNodes(sel[i]);
		if(geneIDs.indexOf(c[0]) == -1 && typeof(c[0]) == 'number'){
		geneIDs.push(c[0])
		}
                        if(geneIDs.indexOf(c[1]) == -1 && typeof(c[1]) == 'number' ){
                                geneIDs.push(c[1])
                        }
                }
		//console.log('geneIDs 761: "'+geneIDs+'"')
		// if we have more than one gene in cluster ...
		if(geneIDs.length==0) { geneIDs = nodeId; }
		filename=$('#fileP').text()
		filename=filename.split(" ")[2]
		seuil=$('#seuilhide').val()
		//seuil=seuil.split(" ")[2]
		//seuil=seuil.replace('.','_')
		
                $('#dlnDIV').remove()
                $('#closeDiv').fadeOut()
                $('#netcontainer').fadeOut('slow')
                $('#selP').fadeOut()
                $('#legendDiv').fadeOut()
                $('#searcher').fadeOut()
                $('#annotSel').fadeOut()
                $('.loader').fadeIn('slow')
                $('#accordion').fadeOut()
                $('.Container').remove()
                $('#displayDiv').append('<div class="Container" id="container"></div>');
                $('.Container').css('height','400px')                
                //console.log('geneIDs len: "'+geneIDs.length+'"')
                drawProfiles(geneIDs,filename,seuil)
            
                $('.loader').fadeOut()
                $('.Container').append('<p> You can click on a <b>line</b> to hide it. Click on <b>the gene name</b> to show it.</p>')
                $('.nav-pills li').removeClass('active')
                $('#profLink').parent('li').addClass('active')
	}
})

function findGeneNetwork(gene){
        	var geneID=""
                nodes.forEach(function(element, index, array){
                	if(element['label'] == gene){ 
                		geneID=element['id']
                	}
                })
		var options = {
			scale:0.3,
                      	animation: {
                	       	duration: 1000,
                                easingFunction: 'linear'
                        }
		}
		if(network.findNode(geneID)[1] != undefined){
			cluster=network.findNode(geneID)[1]
			network.openCluster(cluster['id'])
		}
                network.focus(geneID,options);
}
$(document).keypress(function(e) {
	if($('#netLink').parent('li').hasClass('active')){
		if($('#search').val() != ""){
			var gene=$('#search').val()
        		if(e.which == 13 ) {
				findGeneNetwork(gene)
			}
		}
	}
})

$('#geneSearchLink').click(function(){
	if($('#netLink').parent('li').hasClass('active')){
		if($('#search').val() != ""){
			var gene=$('#search').val()
				findGeneNetwork(gene)
		}
	}
})	

$('.loader').fadeOut('slow')
////////////////////////////////////////////////////////////////////////////////////////////////////////////
		})
	})

/////////////////////////////////////////////////////////////////////////////////////////////////////////////

}



