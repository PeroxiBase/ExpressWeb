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
// http://www.itnewb.com/v/Generating-Session-IDs-and-Random-Passwords-with-PHP
function generate_token ($len = 32)
{
        // Array of potential characters, shuffled.
        $chars = array(
                'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 
                'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
                'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 
                'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
                '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'
        );
        shuffle($chars);
        $num_chars = count($chars) - 1;
        $token = '';
        // Create random token at the specified length.
        for ($i = 0; $i < $len; $i++)
        {
                $token .= $chars[mt_rand(0, $num_chars)];
        }
        return $token;
}
session_start();
 if(isset($_SESSION['running_job'])) unset($_SESSION['running_job']);
if(isset($_SESSION['post']) )
{
    $_POST= $_SESSION['post'];
    $inputDBhost =  $_POST['inputDBhost'] ; 
    $inputDNS_DBhost = $_POST['inputDNS_DBhost'];
    $inputDBname =  $_POST['inputDBname'] ;
    $inputDBusername =  $_POST['inputDBusername'] ;
    $inputDBpassword =  $_POST['inputDBpassword'] ;
    $inputSiteurl =  $_POST['inputSiteurl'] ;
    $inputEncryption_key =  $_POST['inputEncryption_key'] ;
    $input_apache_user =  $_POST['input_apache_user'] ;
    $input_admin_email = $_POST['input_admin_email'] ;
    $input_admin_name = $_POST['input_admin_name'] ;
    $input_header_name =  $_POST['input_header_name'] ;
    $input_web_path =  $_POST['input_web_path'] ;
    $input_network =  $_POST['input_network'] ;
    $input_similarity = $_POST['input_similarity'] ;
    $input_cluster_env =  $_POST['input_cluster_env'] ;
    $input_cluster_app =  $_POST['input_cluster_app'] ;
    $input_work_cluster =  $_POST['input_work_cluster'] ;
    $input_python_app =  $_POST['input_python_app'];
    $input_rscript_app =  $_POST['input_rscript_app'];
    $input_bash_app =  $_POST['input_bash_app'] ;
    $input_MaxGeneNameSize = $_POST['input_MaxGeneNameSize'];
    $input_maxError = $_POST['input_maxError'];
    $input_qdelay = $_POST['input_qdelay'];
}
else
{
    $inputDBhost = '' ;
    $inputDNS_DBhost = '';
    $inputDBname = '' ;
    $inputDBusername = '' ;
    $inputDBpassword = '' ;
    $inputSiteurl = '' ;
    $inputEncryption_key = generate_token();
    $input_apache_user = '' ;
    $input_admin_email = '' ;
    $input_admin_name = 'administrator'; 
    $input_header_name = '' ;
    $input_web_path = '' ;
    $input_network = 'assets/network/';
    $input_similarity = 'assets/similarity/';
    $input_cluster_env = '/SGE/ogs' ;
    $input_cluster_app = '/SGE/ogs/bin/linux-x64' ;
    $input_work_cluster = '' ;
    $input_python_app = '' ;
    $input_rscript_app = '' ;
    $input_bash_app = '' ;
    $input_MaxGeneNameSize = 15;
    $input_maxError = 50;
    $input_qdelay = 30;
}

?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="bootstrap.css"/>
    
<script type="text/javascript" src="../assets/js/jquery-2.1.4.min.js"></script>
    <title>Expression Database App : Installation </title>
</head>
<script>
$(document).ready(function() {
    $('#input_web_path').blur(function() {
            var path = $(this).val();
            path = path.replace(/\/?$/, '/');            
      $('#input_network').val( path);
      $('#input_network2').val( path);
    });
    $("#random").click(function(){
             var number =  "<?php $val=generate_token() ; print "$val";?>";
                 $("#CryptKey").val(number);
    });
    
});
</script>
<body>
<div class="container">
    <div class="row-fluid">
    <h4>Install Your Expression Database App</h4>
    <form class="form-horizontal" action="form.php" method="post" style="margin-top:30px;">

        <?php if ($error == 1): ?>
        <div class="alert alert-error" style="font-size:11px;">               
            <b>Opps error ... </b> please check: 
            <br/> - <i>Each fields cannot be blank</i>
            <br/> - <i>App Folder and System Folder cannot the same</i>
            <br/> - <i>Database name must exist on your MySQL</i>
        </div>
        <?php endif; ?>
        <legend>Database Settings</legend>
        
        <div class="row">
            <div class="col-md-4">
                <div class="control-group">
                    <label class="control-label">DB Hostname</label>
                    <div class="controls">
                        <input type="text" id="UserHost" name="inputDBhost" value="<?php print $inputDBhost; ?>" placeholder="% , localhost or website.domain.org " size="30" required />
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="control-group"> 
                    <label class="control-label">DB Hostname for cluster</label>
                    <div class="controls">
                        <input type="text" id="UserHost" name="inputDNS_DBhost" value="<?php print $inputDNS_DBhost; ?>" placeholder="website.domain.org" size="30" required />
                    </div>
                </div>
            </div>          
        </div>
        <div class="row">
                <div class="col-md-12">
                <pre><b>DB Hostname</b>: if mysql server and apache are on the same machine use  <b>localhost</b> otherwise give DNS name of mysql server <b>fully.qualified.domain.name</b> 
If 'DB Hostname' use DNS name, 'DB Hostname for cluster' will used same value
<b>DB Hostname for cluster</b>: use only <b>fully.qualified.domain.name</b>
</pre>
            </div>
        </div>
        <div class="row">       
            <div class="col-md-4">
                <div class="control-group">
                    <label class="control-label">DB Username</label>
                    <div class="controls">
                        <input type="text" id="UserUsername" name="inputDBusername" value="<?php print $inputDBusername; ?>" required />
                    </div>
                </div>                
                <div class="control-group">
                    <label class="control-label">DB Password</label>
                    <div class="controls">
                        <input type="password" id="UserPass" name="inputDBpassword" value="<?php print $inputDBpassword; ?>" required />
                    </div>
                </div>
                
                <div class="control-group">
                    <label class="control-label">DB Name</label>
                    <div class="controls">
                        <input type="text" id="UserDb" name="inputDBname" value="<?php print $inputDBname; ?>" required />
                    </div>
                </div>            
          </div>
       </div>
        <!-- //////////////////////////////////////////////////////////////////////////////////////// -->
        <hr />
        <legend>Web server Settings</legend>
        <div class="control-group">
            <label class="control-label">Site URL</label>
            <div class="controls">
                <input type="text" name="inputSiteurl" size="60"  value="<?php print $inputSiteurl; ?>" required />
            </div>
            <pre>ex :website.domain.org  . If you defined an Alias in apache config (ex ExpressWeb) give full name: website.domain.org/ExpressWeb<br />
            <b>Note</b>: please remove any http:// or https:// before your site URL<br /></pre>
            
        </div>
        <!-- Site encryption key   Used by codeigniter to secure your installation-->
                <input type="hidden" id="CryptKey" name="inputEncryption_key"  value="<?php print $inputEncryption_key; ?>" size="34" readonly />
         
        <div class="control-group">
            <label class="control-label">User running apache / job launcher</label>
            <div class="controls">
                <input type="text" name="input_apache_user" value="<?php print $input_apache_user; ?>" placeholder="apache" required />
            </div>
            <pre><b>Important</b>: name of user who run apache. By default, user is 'apache' .  
            Check with your cluster administrator if user apache is allowed to write to the cluster !!. 
            If not, you may have to use an account with shell access and write permission to run apache.</pre>
        </div>
        <div class="control-group">
            <label class="control-label">Website title</label>
            <div class="controls">
                <input type="text" name="input_header_name" value="<?php print $input_header_name; ?>" placeholder="The Expression Db" size="35" required />
            </div>
            <pre>Name of your website. Will be displayed in navigator tab, Home page and footer</pre>
        </div>
        <div class="control-group">
            <label class="control-label">Web path</label>
            <div class="controls">
                <input type="text" id="input_web_path"  name="input_web_path" value="<?php print $input_web_path; ?>" placeholder="/var/www/html/ExpressWeb" size="35" required />
            </div>
            <pre>Full path of web Site directory. Applications (controller, model, view) and storage folders will be created under it.</pre>
        </div>
        <div class="control-group">
            <label class="control-label">Cluster results Folder</label>
            <div class="controls">
                <input type="text" id="input_network"  size="35" />
                <input type="text" name="input_network"  value="<?php print $input_network; ?>" placeholder="assets/network/" size="35" required/><br />
                <input type="text" id="input_network2"  size="35" />
                <input type="text" name="input_similarity"  value="<?php print $input_similarity; ?>" placeholder="assets/similarity/" size="35" required/>
            </div>
            <pre>The full path of directory used for storing computed networks.Default under Web Path.</pre>
        </div>
        <div class="control-group">
            <label class="control-label">MaxGeneNameSize</label>
            <div class="controls">
                <input type="text" name="input_MaxGeneNameSize"  value="<?php print $input_MaxGeneNameSize; ?>" placeholder="assets/network/" size="5"/><br />
             </div>
            <pre>The maximum length of gene name. Limit size for a better display in heatmap and network display</pre>
        </div>
        
        <div class="control-group">
            <label class="control-label">Admin email</label>
            <div class="controls">
                <input type="email" name="input_admin_email"  value="<?php print $input_admin_email; ?>" placeholder="email@website.org" size="35" required/><br />
             </div>
            <pre>Admin email. Use for account validation</pre>
        </div>
        
        <div class="control-group">
            <label class="control-label">Admin login</label>
            <div class="controls">
                <input type="email" name="input_admin_name"  value="<?php print $input_admin_name; ?>" placeholder="email@website.org" size="35" required/><br />
             </div>
            <pre>Admin email. Use for account validation</pre>
        </div>
        <!-- ////////////////////////////////////////////////////////////////// -->
        <hr />
        <legend>Cluster Settings</legend>
        <div class="control-group">
            <label class="control-label">Cluster Env Path</label>
            <div class="controls">
                <input type="text" name="input_cluster_env" value="<?php print $input_cluster_env; ?>" placeholder="/SGE/ogs" size="35" required />
            </div>
            <pre> Root path of cluster manager</pre>
        </div>
        <div class="control-group">
            <label class="control-label">Cluster App Path</label>
            <div class="controls">
                <input type="text" name="input_cluster_app" value="<?php print $input_cluster_app; ?>" placeholder="/SGE/ogs/bin/linux-x64" size="35" required />
            </div>
            <pre> Full path of binary command for cluster operation (qsub,qstat,...)</pre>
        </div>
        <div class="control-group">
            <label class="control-label">Cluster working Folder</label>
            <div class="controls">
                <input type="text" name="input_work_cluster"  value="<?php print $input_work_cluster; ?>"  placeholder="/work/user_name/cluster/" size="35" required />
            </div>
            <pre>The full path to writing directory on cluster. Your cluster job should be able to write in this directory !!</pre>
        </div>
        <div class="control-group">
            <label class="control-label">Qdelay</label>
            <div class="controls">
                <input type="text" name="input_qdelay"  value="<?php print $input_qdelay; ?>"  placeholder="30" size="5" />
            </div>
            <pre>Waiting time in second before new qsub status check. On overloaded cluster script increase this value</pre>
        </div>
        <div class="control-group">
            <label class="control-label">maxError try</label>
            <div class="controls">
                <input type="text" name="input_maxError"  value="<?php print $input_maxError; ?>"  placeholder="50" size="5" />
            </div>
            <pre>While submitting job to the cluster, script test connectivity. On overloaded cluster script will exit after maxError try</pre>
        </div>
        
         <!-- ////////////////////////////////////////////////////////////////// -->
        <hr />
        <legend>Third party software Settings</legend>
        <div class="control-group">
            <label class="control-label">Python binary Path</label>
            <div class="controls">
                <input type="text" name="input_python_app" value="<?php print $input_python_app; ?>" placeholder="/usr/bin/python" size="35" required />
            </div>
            <pre> Full path of python program (/usr/bin/python) </pre>
        </div>
        <div class="control-group">
            <label class="control-label">Rscript binary Path</label>
            <div class="controls">
                <input type="text" name="input_rscript_app" value="<?php print $input_rscript_app; ?>" placeholder="/usr/bin/Rscript" size="35" required />
            </div>
            <pre> Full path of Rscript command </pre>
        </div>
        <hr />
        <div class="control-group">
            <label class="control-label">Bash binary path</label>
            <div class="controls">
                <input type="text" name="input_bash_app"  value="<?php print $input_bash_app; ?>"  placeholder="/usr/bin/bash" size="35" required />
            </div>
            <pre>Full path of bash command</pre>
        </div>
        
        <div class="control-group">
            <div class="controls">
                <input type="reset" class="btn" name="btn" value="Reset"/>
                <input type="submit" class="btn btn-primary" name="btn-install" value="Install"/>
            </div>
        </div>
    </form>
    </div>
</div>

</body>
</html>
