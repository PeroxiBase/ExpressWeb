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
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="bootstrap.css"/>
    <title>Expression Database App : Installation result</title>
</head>
<body>
<div class="container">

<?php
session_start();
session_destroy();
$error = 0;
$debug=0;
if (isset($_POST['btn-install'])) 
{
   
    print "Data submitted: <pre>".print_r($_POST,1)."</pre><br />\n";
    // validation

    // setting site url
    $inputSiteurl = trim( $_POST['inputSiteurl']);
    $inputSiteurl = preg_replace("/http:\/\/|https:\/\//","",$inputSiteurl);
    $inputSiteurl = preg_replace("/\/$/","",$inputSiteurl);
    ######  copy original file  ##############
    copy("config/config.php","../application/config/config.php");
    ################################################################
    $file_config = "../application/config/config.php";
    $Content_config = file_get_contents($file_config);
    $content_config = preg_replace("/config\['domain'\] = '.*';/", "config['domain'] = '$inputSiteurl';",$Content_config);
    $content_config = preg_replace("/config\['encryption_key'\] = '.*';/","config['encryption_key'] = '".$_POST['inputEncryption_key']."';", $content_config);
    file_put_contents($file_config, $content_config);
    print "<pre>Write $file_config</pre><br />\n";
    if($debug) print "<pre>".htmlentities($content_config)."</pre> domain: $inputSiteurl key ".$_POST['inputEncryption_key'].".<br />\n";
    
    // modify .htaccess !!
    copy("config/.htaccess","../.htaccess");
    $get_path = pathinfo($inputSiteurl);
    $Base = $get_path['filename'];
    $ChBase =exec("sed -i 's/RewriteBase \/ExpressWeb\//RewriteBase \/$Base\//' ../.htaccess ",$ShebR);
    print "<pre>Write .htaccess  New path Base: $Base</pre><br />\n";
    // setting database
    ######  copy original file  ##############
    copy("config/database.php","../application/config/database.php");
    ################################################################
    $file_db = "../application/config/database.php";
    $Content_db = file_get_contents($file_db);
    if(isset($_POST['inputDBusername']) && isset($_POST['inputDBpassword']))
    {
        // db already created
        $username = trim($_POST['inputDBusername']);
        $password = trim($_POST['inputDBpassword']);
        $hostname = trim($_POST['inputDBhost']);
        $DNS= 0;
        #print "hostname $hostname<br />";
        if(!preg_match("/localhost/",$hostname))
        {
            $hostname = preg_replace("/http:\/\/|https:\/\/|\/$/","",$hostname);
           # print "Clean hostname $hostname<br />";
            if(filter_var(gethostbyname($hostname), FILTER_VALIDATE_IP))
            {
                $DNS = 1;
                
            }
            else
            {
                $DNS = 0;
                $message = "Your hostname $hostname is not a valid DNS domain !!";
                show_error( $message,$_POST);
                $success=0;
                exit;
            }
            
        }
        if( $DNS == 1)
        {
            $DNS_hostname = $hostname;
        }
        else
        {
            $DNS_hostname = trim($_POST['inputDNS_DBhost']);            
            #print "DNS_hostname $DNS_hostname<br />";
            $DNS_hostname = preg_replace("/http:\/\/|https:\/\/|\/$/","",$DNS_hostname);
            #print "Clean DNS_hostname $DNS_hostname<br />";
            
            if(filter_var(gethostbyname($DNS_hostname), FILTER_VALIDATE_IP))
            {
                $IP = gethostbyname($DNS_hostname); 
                print "<pre>DNS_hostname $DNS_hostname is a valid DNS Domain with @IP: $IP</pre><br />";
            }
            else
            {
                $message = "Your hostname $hostname is not a valid DNS domain !!";
                show_error( $message,$_POST);
                $success=0;
                exit;
            }
        }
        
        $database = trim($_POST['inputDBname']);
        $check_db=check_connect($username,$password,$hostname,$database);
        if($check_db==1)
        {                    
          $content_db = preg_replace("/'hostname' => '.*'/","'hostname' => '$hostname'", $Content_db);
          $content_db = preg_replace("/'username' => '.*'/","'username' => '$username'",  $content_db);
          $content_db = preg_replace("/'password' => '.*'/","'password' => '$password'",  $content_db);
          $content_db = preg_replace("/'database' => '.*'/","'database' => '$database'",  $content_db);
          file_put_contents($file_db, $content_db);   
          print "<pre>Write $file_db </pre><br />\n";
        }
        else
        {
            // credentials are wrong. stop
            $message= "Query failed! Unable to check database $database <br />\n";
            show_error( $message,$_POST);
            $success=0;
            exit;
        }                
    }
    else
    {
        $message = "Please provide database informations !!<br />\n";
        show_error( $message,$_POST);        
        $success=0;
        exit;
    }
    ////////////////////////////////////////
    // configure config/expressWeb.php
    $input_header_name = trim($_POST['input_header_name']);
    $web_path = preg_replace("/\/$/","",trim($_POST['input_web_path']) );
    $input_apache_user = trim($_POST['input_apache_user']);
    $input_admin_email = trim($_POST['input_admin_email']);
    $input_admin_name = trim($_POST['input_admin_name']);
    if (filter_var($input_admin_email, FILTER_VALIDATE_EMAIL)) 
    {
        print "<pre>Email $input_admin_email is a valid email address</pre><br />";
    }
    else
    {
        $message = "Your email $input_admin_email is not  valid  !!";
        show_error( $message,$_POST);
        $success=0;
        exit;
    }
    $input_network = trim($_POST['input_network']);
    $input_similarity = trim($_POST['input_similarity']);
    
    
    $cluster_env = trim($_POST['input_cluster_env']);
    $cluster_env = preg_replace("/\/$/","",$cluster_env);
    $cluster_app = trim($_POST['input_cluster_app']);
    $cluster_app = preg_replace("/\/$/","",$cluster_app);
    $work_cluster = trim($_POST['input_work_cluster']);
    $work_cluster = preg_replace("/\/$/","",$work_cluster);
    $MaxGeneNameSize = trim($_POST['input_MaxGeneNameSize']);
    $maxError = trim($_POST['input_maxError']);
    if ($MaxGeneNameSize == '' ) $MaxGeneNameSize = '15';
    if ($maxError == '' ) $maxError = '50';
    $qdelay = trim($_POST['input_qdelay']);
    if ($qdelay == '' ) $qdelay = '30';
    ////////////////////////////////////////
    //// check directory existance !!
    ////////////////////////////////////////
    $network_path = $web_path.'/'.$input_network;
    $similarity_path = $web_path.'/'.$input_similarity;
    $dir_paths = array('web_path' => $web_path,
        'network_path' => $network_path,
        'similarity_path' => $similarity_path ,
        'cluster_env' => $cluster_env,
        'cluster_app' => $cluster_app,
        'work_cluster' => $work_cluster,
        'work_files' => $work_cluster.'/files',
        'work_scripts' => $work_cluster.'/scripts');
    
    $wrong_dir = TRUE;
    $wrong_dir_message ="";
    
    print "<h4>Check directories</h4><pre>\n";
    
    foreach($dir_paths as $key=>$dir)
    {
        if(is_dir($dir))
        {
            print "Directory $key [ $dir ] exist.\n";
        }
        else
        {
            print "Directory $key [ $dir ] doesn't exist.\n";
            print "to create directory $key [ $dir ] :\n";
            if (!mkdir($dir, 0777, true))
            {                
                 $wrong_dir = FALSE;
                 $wrong_dir_message .= "Unable to create directory $key ...<br />\n";
            }
            else
            {
                 print "Directory $key created<br />\n";
            }            
        }
    }
    
    print "</pre>\n";
    
    
    if($wrong_dir == TRUE)
    {
        #############  check admin name  ####################
        if($input_admin_email != "administrator")
        {
            
            $query = "UPDATE users SET username = '$input_admin_name' WHERE id =1; ";
            do_sql($username,$password,$hostname,$database,$query);
            
            ##############  rename previous reference to administrator ##########
            $query = "UPDATE tables SET Submitter='$input_admin_name' WHERE Submitter ='administrator' ";
            do_sql($username,$password,$hostname,$database,$query);
        }
        
        ////////////////////////////////////////
        ///  OK: write conf/expressWeb.php
        ////////////////////////////////////////
        
        ######  copy original file  ##############
        copy("config/expressWeb.php","../application/config/expressWeb.php");
        ################################################################
        $file_Express = "../application/config/expressWeb.php";
        $Content_Express = file_get_contents($file_Express);
        $content_Express = preg_replace("/config\['header_name'\] = '.*';/","config['header_name'] = '$input_header_name';", $Content_Express);
        $content_Express = preg_replace("/config\['web_path'\] = '.*';/","config['web_path'] = '$web_path';", $content_Express);
        $content_Express = preg_replace("/config\['admin_name'\] = '.*';/","config['admin_name'] = '$input_admin_name';", $content_Express);
        $content_Express = preg_replace("/config\['apache_user'\] = '.*';/","config['apache_user'] = '$input_apache_user';", $content_Express);
        $content_Express = preg_replace("/config\['network'\] = .*\.'.*';/","config['network'] = \$web_path.'/$input_network';", $content_Express);
        $content_Express = preg_replace("/config\['similarity'\] = .*\.'.*';/","config['similarity'] = \$web_path.'/$input_similarity';", $content_Express);
        $content_Express = preg_replace("/config\['cluster_env'\] = '.*';/","config['cluster_env'] = '$cluster_env';", $content_Express);
        $content_Express = preg_replace("/config\['cluster_app'\] = '.*';/","config['cluster_app'] = '$cluster_app';", $content_Express);
        $content_Express = preg_replace("/config\['work_cluster'\] = '.*';/","config['work_cluster'] = '$work_cluster';", $content_Express);
        $content_Express = preg_replace("/config\['work_cluster'\] = '.*';/","config['work_cluster'] = '$work_cluster';", $content_Express);
        $content_Express = preg_replace("/config\['MaxGeneNameSize'\] = '.*';/","config['MaxGeneNameSize'] = '$MaxGeneNameSize';", $content_Express);
        $content_Express = preg_replace("/config\['maxError'\] = '.*';/","config['maxError'] = '$maxError';", $content_Express);
        $content_Express = preg_replace("/config\['qdelay'\] = '.*';/","config['qdelay'] = '$qdelay';", $content_Express);
        print "<pre>Write $file_Express</pre><br />\n";
        file_put_contents($file_Express, $content_Express);
        $check_cluster = "export SGE_ROOT= $cluster_env && ${cluster_app}/qstat -u $input_apache_user ";
        
        ///////////// update config/ion_auth.php
        ######  copy original file  ##############
        copy("config/ion_auth.php","../application/config/ion_auth.php");
        ################################################################
        $file_Ion_auth = "../application/config/ion_auth.php";
        $Content_Ion = file_get_contents($file_Ion_auth);
        $content_Ion = preg_replace("/config\['site_title'\]\s*= '.*';/","config['site_title'] = '$input_header_name';", $Content_Ion);
        $content_Ion = preg_replace("/config\['admin_email'\]\s*= '.*';/","config['admin_email'] = '$input_admin_email';", $content_Ion);
        print "<pre>Write $file_Ion_auth</pre> <br />\n";
        file_put_contents($file_Ion_auth, $content_Ion);
        
        ///////////// update view/admin/login.php 
        ######  copy original file  ##############
        copy("config/login.php","../application/view/admin/ion_auth.php");
        ################################################################
        $file_Login = "../application/view/admin/login.php";
        $Content_Login = file_get_contents($file_Login);
        $content_Login = preg_replace("/Expression database/","$input_header_name", $Content_Login);
        $content_Login = preg_replace("/mailto:void@dom.org/","mailto:$input_admin_email", $Content_Login);
        $content_Login = preg_replace("/The ExpressWeb Team/","$input_header_name Team", $Content_Login);
        print "<pre>Write $file_Login</pre> <br />\n";
        file_put_contents($file_Login, $content_Login);
        
    }
    else
    {
        $message = "Please check directory path!!:<br />$wrong_dir_message<br />\n\n";
        show_error( $message,$_POST);        
        $success=0;
        exit;
    }
    ///////////////////////////////////////////////////////////////
    //  Check third party software and cluster command ..       //
    /////////////////////////////////////////////////////////////
    print "<h4>Check cluster path and commands</h4>\n";
    
    $input_python_app = trim($_POST['input_python_app']);
    $Python_app = preg_replace("/\/$/","",$input_python_app);
    $input_rscript_app = trim($_POST['input_rscript_app']);
    $Rscript_app = preg_replace("/\/$/","",$input_rscript_app);
    $input_bash_app = trim($_POST['input_bash_app']);
    $Bash_app = preg_replace("/\/$/","",$input_bash_app);
    
    print "<h4><small>Check $input_apache_user ENV ...</small></h4><pre>\n";
    
    $Env = exec("env",$r0);
    print " ENV:".print_r($r0,1)."</pre>\n";
    
    $create_test_file = fopen("check_app.sh",'w');
    $data_file ="#!$Bash_app\n";
    $data_file .="echo '************* Bash version *************'
    
$Bash_app --version |head -1
echo '************* python version *************'
$Python_app -V

echo '************* Rscript --version *************'
$Rscript_app --version
export SGE_ROOT=$cluster_env
echo '************* qsub version *************'
$cluster_app/qsub -help  |head -1

echo '************* qstat *************'
$cluster_app/qstat |wc -l


echo '************* end script *************'
";
    
    
    fwrite($create_test_file, $data_file);
    fclose($create_test_file);
    

    print "<h4><small>Check command path ...</small></h4><pre>\n";
    $test_app = exec("$Bash_app check_app.sh 2&>test",$r1);
    $test_app = exec("cat test",$r1);
    print "Check app  <br />".implode("<br>",$r1)."</pre><br />\n";    

    ////////////////////////////////////////
    // All path and command Ok            //
    ////////////////////////////////////////
    $debug='1';
    ######  copy original file  ##############
    copy("scripts/ExpressWeb.conf","../assets/scripts/ExpressWeb.conf");
    ################################################################
    $Script_Express = "../assets/scripts/ExpressWeb.conf";
    $Content_SExpress = file_get_contents($Script_Express);
    $content_SExpress = preg_replace("/path_cluster='.*'/","path_cluster='$work_cluster'", $Content_SExpress);
    $content_SExpress = preg_replace("/output_files='.*'/","output_files='$work_cluster/files/'", $content_SExpress);
    $content_SExpress = preg_replace("/out_network='.*'/","out_network='$web_path/$input_network'", $content_SExpress);
    $content_SExpress = preg_replace("/out_similarity='.*'/","out_similarity='$web_path/$input_similarity'", $content_SExpress);
    $content_SExpress = preg_replace("/path_files='.*'/","path_files='$web_path/assets/users'", $content_SExpress);
    $content_SExpress = preg_replace("/SGE_Root='.*'/","SGE_Root='$cluster_env'", $content_SExpress);
    $content_SExpress = preg_replace("/qsub='.*'/","qsub='$cluster_app/qsub'", $content_SExpress);
    $content_SExpress = preg_replace("/qstat='.*'/","qstat='$cluster_app/qstat'", $content_SExpress);
    $content_SExpress = preg_replace("/debug='.*'/","debug='$debug'", $content_SExpress);
    $content_SExpress = preg_replace("/maxError='.*'/","maxError='$maxError'", $content_SExpress);
    $content_SExpress = preg_replace("/qdelay='.*'/","qdelay='$qdelay'", $content_SExpress);
    $content_SExpress = preg_replace("/host='.*'/","host='$DNS_hostname'", $content_SExpress);
    $content_SExpress = preg_replace("/dbUser='.*'/","dbUser='$username'", $content_SExpress);
    $content_SExpress = preg_replace("/dbPwd='.*'/","dbPwd='$password'", $content_SExpress);
    $content_SExpress = preg_replace("/db='.*'/","db='$database'", $content_SExpress);
    print "<pre><b>Write $Script_Express </b><br>$content_SExpress</pre><br />\n";
    file_put_contents($Script_Express, $content_SExpress);

    
    ######  copy original file  ##############
    copy("scripts/config.R","../assets/scripts/config.R");
    ################################################################
    $Script_Rscript = "../assets/scripts/config.R";
    $Content_RExpress = file_get_contents($Script_Rscript);    
    $content_RExpress = preg_replace("/output_files <- '.*'/","output_files <- '$work_cluster/files/'", $Content_RExpress);
    $content_RExpress = preg_replace("/host <- '.*'/","host <- '$DNS_hostname'", $content_RExpress);
    $content_RExpress = preg_replace("/user <- '.*'/","user <- '$username'", $content_RExpress);
    $content_RExpress = preg_replace("/password <- '.*'/","password <- '$password'", $content_RExpress);
    $content_RExpress = preg_replace("/dbname <- '.*'/","dbname <- '$database'", $content_RExpress);
    print "<pre><b>Write $Script_Rscript </b><br>$content_RExpress</pre><br />\n";
    file_put_contents($Script_Rscript, $content_RExpress);
    print "<br />\n";
    
    ////////////  change shebang !! /////////////////////
    print "<h4>Change shebang</h4>\n";
    $Rscript_app = preg_replace("/\//", "\/", "#!$Rscript_app");
    $R_before= exec("head -1 ../assets/scripts/DBClustering.R");
    $R_shebang= exec("sed -i '1s/.*/$Rscript_app/' ../assets/scripts/DBClustering.R",$ShebR);
    $R_after= exec("head -1 ../assets/scripts/DBClustering.R");
    
    print "<h4><small>Change DBClustering.R ...</small></h4><pre>\n";
    print "Before: $R_before <br />";
    print "After: $R_after</pre>\n";
            ////////////////////////////////////        
    $P_before= exec("head -1 ../assets/scripts/DBCreateNetwork.py");
    $Python_app = preg_replace("/\//", "\/", "#!$Python_app");
    $P_shebang= exec("sed -i '1s/.*/$Python_app/' ../assets/scripts/DBCreateNetwork.py",$ShebP);
    $P_after= exec("head -1 ../assets/scripts/DBCreateNetwork.py");
    
    print "<h4><small>Change DBCreateNetwork.py ...</small></h4><pre>\n";
    print "Before: $P_before <br />";
    print "After: $P_after</pre>\n";
            ////////////////////////////////////      
    $B_before= exec("head -1 ../assets/scripts/execute_bash.sh");
    $Bash_app = preg_replace("/\//", "\/", "#!$Bash_app");
    $B_shebang= exec("sed -i '1s/.*/$Bash_app/' ../assets/scripts/execute_bash.sh",$ShebB);
    $B_after= exec("head -1 ../assets/scripts/execute_bash.sh");
        
    print "<h4><small>Change execute_bash.sh ...</small></h4><pre>\n";
    print "Before: $B_before <br />";
    print "After: $B_after</pre>\n";
            ////////////////////////////////////  
    $B_before= exec("head -1 ../assets/scripts/clean.sh");
    $Bash_app = preg_replace("/\//", "\/", "#!$Bash_app");
    $B_shebang= exec("sed -i '1s/.*/$Bash_app/' ../assets/scripts/clean.sh",$ShebB);
    $B_after= exec("head -1 ../assets/scripts/clean.sh");
        
    print "<h4><small>Change clean.sh ...</small></h4><pre>\n";
    print "Before: $B_before <br />";
    print "After: $B_after</pre>\n";         
    
    print "<h4>Copy scripts files to cluster directory</h4><pre>\n";
    // 
    $Copy_files= array('clean.sh','config.R','DBClustering.R','DBCreateNetwork.py','execute_bash.sh','ExpressWeb.conf');
    
    foreach($Copy_files as $files)
    {
        $cp =exec("cp ${web_path}/assets/scripts/$files ${work_cluster}/scripts/",$RF);
        print "cp ${web_path}/assets/scripts/$files ${work_cluster}/scripts/ \n";
    }
    
    if(count($RF) >0) print "Check... ".print_r($RF,1)."<br />\n";
    print "</pre>\n";
    
    unset($_SESSION['running_job']);
    
    print " <form class=\"form-horizontal\" action=\"check_cluster.php\" method=\"post\" style=\"margin-top:30px;\">
                <div class=\"control-group\">
                    <div class=\"controls\">
                        <input type=\"hidden\" name=\"web_path\" value=\"$web_path\" />
                        <input type=\"hidden\" name=\"work_cluster\" value=\"$work_cluster\" />
                        <input type=\"hidden\" name=\"apache_user\" value=\"$input_apache_user\" />
                        <input type=\"hidden\" name=\"admin_name\" value=\"$input_admin_name\" />
                        <input type=\"hidden\" name=\"check_cluster\" value=\"$check_cluster\" />
                        <input type=\"hidden\" name=\"hostname\" value=\"$hostname\" /> 
                        <input type=\"hidden\" name=\"username\" value=\"$username\" /> 
                        <input type=\"hidden\" name=\"password\" value=\"$password\" /> 
                        <input type=\"hidden\" name=\"database\" value=\"$database\" /> 
                        <input type=\"submit\" class=\"btn btn-primary\" name=\"btn-check\" value=\"Check cluster\"/>
                    </div>
                </div>
            </form>\n";
}
else
{
     print "Data submitted: <pre>".print_r($_POST,1)."</pre><br />\n";
    print "Oops:!!<br />\n";
}

function show_error($message,$post)
{
    session_start();

    $_SESSION['post'] = $post;
    print "<b>$message</b><br />\n";
    print "<form action=\"submit.php\" method=\"post\" >\n";
    print "<input type=hidden name=message value=\"$message\" />\n";
    print "<input type=submit value=\"back and check your data\" />\n";
    print "</form>\n";
    print "</div>\n</body>\n";
    
}
function check_connect($username,$password,$localhost,$database)
{  
     $dsn = mysqli_connect($localhost,$username,$password,$database)  or die("error connect mysql".mysqli_errno());
     $check_db = mysqli_select_db($dsn,$database) ;
    // check connection details
     print "<h4>Try to connect to database $database ...</h4>\n";
    if(!$check_db)
    {
        // if connection details incorrect show error
         print "<pre>Oops ! Unable to connect to Database $database.<br />Please check your credentail :<br />\n";
        print "<ul><li>username: $username</li>
        <li>password: $password</li>
        <li>localhost: $localhost</li>
        <li>database: $database</li></ul>\n";
        print " and database exist !!<br />\n";
        $data=0;
    }
    else
    {
       print "<pre>Correct ! database information provided are valid.</pre><br />" ;
       $data =1;
    }
    
    return $data;
}

function do_sql($username,$password,$localhost,$database,$query)
{  
     $dsn = mysqli_connect($localhost,$username,$password,$database)  or die("error connect mysql $localhost,$username,$password,$database ".mysqli_errno());
     $check_db = mysqli_select_db($dsn,$database) ;
 
    // check connection details
    if(!$check_db)
    {
        // if connection details incorrect show error        
        print "<pre>Oops ! Unable to connect to Database $database.<br />Please check your credentail '$check_db':<br />\n";
        print "<ul><li>username: $username</li>
        <li>password: $password</li>
        <li>localhost: $localhost</li>
        <li>database: $database</li></ul>\n";
        print " and database exist !!<br />\n";
        $data=0;
    }
    else
    {
      #   print "<pre>Correct ! database information provided are valid.'$query'</pre><br />" ;
       if (!mysqli_query($dsn,$query) )
       {
           print  "Error on query: $query <br /> : (" . $mysqli->errno . ") " . $mysqli->error;

       }
        $data =1;
    }
    
    return $data;
}

?>


</div>

</body>
</html>
