<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="bootstrap.css"/>    
    <script type="text/javascript" src="../assets/js/jquery-2.1.4.min.js"></script>
    
    <title>Expression Database App : Installation . Required steps</title>
</head>
<body>
<div class="container">
    <div class="row-fluid">
        <h4>Before installing Your Expression Database App</h4>
         <br />
         ExpressWeb was developped under <a href="https://codeigniter.com/docs" target="_blank" title="Go to Codeigniter web site" >Codeigniter 3.0.3</a> 
        a PHP framework.<br /><br />
        Codeigniter structure is show in fig1. <br /> <br />
                
        <div class="row">
        
            <div class="col-md-2">
<pre>
.
├── application/
├── <b>assets/</b>
├── composer.json
├── contributing.md
├── index.php
├── <b>install/</b>
├── license.txt
├── output/
├── readme.rst
├── system/
└── user_guide/
</pre>fig1
            </div>
            <div class="col-md-2">
<pre>
application/
├── cache/
├── config/
├── controllers/
├── core/
├── helpers/
├── hooks/
├── index.html
├── language/
├── libraries/
├── logs/
├── models/
├── third_party/
└── views/
</pre>fig2
            </div>
            <div class="col-md-2">
<pre>
assets/
├── css/
├── img/
├── js/
├── <b>network/</b>
├── <b>scripts/</b>
├── <b>similarity/</b>
├── <b>uploads/</b>
└── <b>users/</b>
</pre>fig3
           </div>
           <div class="col-md-2">
<pre>
install/
├── <b>apache_conf/</b>
├── bootstrap.css
├── form.php
├── index.php
├── <b>sql/</b>
├── submit_ori.php
└── submit.php

           </pre>fig4
           </div>
           
        </div> 
        
        All source code (for web interface) are located under <b>application</b> directory (fig2)<br />
        <b>assets</b> directory (fig3) contains directories required by our application (network,scripts,similarity,uploads,users)<br />
        <b>scripts</b> directory contains scripts used for cluster submission and post-calculations. 
        These scripts will be copied to the cluster in a directory with cluster write access.<br />
        <b>install</b> directory contain scripts for installing Expression Database App.<br /> 
        <ul>
                <li>In apache_conf you will find an example of httpd.conf file</li>
                <li>In sql you will find sql scripts for creating Expression database<br /></li>
                <li><b>This directory will be renamed after configuration</b></li>
        </ul>
        
        <br />
        <legend>Apache configuration</legend>
         <ol>
                <li>Create a directory located in your web space (under <code>DocumentRoot</code> ) (ex: /var/www/htm/ExpressWeb)</li>
                <li>Copy the archive in this directory and Unzip it</li>                
                <li>Edit your Apache conf file (/etc/httpd/conf/httpd.conf | /etc/apache2/apache2.conf & sites-enabled/) 
                    <ul>
                        <li>look at <code>install/apache_conf/httpd.conf</code> for example</li>
                        <li> define a new virtual host (website.domain.org) or  create an alias (ExpressWeb)</li>
                    </ul>
                </li>
                <li>reload apache config and open page <code>http://website.domain.org/install/</code> 
                or <code>http://website.domain.org/ExpressWeb/install</code></li>
                <li>you should display the first page of the installer "Before installing Your Expression Database App" </li>
         </ol>
       
        <br />
        <legend>Database settings</legend>
        
        Before proceeding to configuration of your local copy of Expression Db , you need to create the database on your mysql server.<br />
        <b>Note:</b> Replace <i>username,hostname,password</i> and <i>dbname</i> by your own value.<hr />
        <b>Note II:</b> Even if your mysql server is located on the same machine as your Apache server, Do not use 'localhost' as hostname. <br />
        Use '%' (for any host) to allow cluster to connect to your database<br />
        When you launch job on the cluster, 'localhost' will be interpreted as local from cluster !! <hr />
            <ol>
                <li>define a username with all privileges and grants on the new database:<br />
                        <code>* CREATE USER 'username'@'hostname' IDENTIFIED BY 'password';<br />
                        * GRANT ALL PRIVILEGES ON `dbname`.* TO 'username'@'hostname' WITH GRANT OPTION;<br />
                        * FLUSH PRIVILEGES ;
                        </code>
<pre>ex:
CREATE USER 'expres_db'@'%' IDENTIFIED BY 'my_super_password';
GRANT ALL PRIVILEGES ON `DbExpres_db`.* TO 'expres_db'@'%' WITH GRANT OPTION;
FLUSH PRIVILEGES;

<b>paranoid</b>

With mysql server and apache server on same machine
1) create user localhost with All privileges:
GRANT USAGE ON *.* TO 'express_web'@'localhost' IDENTIFIED BY PASSWORD 'my_super_password';
GRANT ALL PRIVILEGES ON `DbExpres_db`.* TO 'express_web'@'localhost' WITH GRANT OPTION;

2) create user % with limited access for the cluster:
GRANT USAGE ON *.* TO 'express_web'@'%.cluster.org'' IDENTIFIED BY PASSWORD 'my_super_password'; //any of cluster's node with dns record
or
GRANT USAGE ON *.* TO 'express_web'@'192.168.25.%' IDENTIFIED BY PASSWORD 'my_super_password'; // any of cluster's node by subnet @IP address

GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, CREATE TEMPORARY TABLES, LOCK TABLES, CREATE VIEW, SHOW VIEW ON `DbExpres_db`.* TO 'express_web'@'%';

</pre>
                </li>
                <li>Keep these credentials for next steps</li>
                <li>import express_web.sql (in install/sql/) . Database will be created with table and user accounts<br />
                        from command prompt:<br />
                        <code>mysql -u username -p<br />
                        mysql > source install/sql/express_web.sql
                        </code>
                </li>
            </ol>
        <br />
        <legend>Web ,directories,third party software and cluster settings</legend>
        
        Our software use SGE cluster to calculate networks and similarity matrices from expression data submited by users.<br />
        Cluster calculation require write permission on cluster and environment configuration to allow web submission.<br /> 
        <br />
        We need to define variables for various configuration files and executable scripts.<br />
        
        <h2><small>Web requirement</small></h2>

        <dl>
            <dt><code>apache_user</code></dt>
                <dd>By default, apache is run under user apache and group apache.<br />
                You may have to run apache under an account allowed to write on the cluster... <br />
                <b>This account is used for job submission</b>. <hr />
                <b>You need to check if <code>apache_user</code> is able to launch a job on your cluster from Apache !!</b><hr />
                </dd>
            
            <dt><code>Site URL</code></dt>
                <dd>Name of your web site(ex :website.domain.org)<br /> 
                If you defined an Alias in apache config (ex ExpressWeb) give full name:
                website.domain.org/ExpressWeb </dd>
                
            <dt><code>Web path</code></dt>
                <dd>Full path of web Site directory as defined in apache conf<br />
                    <code>&lt;Directory "/var/www/html/ExpressWeb/"></code>
                </dd>
            
            <dt><code>Cluster results Folder</code></dt>
                <dd>The full path of directory used for storing computed networks and similarity files.<br />
                Default under Web Path in <code>/assets/network</code> adn <code>/assets/similarity</code><br />
                </dd>
                
            <dt><code>Admin email</code></dt>
                <dd>User must request account to access database. Request is send to <code>Admin email</code> for validation purpose<br />
                </dd>
                
                
                
        </dl>
         
        <h2><small>Cluster Settings</small></h2>
        
        You need to define application and environment variables to submit job on cluster.<br />
        <h4><small>Cluster architecture</small></h4>
        <dl>
            <dt><code>Cluster Env Path</code></dt>
                <dd>Root path of cluster manager: <code>/SGE/ogs/</code></dd>
                
            <dt><code>Cluster App Path</code></dt>
                <dd>Full path of binary command for cluster operation (qsub,qstat,...) <code>/SGE/ogs/bin/linux-x64</code></dd>
                
            <dt><code>Cluster working Folder</code></dt>
                <dd>The full path to writing directory on cluster. Your cluster job should be able to write in this directory !!<br />
                Two directories will be created under this directory:<br />
                <ul>
                        <li><b>files</b>: intermediates results files will be write temporarily in this directory. 
                        On success , files are moved to <code>Cluster results Folder</code></li>
                        <li><b>scripts</b>: scripts in <code>assets/scripts/</code> will be copied in this directory<br />
                        job submission are done from this directory and all job files and control commands are write/execute from this directory<br />
                        temporary files are deleted on success.
                        </li>
                </dd>
        </dl>
        
        <h2><small>Third party software</small></h2>
        
        <dl>
            <dt><code>Python</code></dt>
                <dd>Python 2.7.2</dd>
                <dd>Libraries used:<br />
                        <ul>
                                <li>MySQLdb</li>
                                <li>csv</li>
                                <li>json</li>
                                <li>resource</li>
                                <li>math</li>
                        </ul>
                </dd>
            <dt><code>R</code></dt>
                <dd>R version 3.1.2</dd>
                <dd>Libraries used:<br />
                    <ul>
                        <li>RMySQL</li>
                        <li>Hmisc</li>
                        <li>RJSONIO</li>
                        <li>dendextend</li>
                    </ul>
                </dd>
        </dl>
        
        <legend>Configuration process</legend>
        With all these informations , we are able to configure Codeigniter (config,database), configuration scripts for cluster scripts<br />
        Path and Directories will be checked.If need we'll try to create missing directories...<br />
        A cluster job submission will be launched.<br />
        On success install directory will be renamed and your web site launch!.<br />

        <form class="form-horizontal" action="submit.php" method="post" style="margin-top:30px;">
                <input type="submit" class="btn btn-primary" name="btn-install" value="Proceed to configuration..."/> 
        </form>
    </div>
</div>
</body>
</html>
