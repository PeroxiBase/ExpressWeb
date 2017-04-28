<?php
/**
* The Expression Database.
*       view start.php
*       home page. Display Web info or informations for logged users
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage views
*/

print "<!-- //////////////    start      //////////////  -->\n\n";
print "     <div class=\"row\">\n";
print "         <div  id=\"param\" class=\" col-md-8 center-block\"><br /><br /> \n";

if($groups)
{
    print "             You belong to this (these) group(s):<br />\n";
    print "             <ul>\n";
    
    foreach($groups as $key=>$group)
    {
        if($group == "Admin")
        {
            print "                 <li>Admin group. Master of the Universe !</li>\n";
            break;   
        }
        else
            print "                 <li>$group group</li>\n";
    }
    
    print "             </ul>\n";
    
    print "             <br /><br />\n";
    
    
    if($tables->nbr >0)
    {
        // print "res: $tables->nbr sql: $tables->sql <br />";
	//print_r($tables);
        print "             You can use these data: <br />\n";
        print "             <table class=\"table\">\n";
        print "                 <thead>\n";
        print "                     <tr><th>Data</th><th>Version</th><th>Comment</th></tr>\n";
        print "                 </thead>\n";
        print "                 <tbody>\n";
        foreach($tables->result as $row)
        {
            $id= $row['IdTables'];
            $TableName =$row['TableName'];
            $Version = $row['version'];
            $comment = $row['comment'];
            print "                     <tr>\n";
            print "                         <td>".anchor(base_url()."display/detail/$TableName","$TableName", 'target="_blank"')."</td>\n"; 
            print "                         <td>$Version</td>\n";
            print "                         <td>$comment</td>\n";
            print "                     </tr>\n";
        }
        print "                 <tbody>\n";
        print "             </table>\n";
         
         print "             Go to <b>Analysis</b> menu to perform an analysis.<br />\n";
         print "             Click on Data name for more information about this data<br />\n";
    }
    else
    {
        print "             No Dataset are available \n";
    }
    
    print "         </div> <!-- End DIV param -->\n";
    print "     </div> <!-- End DIV row -->\n";
}
else
{
     ?>
     <!-- //////////////    start      //////////////  -->
     
         </div> <!-- End DIV param -->\n
       </div><!-- End DIV row -->\n
    </div><!-- End DIV block1 -->\n
    
    <div id="section1" class="container-fluid section" >
        <div class="jumbotron" style="margin-top:2vh;" >
            <h2>Welcome to the <?php print $footer_title; ?> Website</h2>
        </div>
        
        <p class="lead col-md-6 col-lg-6" style="margin-top:10vh;margin-left:40vh;">
        The Express Db website is an online Tool that will allow you to easily compute 
        clustering on your expression data and provides usefull visualisation tools 
        as heatmaps, graphs and networks </p>
        <p class="lead col-md-6 col-lg-6" style="margin-top:10vh;margin-left:40vh;">
        You will also be able to load your own annotations file and do researches in the
        clustering results</p>
        <p id="arrowLabel">see more</p>
        <span class="glyphicon glyphicon-circle-arrow-down" id="scrollArrow" aria-hidden="true"></span>
    </div> <!-- End DIV section1 -->
        
    <div id="section2" class="container-fluid section" >
    
        <div class="col-md-3 col-lg-3 thumbDiv thumbnail">
            <h2>Heatmaps</h2>
            <a id="img_heatmap" data-toggle="modal" href="#heatModal" title="Heatmap"><img src="<?php print base_url(); ?>assets/img/Heatmap.png" alt="Heatmap" /></a>
            click to enlarge
        </div>
        
        <div class="col-md-3 col-lg-3 thumbDiv thumbnail">
            <h2>Profiles</h2>
            <a id="img_profile" style="margin-top:20vh" data-toggle="modal" href="#profModal" title="Profile">
            <img style="margin-top:10vh" src="<?php print base_url(); ?>assets/img/Profile.png" alt="Profile" />
            </a>
            click to enlarge
        </div>
        
        <div class="col-md-3 col-lg-3 thumbDiv thumbnail">
            <h2>Networks</h2>
            <a id="img_network" style="margin-top:20vh" data-toggle="modal" href="#netModal" title="Network">
            <img style="margin-top:10vh" src="<?php print base_url(); ?>assets/img/Network.png" alt="Network" />
            </a>
            click to enlarge
        </div>
        
        <p style="color:#FDFFFC;margin-left:45vh;" class="lead col-md-6 col-lg-6">
        Those are only images, the visualisation tools provides more dynamics graphs </p>
        <span class="glyphicon glyphicon-circle-arrow-down" id="scrollArrow2" aria-hidden="true"></span>
        
    </div><!-- End DIV section2 -->
    
    
    <div id="section3" class="container-fluid section" >
        <p class="lead col-md-6 col-lg-6" style="margin-top:5vh;margin-left:40vh;">To order the gene by expression values, we compute hierarchical clustering and genes are classified into groups
        <img style="margin-left:30%;width:40vh;" src="<?php print base_url(); ?>assets/img/tree_groups.jpg" class="img-responsive img-rounded" alt="dendogramm" /></p>
        <p class="lead col-md-6 col-lg-6" style="margin-top:5vh;margin-left:40vh;">
            The clustering results are used to display graphs and networks.<br />The database contains annotaions 
            so it is possible to look for specific genes in <strong>your dataset</strong>.<br />
            <br />
            Want to test ? <a href="<?php print base_url(); ?>auth/login">Login</a> with Demo account !<br />
            
        <img style="margin-top:5vh;margin-left:30%;margin-bottom:2vh;width:25vh;" src="<?php print base_url(); ?>assets/img/logo_LRSV.jpg" class="img-responsive img-rounded" alt="logo LRSV" /></p>
       
    </div><!-- End DIV section2 -->
    
    
    
    <!-- Modal for Heatmap # -->
     <div class="modal fade" id="heatModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content" style="text-align:center;">
          <div class="modal-body" >
          	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
              <h3>Heatmap</h3>             
            <img src="<?php print base_url(); ?>assets/img/Heatmap.png" class="imagepreview" style="width: 100%;" alt="Heatmap" />
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modal for Profile # -->
     <div class="modal fade" id="profModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content" style="text-align:center;">
          <div class="modal-body" >
          	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
              <h3>Profile</h3>             
            <img src="<?php print base_url(); ?>assets/img/Profile.png" class="imagepreview" style="width:100%;height:100%" alt="Profile" />
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modal for Network -->
     <div class="modal fade" id="netModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content" style="text-align:center;">
          <div class="modal-body" >
          	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
              <h3>Network</h3>             
            <img src="<?php print base_url(); ?>assets/img/Network.png" class="imagepreview" style="width: 100%;height:100%;" alt="Network" />
          </div>
        </div><!-- End DIV section3 -->
      </div> <!-- End DIV param -->
   
      
 
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

print "<!-- //////////////    End start      //////////////  -->\n\n";

