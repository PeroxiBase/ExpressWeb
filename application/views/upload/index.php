<?php
/**
* The Expression Database.
*       view upload/index.php
*       menu for annotation import
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package expressionWeb
*@subpackage views
*/
?>
<!-- //////////////    upload/index  //////////////  -->
<div class="container-fluid">
    <div  id="param" class="col-md-10 left-block"> 
    <br />
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#tabs-1" role="tab" data-toggle="tab" aria-controls="tabs-1" >Manage Table </a></li>
            <li role="presentation"><a href="#tabs-2" role="tab" data-toggle="tab" aria-controls="tabs-2" >Create DataSet</a></li>
            <li role="presentation"><a href="#tabs-3" role="tab" data-toggle="tab" aria-controls="tabs-3" >Annotations</a></li>
            <li role="presentation"><a href="#tabs-4" role="tab" data-toggle="tab" aria-controls="tabs-4" >Manage organisms</a></li>
        </ul>
        
         <div class="tab-content">
         
            <div class="tab-pane active col-sm-12" id="tabs-1">
                <p>Manage your Tables details. Change groups and informations</p>
                <div class="  col-sm-6">
                    <ul>
                      <li>
                        <a href="<?php echo base_url();?>admin/manage_tables">Manage table Details</a>
                      </li>	
                      <li>
                        <a href="<?php echo base_url();?>admin/remove_tables">Remove table and dependencies </a>
                      </li>
                    </ul>
                </div>
                <div class="  col-sm-6">
                    <ul>
                        <li>
                            <a href="create_table/update_tables_annot" >Regenerate tables annotation</a>
                        </li>
                        <?php 
                        if($this->ion_auth->get_user_id('1'))
                        { 
                            print "<li><a href=\"".base_url('admin/reset_db')."\">Reset Database</a></li>\n";
                        }
                        ?>
                     </ul>
                 </div>
            </div>
             
            <div class="tab-pane" id="tabs-2">                
                <p>Convert csv or tabulated file to SQL table</p>
                <ul>
                  <li>
                    <a href="create_table/upload_csv" >Generate DataSet</a>
                  </li>	                  
                </ul>
            </div>
            
            <div class="tab-pane col-sm-12" id="tabs-3">
                <p>Annotations</p>
                
                <div class="  col-sm-6">Import 
                    <ul>
                      <li>
                        <a href="create_table/upload_phytozom" >Import annotation from Phytozom</a>
                      </li>
                      <li>
                        <a href="create_table/create_annot" >Create annotation</a>
                      </li>	
                      <li>
                        <a href="toolbox/index">Import Toolbox</a>
                      </li>	
                    </ul>
                </div>
                
                <div class="  col-sm-6">Edit
                    <ul>
                      <li>
                        <a href="create_table/update_phytozom" >Upgrade annotation from Phytozom</a>
                      </li>
                      <li>
                        <a href="create_table/update_annot_page" >Upgrade annotation</a>
                      </li>
                    </ul>
                </div>
                
             </div>   
            
             
            <div class="tab-pane col-sm-12" id="tabs-4">
                <p>Organisms</p>
                <ul>
                  <li>
                    <a href="admin/manage_organism" >Manage organisms</a>
                  </li>	
                </ul>
            </div>
        
        </div><!-- End DIV tab-content -->
 </div><!-- End DIV param -->
</div><!-- End DIV row -->
<!-- //////////////    End upload/index  //////////////  -->
