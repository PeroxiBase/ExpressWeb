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
<div class="row">
        <div  id="param" class="col-md-8 center-block"> 
        <br />
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#tabs-1" role="tab" data-toggle="tab" aria-controls="tabs-1" >Manage Table </a></li>
                <li role="presentation"><a href="#tabs-3" role="tab" data-toggle="tab" aria-controls="tabs-3" >User Access</a></li>
                <li role="presentation"><a href="#tabs-4" role="tab" data-toggle="tab" aria-controls="tabs-4" >Convert Data</a></li>
                <li role="presentation"><a href="#tabs-5" role="tab" data-toggle="tab" aria-controls="tabs-5" >Annotations</a></li>
                <li role="presentation"><a href="#tabs-6" role="tab" data-toggle="tab" aria-controls="tabs-6" >Manage organisms</a></li>
            </ul>
            
             <div class="tab-content">
                <div class="tab-pane active" id="tabs-1">
                    <p>Manage your Tables details. Change groups and informations</p>
                    <ul>
                      <li>
                        <a href="<?php echo base_url();?>admin/manage_tables">Manage table Details</a>
                      </li>	
                      <li>
                        <a href="<?php echo base_url();?>admin/remove_tables">Remove table and dependencies </a>
                      </li>
                    </ul>
                </div>
                 
                <div class="tab-pane" id="tabs-3">
                    <p>Manage users access. Allow user to access to new Tables</p>
                    <ul>
                      <li>
                        <a href="<?php echo base_url();?>admin/manage_users">Manage users access</a>
                      </li>	
                    </ul>
                </div>
                
                <div class="tab-pane" id="tabs-4">
                    <p>Convert csv or tabulated file to SQL table</p>
                    <ul>
                      <li>
                        <a href="create_table/upload_csv" >Conversion Fichier => Table</a>
                      </li>	
                    </ul>
                </div>
                <div class="tab-pane col-sm-12" id="tabs-5">
                    <p>Annotations</p>
                    
                    <div class="  col-sm-6">Import 
                        <ul>
                          <li>
                            <a href="create_table/upload_annot" >Import annotation</a>
                          </li>
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
                    <div class="  col-sm-4">Edit
                        <ul>
                          <li>
                            <a href="create_table/edit_annot_page" >Edit annotation</a>
                          </li>
                          <li>
                            <a href="create_table/edit_phytozom" >Edit annotation from Phytozom</a>
                          </li>
                        </ul>
                      
                    </div>
                 </div>   
                <div class="tab-pane" id="tabs-6">
                    <p>Organisms</p>
                    <ul>
                      <li>
                        <a href="admin/manage_organism" >Manage organisms</a>
                      </li>	
                    </ul>
                </div>
            
            </div>
     </div>       
</div>       
