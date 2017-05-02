<?php
/**
* The Expression Database.
*       view auth/public/dashboard.php
*       user dashboard
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage views
*/
?>

<!-- //////////////  auth/public/dashboard_view ////////////// -->

<div class="row">
    <!-- Intro Content  -->
    <div  id="param" class="col-md-8 center-block"> 	
        <h2><?php echo $this->session->username;?> dashboard</h2>
        <p>Manage your account .  download, upload files</p>
	 
<?php if (! empty($message)) { ?>
    <div id="message">
            <?php echo $message; ?>
    </div>
<?php } ?>
         
        <ul  class="nav nav-tabs" role="tablist">
            <li role="presentation"  class="active"><a href="#account" role="tab" data-toggle="tab" aria-controls="account" >Account Details</a></li>
            <li role="presentation" ><a href="#files" role="tab" data-toggle="tab" aria-controls="files">File Managment</a></li>
            <li role="presentation"><a href="#workspace" role="tab" data-toggle="tab" aria-controls="workspace" >Working space</a></li>                
        </ul>
        
        <div class="tab-content"><!-- Start DIV  tab-content: TABS definition -->
        
            <div class="tab-pane active" id="account">
                <p>Update your account details</p>
                <ul>
                  <li>
                    <a href="<?php echo base_url();?>auth_public/edit_user">Update Account Details</a>
                  </li>	
                </ul>
            </div>
            
            <div  class="tab-pane"  id="files">
                <p>Manage datas and generated files</p>
                <ul>
                  <li>
                    <a href="<?php echo base_url();?>auth_public/get_members_files">Dataset generated raw files and uploaded files</a>
                  </li>	
                </ul>
            </div>
            
            <div class="tab-pane" id="workspace">
                <p>Environment details</p>
                <?php
                
                  $username = $this->session->username;
                  $email = $this->session->email;
                  $working_path = $this->session->working_path;
                  $Directory= $working_path;
                  $groups = $this->session->groups;
                  $membership ="";
                  foreach($groups as $key=>$group)
                  {
                      $membership .= "$group, ";
                  }
                  $old_last_login  = date ("F d Y H:i:s.",$this->session->old_last_login);
                  $membership = trim($membership,',');
print "<pre>
username:       $username
email:          $email
User directory: $working_path
groups:         $membership
last_login:     $old_last_login
</pre>";
                    ?>
            </div>
                
      </div><!-- End DIV  tab-content  -->
    </div> <!-- End DIV  param  -->
 </div> <!-- End DIV  row  -->
 
<!-- //////////////  END auth/public/dashboard_view ////////////// -->

