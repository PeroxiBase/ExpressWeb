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

//var_dump($project);
?>
      <!--  <div id="tabs"> -->
            <ul  class="nav nav-tabs" role="tablist">
                <li role="presentation" active ><a href="#file" role="tab" data-toggle="tab" aria-controls="file" >Datasets files</a></li>
                <li role="presentation" ><a href="#users_file" role="tab" data-toggle="tab" aria-controls="users_file" >User files</a></li>
                <?php if($this->ion_auth->is_admin()){ ?>
                    <li role="presentation" ><a href="#reports_file" role="tab" data-toggle="tab" aria-controls="reports_file" >Reports files</a></li>
                <?php } ?>
            </ul>
            
          <div class="tab-content">               
             
                <div class="tab-pane active" id="file">
                          
                <?php
                    if( $File_list)
                    {
                        print "Clustering results raw files availables<br />\n";
                        print $File_list;
                    }
                    else
                    {
                        print "No files availables\n";
                    }
                       ?>               
                </div>
                
                <div class="tab-pane" id="users_file">
                        <?php if(is_dir($Directory)) { ?>
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
                                echo "<li><a class='load-file' data-file='$dir' href='../$dir'>".basename($dir)." (".round(filesize("$dir")/1000,2)." Kb)</a></li>";
                            }
                        }
                        
                        
                        createMenu($Directory);
                        }
                        else
                        {
                                print "No files availables\n";   
                        }
                       ?>               
               </div><!--  END div id="users_file" -->
               <?php if($this->ion_auth->is_admin())
               {
                   print "<div class=\"tab-pane\" id=\"reports_file\">\n";
                   if($Report_Files)
                   {
                       print $Report_Files;
                   }
                   else
                   {
                       print "No reports data availables\n";
                   }
                   print " </div>\n";
               }
               ?>
        </div><!--  <div id="tab-content"> -->
    </div><!-- End DIV row -->
    
<script type="text/javascript">    
$(".delFile").click(function()
{
   var FileName = $(this).attr("value");
   jQuery.ajax({ 
            type:'post',
            url:'delete_file',
            data: 'FileName='+FileName,
            dataType:'html',
            success: function(code_html, statut)
            { // success est toujours en place, bien sûr !
                 // alert('File '+FileName+' deleted '+status+ ' mesg:'+code_html);
                location.reload();
            },
            error: function(resultat, statut, erreur)
            { 
               alert('File '+FileName+' not deleted !');
               console.log('File '+FileName+' not deleted   status:'+status+ ' res:'+resultat+' err:'+erreur);
                location.reload();
            }
    });
});
</script>
<!-- //////////////    auth/admin/project_detail      //////////////  -->