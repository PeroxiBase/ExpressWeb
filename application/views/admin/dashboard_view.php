<div class="row">
	<!-- Intro Content  -->
	<div  id="param" class="col-md-8 center-block"> 	
            <h2><?php echo $this->session->username;?> dashboard</h2>
            <p>Manage your account . Create project, download, upload files</p>
	 
<?php if (! empty($message)) { ?>
        <div id="message">
                <?php echo $message; ?>
        </div>
<?php } ?>
         
            <ul  class="nav nav-tabs" role="tablist">
                <li role="presentation"  class="active"><a href="#account" role="tab" data-toggle="tab" aria-controls="account" >Account Details</a></li>
                <li role="presentation" ><a href="#email" role="tab" data-toggle="tab" aria-controls="email">Email Address</a></li>
                <li role="presentation" ><a href="#project" role="tab" data-toggle="tab" aria-controls="project" >Project</a></li>
                <li role="presentation" ><a href="#files" role="tab" data-toggle="tab" aria-controls="files" >File Managment</a></li>
            </ul>
            
            <div class="tab-content">
                <div class="tab-pane active" id="account">
              <!-- <div role="tabpanel" class="tab-pane active"  id="account">-->
                    <p>Update your account details</p>
                    <ul>
                      <li>
                        <a href="<?php echo base_url();?>auth_public/update_account">Update Account Details</a>
                      </li>	
                    </ul>
                   
                </div>
                
                <div class="tab-pane"  id="email">
                    <p>Update your email address via email verification.</p>
                    <ul>
                      <li>
                        <a href="<?php echo base_url();?>auth_public/update_email">Update Email Address via Email Verification</a>
                      </li>	
                    </ul>
                </div>
                
                 <div  class="tab-pane"  id="project">
                    <p>Create or manage Project</p>
                    <ul>
                      <li>
                        <a href="<?php echo base_url();?>auth_public/manage_project">Manage project</a>
                      </li>	
                      
                    </ul>
                </div>
                
                 <div  class="tab-pane"  id="files">
                    <p>Manage datas and generated files</p>
                    <ul>
                      <li>
                        <a href="<?php echo base_url();?>auth_public/view_project">Manage files and datas</a>
                      </li>	
                    </ul>
                </div>
          </div>
            </div> 
              </div> 