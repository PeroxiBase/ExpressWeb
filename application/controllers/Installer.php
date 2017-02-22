<?php
/**
* The Expression Database.
* Installer Class
*
* This class perfomr configuration
*
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package expressionWeb
*@subpackage     Controller
*/
defined('BASEPATH') OR exit('No direct script access allowed');
class Installer extends CI_Controller {
     
    public function index()
    {
            //
            $id = $this->session->user_id  ;
            $username = $this->session->username;
            
                 $data = array(
                  'title'=>"The Expression Web: Installation",
                  'contents' => "install/install",
                  );
                  $this->load->view("templates/template", $data);
    }
		
    public function process()
    {
        $error = 0;

        if (isset($_POST['btn-install'])) 
        {
           
            print "Data submitted: <pre>".print_r($_POST,1)."</pre><br />\n";
            // validation
           if ( $_POST['inputDBhost'] == '' || $_POST['inputDBname'] == '' || $_POST['inputSiteurl'] == '' &&
               (
                   ($_POST['inputRDBRootName']  == ''|| $_POST['inputRDBRootPassword'] == '' ) ||
                   ( $_POST['inputDBusername'] == '' ||  $_POST['inputDBpassword'] == '' )
                )  || $_POST['inputAppfolder'] == '' || 
                    $_POST['inputSystemfolder'] == '' || 
                    ($_POST['inputAppfolder'] == $_POST['inputSystemfolder'])) 
           {
                $error = 1;
                 $message = "Please provide database informations !!<br />\n";
                print $message;
                print "<form action=\"index.php\" method=post>\n";
                print "<input type=hidden name=post value='$_POST' />\n";
                print "<input type=hidden name=message value='$message' />\n";
                print "<input type=submit value='back and check your data' />\n";
                print "</form>\n";
                print "</div>\n</body>\n";
                $success=0;
                
                exit;
            } 
            else
            { 
                
                @$con = mysql_connect($_POST['inputDBhost'], $_POST['inputDBusername'], $_POST['inputDBpassword']);
                @$db_selected = mysql_select_db($_POST['inputDBname'], $con);
        
                if (!$con) 
                {
                    $error = 1;
                } 
                else if (!$db_selected)
                {  
                    $error = 1;
                } 
                else 
                {                 
                    // setting site url
                    $file_config = "../application/config/config.php";
                    $content_config = file_get_contents($file_config);
                    $content_config .= "\n\$config['domain'] = '".$_POST['inputSiteurl']."';";
                    $content_config .= "\n\$config['encryption_key'] = '".$_POST['InputEncryption_key']."';";
                    file_put_contents($file_config, $content_config);
        
                    // setting database
                    $file_db = "../application/config/database.php";
                    $content_db = file_get_contents($file_db);
                    if(isset($_POST['inputRDBRootName']) && isset($_POST['inputRDBRootPassword']))
                    {
                        //root level
                        $Rusername = $_POST['inputRDBRootName'];
                        $Rpassword = $_POST['inputRDBRootPassword'];
                        $hostname = $_POST['inputRDBhost'];
                        $database = $_POST['inputRDBname'];
                        $username = $_POST['inputRDBusername'];
                        $password = $_POST['inputRDBpassword'];
                        // check if credentials are valid
                        $check_db=check_connect($Rusername,$Rpassword,$hostname,$database);
                        if($check_db==1)
                        {
                            $content_db .= "\n\$db['default']['hostname'] = '$hostname';";
                            $content_db .= "\n\$db['default']['database'] = '$database';";
                            if(isset($username) && isset($password))
                            {
                               $content_db .= "\n\$db['default']['username'] = '$username';";
                               $content_db .= "\n\$db['default']['password'] = '$password';";
                               $create_db_user = 1;
                            }
                            else
                            {
                               $content_db .= "\n\$db['default']['username'] = '$Rusername';";
                               $content_db .= "\n\$db['default']['password'] = '$Rpassword';";   
                               $create_db_user = 0;
                            }
                            file_put_contents($file_db, $content_db);     
                           
                            // import sql script and create database and tables
                            $file_sql = "sql/express_db.sql";
                            $content_sqldb = file_get_contents($file_sql);
                            $this->load->database();
                            if ($this->db->simple_query($content_sqldb))
                            {
                                    echo "Success! Your database $database have been created<br />";
                                    $success=1;
                            }
                            else
                            {
                                    print "Query failed! Unable to create database $database<br />";
                                    print "<form action=\"index.php\" method=post>\n";
                                    print "<input type=hidden name=post value=$_POST />\n";
                                    print "<input type=submit value=\"back and check your data\" />\n";
                                    print "</form>\n";                            
                                    print "</div>\n</body>\n";
                                    $success=0;
                                    exit;
                            }
                            // create specific user and grant
                            if($create_db_user && $success)
                            {
                                $sql_create_user="CREATE USER '$username'@'$hostname' 
                                        IDENTIFIED BY '$password';";
                                $sql_grant =" GRANT ALL PRIVILEGES ON $database.* TO '$username'@'$hostname' 
                                        IDENTIFIED BY PASSWORD '$password' WITH GRANT OPTION;";
                                /////////////////////////////////
                                if ($this->db->simple_query($sql_create_user))
                                {
                                        echo "Success! user $username have been created<br />$sql_create_user<br />";
                                        $success=1;
                                }
                                else
                                {
                                        print "Query failed! Unable to create user $username<br />";
                                        print "<form action=\"index.php\" method=post>\n";
                                        print "<input type=hidden name=post value=$_POST />\n";
                                        print "<input type=submit value=\"back and check your data\" />\n";
                                        print "</form>\n";
                                        print "</div>\n</body>\n";
                                        $success=0;
                                        exit;
                                }
                                /////////////////////////////////
                                if ($this->db->simple_query($sql_grant))
                                {
                                        echo "Success! GRANTS for user $username have been created<br />$sql_grant<br />";
                                        $success=1;
                                }
                                else
                                {
                                        print "Query failed! Unable to create GRANTS for user $username<br />";
                                        print "<form action=\"index.php\" method=post>\n";
                                        print "<input type=hidden name=post value=$_POST />\n";
                                        print "<input type=submit value=\"back and check your data\" />\n";
                                        print "</form>\n";
                                        print "</div>\n</body>\n";
                                        $success=0;
                                        exit;
                                }
                            }
                            
                        }
                        else
                        {
                            // credentials are wrong. stop
                            print "Query failed! Unable to create database $database<br />";
                            print "<form action=\"index.php\" method=post>\n";
                            print "<input type=hidden name=post value=$_POST />\n";
                            print "<input type=submit value=\"back and check your data\" />\n";
                            print "</form>\n";
                            print "</div>\n</body>\n";
                            $success=0;
                            exit;
                        }
                    }
                    elseif(isset($_POST['inputDBusername']) && isset($_POST['inputDBpassword']))
                    {
                        // db already created
                        $username = $_POST['inputDBusername'];
                        $password = $_POST['inputDBpassword'];
                        $hostname = $_POST['inputDBhost'];
                        $database = $_POST['inputDBname'];
                        $check_db=check_connect($username,$password,$hostname,$database);
                        if($check_db==1)
                        {                    
                           $content_db .= "\n\$db['default']['hostname'] = '$hostname';";
                           $content_db .= "\n\$db['default']['username'] = '$username';";
                           $content_db .= "\n\$db['default']['password'] = '$password';";
                           $content_db .= "\n\$db['default']['database'] = '$database';";
                           file_put_contents($file_db, $content_db);
                        }
                        else
                        {
                            // credentials are wrong. stop
                            print "Query failed! Unable to check database $database<br />";
                            print "<form action=\"index.php\" method=post>\n";
                            print "<input type=hidden name=post value=$_POST />\n";
                            print "<input type=submit value=\"back and check your data\" />\n";
                            print "</form>\n";
                            print "</div>\n</body>\n";
                            $success=0;
                            exit;
                        }                
                    }
                    else
                    {
                        $message = "Please provide database informations !!<br />";
                        print $message;
                        print "<form action=\"index.php\" method=post>\n";
                        print "<input type=hidden name=post value=$_POST />\n";
                        print "<input type=hidden name=message value=$message />\n";
                        print "<input type=submit value=\"back and check your data\" />\n";
                        print "</form>\n";
                        print "</div>\n</body>\n";
                        $success=0;
                        exit;
                    }
                    // configure config/expressDb.php
                     $file_config = "../application/config/expressDb.php";
                    $content_config = file_get_contents($file_config);
                    $content_config .= "\n\$config['input_apache_user'] = '".$_POST['input_apache_user']."';";
                    $content_config .= "\n\$config['input_header_name'] = '".$_POST['input_header_name']."';";
                    $content_config .= "\n\$config['input_web_path'] = '".$_POST['input_web_path']."';";
                    $content_config .= "\n\$config['input_network'] = '".$_POST['input_network']."';";
                    $content_config .= "\n\$config['input_cluster_env'] = '".$_POST['input_cluster_env']."';";
                    $content_config .= "\n\$config['input_cluster_app'] = '".$_POST['input_cluster_app']."';";
                    $content_config .= "\n\$config['input_work_cluster'] = '".$_POST['input_work_cluster']."';";
                    
                    file_put_contents($file_config, $content_config);
                    
                    //
                    
                    // setting cluster folder name
                    $file_index = "../index.php";
                    $content_index = str_replace("\$system_path = 'system';", "\$system_path = '".$_POST['inputSystemfolder']."';", file_get_contents($file_index));
                    file_put_contents($file_index, $content_index);
                    $content_index = str_replace("\$application_folder = 'application';", "\$application_folder = '".$_POST['inputAppfolder']."';", file_get_contents($file_index));
                    file_put_contents($file_index, $content_index);
        
                    // rename app folder
                    $index = str_replace('install', '', dirname(__FILE__));
                    rename($index.'application', $index.$_POST['inputAppfolder']);
                    rename($index.'system',      $index.$_POST['inputSystemfolder']);
                    header('location:../');
                 }
           } 
        }
        else
        {
             print "Data submitted: <pre>".print_r($_POST,1)."</pre><br />\n";
            print "Oops:!! <br />";
        }

    }
    
    public function check_connect($username,$password,$localhost,$database)
    { 
        $dsn = "mysqli://$username:$password@$localhost/$database";
        $CI =& get_instance();
            $CI->load->database();
        // Load database and dbutil
        $CI->load->database($dsn);
        $CI->load->dbutil();
         
        // check connection details
        if(! $CI->dbutil->database_exists("$database"))
        {
            // if connection details incorrect show error
            echo 'Incorrect database information provided';
            $data =0;
        }
        else
        {
            $data=1;
        }
        return $data;
    }

}
