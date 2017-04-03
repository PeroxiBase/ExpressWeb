<?php
/**
* The Expression Database.
*       view auth/public/project_detail.view
*       display files results and personnal directory contents
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage view
*/
?>
<!-- //////////////    auth/admin/project_detail      //////////////  -->
    <div id="row">
			
	<!-- Main Content -->
        <h2>Project Information</h2>
        <a href="<?php echo base_url();?>auth_public/update_account">Manage <i><?php print $username; ?></i> Project</a><br /><br />

<?php if (! empty($message)) { ?>
        <div id="message">
                <?php echo $message; ?>
        </div>
<?php } 
$Directory = $this->session->working_path;
//var_dump($project);
?>
      <!--  <div id="tabs"> -->
            <ul  class="nav nav-tabs" role="tablist">
                <li role="presentation" active ><a href="#file" role="tab" data-toggle="tab" aria-controls="file" >Datasets files</a></li>
                <li role="presentation" ><a href="#users_file" role="tab" data-toggle="tab" aria-controls="users_file" >User files</a></li>
            </ul>
            
          <div class="tab-content">               
             
                <div class="tab-pane active" id="file">
                       Raw files availables<br />   
                <?php
                   
                     print $File_list;
                       ?>               
                </div>
                
                <div class="tab-pane" id="users_file">
                       Files availables in your personal directory <?php print $Directory; ?><br /><br />   
                <?php
                 
function createMenu($dir) 
{
    if(is_dir($dir)) 
    {
        print "         <ul>\n";
        print "                 <li>".basename($dir)."<ul>";
        foreach(glob("$dir/*") as $path) 
        {
            createMenu($path);
        }
        echo "                  </ul></li>";
        print "         </ul>\n";
    }
    else 
    {
        $extension = $dir; #pathinfo($dir);
        #$extension = $extension['extension'];
        echo "<li><a class='load-file' data-file='$dir' href='../$dir'>".basename($dir)."</a></li>";
    }
}


createMenu($Directory);
                       ?>               
        </div>        
    </div><!--  <div id="tab-content"> -->
</div><!-- End DIV row -->
<!-- //////////////    auth/admin/project_detail      //////////////  -->