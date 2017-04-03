<?php
/**
* The Expression Database.
*       view how_it_works.php
*       How to use ExpressWeb
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage views
*/
?>
<!-- //////////////    how_it_works     //////////////  -->
<div class="row">
    <div class="page-header col-md-6 col-lg-6 col-md-offset-3 col-lg-offset-3" style="position:fixed;text-align:center">
            <h1>How it Works</h1>
            <p>Once you have uploaded your values in our database, two scripts will process it.</p>
    </div>
    
    <div class="col-md-11 col-lg-11 col-md-offset-0 col-lg-offset-0" style="position:fixed;height:75vh;margin-top:20vh;">
            <div class="col-md-5 col-lg-5 col-md-offset-1 col-lg-offset-1 thumbnail" style="padding:1vh;font-size:16px;margin-top:5vh;text-align:center;background-color:#011627;color:#FDFFFC;height:50vh;">
                    <h3>R Script</h3>
                    <p>The aim of this script is to compute hierarchical clustering on your values in order to build genes groups.</br></br>Before compting the clustering, the values are scaled in order to have better results.</br></br>The <strong>Varclus</strong> function is used, computing <strong>Hoeffding's Distance</strong> between each genes and retrieving it into a similarity matrix.</br></br>Then the <b>Hclust</b> function computes hierarchical clustering following the<b> Ward's</b> algorithm creating a dendogramm.</br></br>This dendogramm is cut to produce groups,two types of groups are created, one with the threshold you have choosen and one with a lower threhold.</br></br>The groups definition and the scaled values are uploaded in the database and the <b>Python Script</b> can now start.<p>
            </div>
            <div class="col-md-5 col-lg-5 col-md-offset-1 col-lg-offset-1 thumbnail" style="padding:1vh;font-size:16px;margin-top:5vh;text-align:center;background-color:#011627;color:#FDFFFC;height:50vh;">
                    <h3>Python Script</h3>
                    <p>This second script will read the <b>similarity matrix</b> and the <b>groups</b> definition in order to create <b>nodes and edges</b> that will be used to build the network visualisation.</br></br>Nodes are created from genes,<b> each gene will been represented by a node.</b> Each node contains information about the gene, it's name and cluster for example.</br></br>Then the nodes are <b>connected by creating edges.</b> Inside a group ( low level clusters ) the clusters are connected by computing<b> average distance</b> between each clusters and creating an edge between a cluster and the one having the best average distance.</br></br>Inside a cluster, each gene is connected to it's<b> closest</b> according to the similarity matrix except if those two are already connected and then the gene is connected to the second closest etc.</br></br>Nodes and Edges are written in two JSON files that will be read using the <b>vis.js</b> library</p>
            </div>
    </div>
    
    <div class="col-md-12 col-lg-12 col-md-offset-0 col-lg-offset-0" style="margin-top:100vh;background-color:#011627;height:250vh;color:#FDFFFC">
            <div class="col-md-10 col-lg-10 col-md-offset-1 col-lg-offset-1" style="height:50vh">
                    <h1 style="margin-top:4vh;margin-left:35%">Computational Pipeline</h1>
                    <img src="<?php echo base_url(); ?>assets/img/Pipeline.svg" class="img-responsive" alt="Responsive image">
            </div>
            
            <div class="col-md-10 col-lg-10 col-md-offset-1 col-lg-offset-1" style="height:50vh;margin-top:60vh;">
                    <h1>User Guide</h1></br></br>
                    <ol style="font-size:20px">
                            <li>Go to Analysis Page</li>
                            <li>Choose the table you want to use</li>
                            <li>Click on 'Show Table' to see details - Unselect conditions and click on 'save' to create a new table</li>
                            <li>Set the threshold. With a Threshold close to 1 you will generate smaller and more specific clusters</br>With a threshold close to 0 you will generate big clusters, less specific.</li>
                            <li> Click on RUN and wait for calculation</li>
                            </br>
                            <li>The first result page show you the list of the created clusters. From that list, you can search ( if annotation tables are available ) by Gene Name or by annotation.</br>You can also look into the Toolboxes that have been uploaded for your organism.</li>
                            <li> You can click on a cluster name to open it and show the genes inside </li>
                            <li> By clicking on a checkbox, you can select one or many cluster and draw Heatmap and Expression profile</li>
                            <li> You can draw networks without selecting a cluster but if you do it will have a Star shape and not Square</li></br>
                            <li>In the network, Squares are clusters, dot are genes and Stars are selected Clusters/Genes</li>
                            <li>If you draw network after searching by annotation/toolbox, selected genes and clusters that contains those genes will be drawn in a Star shape</li>
                            <li>If you click on a cluster it will open and release genes. If you Double-Click on a gene you will draw expression profile of the gene and all genes that are connected with it</li></br>
                            <li>From the cluster list, you can click on the correlation icon, and open a new Page</li>
                            <li>In this page we show all genes order by hoeffding's distance with the one you have selected, a cutoff is applied, you can change it with the slider on the left</li>
                            <li>Results can be retrieved from the download section</li>
                    </ol>
            </div>
    </div>
</div><!-- End DIV row -->
<!-- //////////////    End  how_it_works     //////////////  -->
