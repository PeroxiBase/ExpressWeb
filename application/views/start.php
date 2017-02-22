<?php
print "<div class=\"row\">\n";
print "<div  id=\"param\" class=\"col-md-8 center-block\"> \n";

if($groups)
{
    print " You belong to this (these) group(s):<br />";
    print "<ul>";
    
    foreach($groups as $key=>$group)
    {
        if($group == "Admin")
        {
            print "<li>Admin group. MAster of the Universe !</li>";
            break;   
        }
        else
            print "<li>$group group</li>";
    }
    
    print "</ul>";
    
    print "<br /><br />";
    /*
    IdTables 	TableName 	MasterGroup 	Organism 	Submitter 	version 	group_id 	
    4 	Euca_Essai 	3 	1 	bsavelli 	1.0 	3
    */
    
    if($tables->nbr >0)
    {
        // print "res: $tables->nbr sql: $tables->sql <br />";
	//print_r($tables);
        print " You can use these data: <br />\n";
        print "<table class=\"table\">\n";
        print " <thead>\n";
        print "         <tr><th>Data</th><th>Version</th><th>Comment</th></tr>\n";
        print " </thead>\n";
        foreach($tables->result as $row)
        {
            $id= $row['IdTables'];
            $TableName =$row['TableName'];
            $Version = $row['version'];
            $comment = $row['comment'];
            print "     <tr>\n";
            print "         <td>".anchor(base_url()."display/detail/$TableName","$TableName", 'target="_blank"')."</td>\n"; 
            print "         <td>$Version</td>\n";
            print "         <td>$comment</td>\n";
            print "     </tr>\n";
        }
         print "</table>\n";
         
         print " Go to <b>Analysis</b> menu to perform an analysis.<br />";
         print " Click on Data name for more information about this data<br />";
    }
    else
    {
        print " You can access only to Demo Db ";
    }
}
else
{  
print "</div>";
print "</div>";
print "</div>";

print "<div id='section1' class='container-fluid section' >";
print "<div class='jumbotron' style='margin-top:2vh;' >";
print "<h1>Welcome to the $footer_title Website</h1>";
print"</div>";
print "<p class='lead col-md-6 col-lg-6' style='margin-top:10vh;margin-left:40vh;'>The Express Db website is an online Tool that will allow you to easily compute clustering on your expression data and provides usefull visualisation tools 
as heatmaps, graphs and networks </p>";
print "<p class='lead col-md-6 col-lg-6' style='margin-top:10vh;margin-left:40vh;'>You will also be able to load your own annotations file and do researches in the clustering results</p>";
print "<p id='arrowLabel'>see more</p>";
print "<span class='glyphicon glyphicon-circle-arrow-down' id='scrollArrow' aria-hidden='true'></span>";
print "</div>";

print "<div id='section2' class='container-fluid section' >";

print "<div class='col-md-3 col-lg-3 thumbDiv thumbnail'>";
print "<h2>Heatmaps</h2>";
print '<a id="img_heatmap" data-toggle="modal" href="#heatModal" title="Heatmap">';
print '<img src="'.base_url().'assets/img/Heatmap.png" alt="" />';
print "</a>";
print 'click to enlarge';
print "</div>";

print "<div class='col-md-3 col-lg-3 thumbDiv thumbnail'>";
print "<h2>Profiles</h2>";
print '<a id="img_profile" style="margin-top:20vh" data-toggle="modal" href="#profModal" title="Profile">';
print '<img style="margin-top:10vh" src="'.base_url().'assets/img/Profile.png" alt="" />';
print "</a>";
print 'click to enlarge';
print "</div>";

print "<div class='col-md-3 col-lg-3 thumbDiv thumbnail'>";
print "<h2>Networks</h2>";
print '<a id="img_network" style="margin-top:20vh" data-toggle="modal" href="#netModal" title="Network">';
print '<img style="margin-top:10vh" src="'.base_url().'assets/img/Network.png" alt="" />';
print "</a>";
print 'click to enlarge';
print "</div>";
print "<p style='color:#FDFFFC;margin-left:45vh;' class='lead col-md-6 col-lg-6'>Those are only images, the visualisation tools provides more dynamics graphs </p>";
print "<span class='glyphicon glyphicon-circle-arrow-down' id='scrollArrow2' aria-hidden='true'></span>";
print "</div>";
$path= base_url();
print "<div id='section3' class='container-fluid section' >";
print "<p class='lead col-md-6 col-lg-6' style='margin-top:5vh;margin-left:40vh;'>To order the gene by expression values, we compute hierarchical clustering and genes
are classified into groups</p>";
print '<img style="margin-left:34%;width:50vh;" src="'.base_url().'assets/img/tree_groups.jpg" class="img-responsive img-rounded" alt="dendogramm" />';
print "<p class='lead col-md-6 col-lg-6' style='margin-top:5vh;margin-left:40vh;'>The clustering results are used to display graphs and networks.<br>The database contains annotaions 
so it is possible to look for specific genes in <strong>your dataset</strong>.<br><br>
Want to test ? <a href='${path}auth/login'>Login</a> with Demo account !</p>";
print '<img style="margin-top:26vh;margin-left:34%;margin-bottom:2vh;width:50vh;" src="'.base_url().'assets/img/logo_LRSV.jpg" class="img-responsive img-rounded" alt="logo LRSV" />';
print "</div>";

#Modal for Heatmap #
print ' <div class="modal fade" id="heatModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
print '  <div class="modal-dialog modal-lg">';
print '    <div class="modal-content" style="text-align:center;">';
print '      <div class="modal-body" >';
print '      	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>';
print '          <h3>Heatmap</h3>';             
print '        <img src="'.base_url().'assets/img/Heatmap.png" class="imagepreview" style="width: 100%;" >';
print '      </div>';
print '    </div>';
print '  </div>';
print '</div>';

#Modal for Profile #
print ' <div class="modal fade" id="profModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
print '  <div class="modal-dialog modal-lg">';
print '    <div class="modal-content" style="text-align:center;">';
print '      <div class="modal-body" >';
print '      	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>';
print '          <h3>Profile</h3>';             
print '        <img src="'.base_url().'assets/img/Profile.png" class="imagepreview" style="width:100%;height:100%" >';
print '      </div>';
print '    </div>';
print '  </div>';
print '</div>';

#Modal for Network #
print ' <div class="modal fade" id="netModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
print '  <div class="modal-dialog modal-lg">';
print '    <div class="modal-content" style="text-align:center;">';
print '      <div class="modal-body" >';
print '      	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>';
print '          <h3>Network</h3>';             
print '        <img src="'.base_url().'assets/img/Network.png" class="imagepreview" style="width: 100%;height:100%;" >';
print '      </div>';
print '    </div>';
print '  </div>';
print '</div>';
?>
<script>
$(function(){
	$('#block1').remove()
	$('#scrollArrow').click(function(){
		$('html,body').animate({
        		scrollTop: $("#section2").offset().top},
        	'slow');
	})
	$('#scrollArrow2').click(function(){
		$('html,body').animate({
        		scrollTop: $("#section3").offset().top},
        	'slow');
	})
})
</script>
<?php
}

print " </div>";
print "</div>";

