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
        <ol>
            <li>You have mysql privilege to install a database</li>
                <ul>
                        <li>provided root username and password for creating a new database </li>
                        <li> define a user with all privileges and grants on the new database</li>      
                </ul>
            <li>ExpressWeb database is already installed:
                <ul><li>use connexion information provided by your database administrator</li>
                </ul>
             </li>
        </ol>
        <div class="row">
          <div class="col-md-4">
                <legend>Case 1</legend>
                    <div class="control-group">
                        <label class="control-label">DB Root name</label>
                        <div class="controls">
                            <input type="text" name="inputRDBRootName" value="<?php print $inputRDBRootName; ?>" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">DB Root Password</label>
                        <div class="controls">
                            <input type="password" name="inputRDBRootPassword" value="<?php print $inputRDBRootPassword; ?>" />
                        </div>
                    </div>
                    <hr />
                    <div class="control-group">
                        <label class="control-label">DB Host</label>
                        <div class="controls">
                            <input type="text" name="inputRDBhost" value="<?php print $inputRDBhost; ?>" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">DB Name</label>
                        <div class="controls">
                            <input type="text" name="inputRDBname" value="<?php print $inputRDBname; ?>" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">DB Username</label>
                        <div class="controls">
                            <input type="text" name="inputRDBusername" value="<?php print $inputRDBusername; ?>" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">DB Password</label>
                        <div class="controls">
                            <input type="password" name="inputRDBpassword" value="<?php print $inputRDBpassword ; ?>" />
                        </div>
                    </div>
          </div>
          <div class="col-md-4">
                <legend>Case 2</legend>
        
                <div class="control-group">
                    <label class="control-label">DB Host</label>
                    <div class="controls">
                        <input type="text" name="inputDBhost" value="<?php print $inputDBhost; ?>" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">DB Name</label>
                    <div class="controls">
                        <input type="text" name="inputDBname" value="<?php print $inputDBname; ?>" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">DB Username</label>
                    <div class="controls">
                        <input type="text" name="inputDBusername" value="<?php print $inputDBusername; ?>" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">DB Password</label>
                    <div class="controls">
                        <input type="password" name="inputDBpassword" value="<?php print $inputDBpassword; ?>" />
                    </div>
                </div>
          </div>
       </div>
        
        
        
        
        <hr />
        <legend>Web server Settings</legend>
        <div class="control-group">
            <label class="control-label">Site URL</label>
            <pre>ex :website.domain.org  . If you defined an Alias in apache config (ex ExpressWeb) give full name: website.domain.org/ExpressWeb<br />
<b>Note</b>: please remove any http:// or https:// before your site URL<br /></pre>
            <div class="controls">
                <input type="text" name="inputSiteurl" size="40"  value="<?php print $inputSiteurl; ?>" required />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">Site encryption key</label>
            <div class="controls">
                <input type="text" name="inputEncryption_key" value="<?php print $inputEncryption_key; ?>" size="35" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">User running apache / job launcher</label>
            <pre><b>Important</b>: name of user who run apache. By default, user is 'apache' . <br />
Check with your cluster administrator if user apache is allowed to write to the cluster !!.<br />
If not, you may have to use an account with shell access and write permission to run apache.</pre>
            <div class="controls">
                <input type="text" name="input_apache_user" value="<?php print $input_apache_user; ?>" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">Website title</label>
            <pre>Name of your website. Will be displayed in navigator tab, Home page and footer</pre>
            <div class="controls">
                <input type="text" name="input_header_name" value="<?php print $input_header_name; ?>" placeholder="The Expression Db" size="35" required />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">Web path</label>
            <pre>Full path of web Site directory. Applications (controller, model, view) and storage folders will be created under it.</pre>
            <div class="controls">
                <input type="text" id="input_web_path"  name="input_web_path" value="<?php print $input_web_path; ?>" placeholder="/var/www/html/ExpressWeb" size="35" required />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">Cluster results Folder</label>
            <pre>The full path of directory used for storing computed networks.Default under Web Path.</pre>
            <div class="controls">
                <input type="text" id="input_network"  size="35" />
                <input type="text" name="input_network"  value="<?php print $input_network; ?>" placeholder="assets/network/" size="35" required/>
            </div>
        </div>
        <!-- ////////////////////////////////////////////////////////////////// -->
        <hr />
        <legend>Cluster Settings</legend>
        <div class="control-group">
            <label class="control-label">Cluster Env Path</label>
            <pre> Root path of cluster manager</pre>
            <div class="controls">
                <input type="text" name="input_cluster_env" value="<?php print $input_cluster_env; ?>" placeholder="/SGE/ogs" size="35" required />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">Cluster App Path</label>
            <pre> Full path of binary command for cluster operation (qsub,qstat,...)</pre>
            <div class="controls">
                <input type="text" name="input_cluster_app" value="<?php print $input_cluster_app; ?>" placeholder="/SGE/ogs/bin/linux-x64" size="35" required />
            </div>
        </div>
        <hr />
        <div class="control-group">
            <label class="control-label">Cluster working Folder</label>
            <pre>The full path to writing directory on cluster. Your cluster job should be able to write in this directory !!</pre>
            <div class="controls">
                <input type="text" name="input_work_cluster"  value="<?php print $input_work_cluster; ?>"  placeholder="/work/user_name/cluster/" size="35" required />
            </div>
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

