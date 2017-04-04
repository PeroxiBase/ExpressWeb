<?php
/**
* The Expression Database.
*
*  Generic Class 
*
*This class contains functions for users and tables management
*
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<picard.sylvain3@gmail.com>
*@version 1.0
*@package        ExpressWeb
*@subpackage     Model
*/

defined('BASEPATH') OR exit('No direct script access allowed');
class Generic extends CI_Model 
{

        public $title;
        public $content;
        public $date;
        public $nbr;
       
        /**  
        * Class constructor
        *
        * Initialize Generic class
        *
        * @return void
        */
        public function __construct()
        {
                // Call the CI_Model constructor
                parent::__construct();
                $this->database = $this->db->database;
        }
        	
        /**
        * function  get_query_row($sql_query)
        *   process sql query. Return single object result
        *
        * @param string $sql_query 
        * @return ressource  sql result as single result
        */          
        public function get_query_row($sql_query)
        {
            $query= $this->db->query($sql_query);
            return $query->row();
        }
        
        /**
        * function  get_query_obj($sql_query)
        *   process sql query. Return Array
        *
        * @param string $sql_query 
        * @return object sql result as object (query, nbr record, sql_result)
        */  
        public function get_query_obj($sql_query)
        {
             $result = new stdClass();
            $query= $this->db->query($sql_query);
            if($query->num_rows() ==0) 
            {
                $result->nbr = 0;
                $result->result = "";
                $result->sql = $sql_query;
            }
            else 
            {
                $result->nbr = $query->num_rows();
                $result->result = $query->result();
                $result->sql = $sql_query;
            }
            return $result;
        }
        
	/**
        * function  get_table_members($id='')
        *   recover usable table
        *       if $id recover usable table for user $id
        *
        * @param integer $id optional 
        * @return object sql result as array (query, nbr record, sql_result)
        */  
        public function get_table_members($id='')
        {
            $result = new stdClass();
            $sql_query= "SELECT IdTables,TableName ,MasterGroup ,Organism,Submitter,
                                Root,Child,t.version,comment,tg.group_id ,g.name as group_name 
                        FROM tables as t
                        INNER JOIN tables_groups as tg on tg.table_id=IdTables
                        INNER JOIN users_groups as u on u.group_id= tg.group_id
                        INNER JOIN groups AS g ON g.id = MasterGroup
                        ";             
             if($id !="" ) $sql_query .= "            WHERE user_id=$id";
             $sql_query .= "    GROUP BY IdTables";
            $query= $this->db->query($sql_query);
            $result->sql = $sql_query;
            #log_message('debug', "get_table_members id $id \n sql: $result->sql" );
            $result->nbr = $query->num_rows();
            $result->result = $query->result_array();
            return $result;
        }

        
        public function get_child($IdTables)
        {
            $result = new stdClass();
            $sql_query= "SELECT IdTables,TableName 
                    FROM tables as t
                    WHERE child='$IdTables' and TableName 
                    REGEXP 'Cluster$' ORDER BY TableName;";
            $query= $this->db->query($sql_query);
            $result->sql = $sql_query;
            $result->nbr = $query->num_rows();
            $result->result = $query->result();
            return $result;
        }
        
        /**
        * function  get_tables($id='',$isRoot='')
        *   list all the tables or only table with IdTables $id
        *       Option $isRoot to get Root table or Child (0)
        * @param integer $id_orga          
        * @param integer $isRoot     
        * @return object sql result as array (query, nbr record, sql_result)
        */  
        public function get_tables($id='',$isRoot='')
        {
            $result = new stdClass();
            $sql_query= "SELECT IdTables,TableName ,MasterGroup,name ,o.Organism ,idOrganisms,
                                Submitter,version ,comment,Root,Child
                        FROM tables as t
                        INNER JOIN Organisms as o on idOrganisms=t.Organism
                        INNER JOIN groups as g on g.id= MasterGroup ";
            if($id !="" ) $sql_query .= " WHERE IdTables ='$id' ";
            if($isRoot !="" ) $sql_query .= " WHERE Root ='$isRoot' ";
            $sql_query .= " ORDER by TableName ";
            $query= $this->db->query($sql_query);
            $result->sql = $sql_query;
            $result->nbr = $query->num_rows();
            $result->result = $query->result_array();
            return $result;
        }
        
        /**
        * function  get_annotation($id='')
        *   list all annotation tables or only annotation table with IdTables $id
        *
        * @param integer $id_orga
        * @return object sql result as array (query, nbr record, sql_result)
        */  
        public function get_annotation($id='')
        {
            $result = new stdClass();
            $sql_query= "SELECT IdTables,TableName ,MasterGroup,name ,o.Organism ,idOrganisms,
                                Submitter,version ,comment,Root,Child
                        FROM tables as t
                        INNER JOIN Organisms as o on idOrganisms=t.Organism
                        INNER JOIN groups as g on g.id= MasterGroup
                        WHERE TableName like 'Annotation_%' ";
            if($id !="" ) $sql_query .= " WHERE IdTables ='$id' ";
            $sql_query .= " ORDER by TableName ";
            $query= $this->db->query($sql_query);
            $result->sql = $sql_query;
            $result->nbr = $query->num_rows();
            $result->result = $query->result_array();
            return $result;
        }
        
	/**
        * function get_tables_organism($id_orga)
        *       get tables list for organism $id_orga
        *
        * @param integer $id_orga
	* @return object sql result as array (query, nbr record, sql_result)
        **/
        public function get_tables_organism($id_orga)
        {
            $result = new stdClass();
            $sql_query= "SELECT IdTables,TableName ,MasterGroup,name ,o.Organism ,idOrganisms,Submitter,version ,comment
                        FROM tables as t
                        INNER JOIN Organisms as o on idOrganisms=t.Organism
                        INNER JOIN groups as g on g.id= MasterGroup 
                        WHERE idOrganisms ='$id_orga' ";
            $sql_query .= " ORDER by TableName ";
            $query= $this->db->query($sql_query);
            $result->sql = $sql_query;
            $result->nbr = $query->num_rows();
            $result->result = $query->result_array();
            return $result;
        }
        
	/**
        * function get_users($id='')
        *       get list of users or user with id=$id
	*
	* @param integer $id
	* @return object sql result as array (query, nbr record, sql_result)
        **/
        public function get_users($id='')
        {
            $result = new stdClass();
            $sql_query= "SELECT id, username, first_name, last_name, company,email
                FROM users";
            if($id !="" ) $sql_query .= " WHERE id ='$id' ";
            $query= $this->db->query($sql_query);
            $result->sql = $sql_query;
            $result->nbr = $query->num_rows();
            $result->result = $query->result_array();
            return $result;
        }
        
	/**
        * function get_users_group($id='')
        *       get list of users groups  or user with id=$id
	*
	* @param integer $id
	* @return object sql result as array (query, nbr record, sql_result)
        **/
        public function get_users_group($id='')
        {
            $result = new stdClass();
            $sql_query= "SELECT u.id,user_id,group_id,name 
                        FROM  users_groups as u
                        INNER join groups as g on g.id= group_id ";
            if($id !="") $sql_query .= "WHERE user_id='$id' ";
            $sql_query .= "ORDER by user_id";
            $query= $this->db->query($sql_query);
            $result->sql = $sql_query;
            $result->nbr = $query->num_rows();
            $result->result = $query->result_array();
            return $result;
        }
        
	/**
        * function get_groups()
        *       get list of avalaible groups
	*       generate html select list
	*
        * @param integer $id
	* @return string $Data  html select list
        **/
        public function get_groups()
        {
            $sql="SELECT name FROM groups ORDER BY name";
            $result=$this->db->query($sql)->result();
            $Data = "<select id=\"master_group\" name=\"master_group\">";
            $Data .= "<option value=\"\">&nbsp; </option>";
            $void=array('Admin','members','Guest');
            foreach ($result as $row)
            {
                $value =$row->name;
                if(!in_array($value,$void))
                $Data .= "<option value=\"$value\" >$value</option>";
            }
            $Data .= "</select>";
            return $Data;
        }
        
	
        /**
        * function  get_table_group($id='')
        *   list all group permissions for all tables or table table_id=$id
        *
        * @param integer $id
	* @return object sql result as array (query, nbr record, sql_result)
        */  
        public function get_table_group($id='')
        {
            $result = new stdClass();
                        
            $sql_query= "SELECT t.id,table_id,group_id,name ,tablename,child
                        FROM  tables_groups as t
                        INNER join tables on IdTables=table_id
                        INNER join groups as g on g.id= group_id ";
            if($id !="") $sql_query .= "WHERE table_id='$id' ";
            
            $sql_query .= "ORDER by tableName";
            $query= $this->db->query($sql_query);
            $result->sql = $sql_query;
            $result->nbr = $query->num_rows();
            $result->result = $query->result_array();
            return $result;
        }
	
        /**
        * function  get_organisms($id='')
        *   list all organism or only organism $id
        *
        * @param integer $id 
        * @return object sql result as array (query, nbr record, sql_result). Single result ($id!="") or array
        */  
        public function get_organisms($id='')
        {
            $result = new stdClass();
            $sql_query= "SELECT * FROM Organisms ";
             if($id !="") $sql_query .= "WHERE idOrganisms='$id' ";
            $query= $this->db->query($sql_query);
            $result->sql = $sql_query;
            $result->nbr = $query->num_rows();
            if($id !="") $result->result = $query->row();
            else $result->result = $query->result_array();
            return $result;
        }        
	
        /**
        * function  get_removable_table()
        *   list  all tables in DB
        *
        * @return object sql result as array (query, nbr record, sql_result). 
        */  
        public function get_removable_table($option='')
        {
            $result = new stdClass();
            $sql_query= "SELECT TABLE_NAME,ENGINE,TABLE_ROWS,DATA_LENGTH,
                                CREATE_TIME,IdTables,TableName
                        FROM information_schema.TABLES as inf
                        LEFT JOIN tables as clu on clu.TableName=inf.TABLE_NAME
                        WHERE TABLE_SCHEMA='$this->database' ";
            if($option !="" ) $sql_query .= " AND $option ";
            $sql_query .= " ORDER BY TableName";
            $query= $this->db->query($sql_query);
            $result->sql = $sql_query;
            $result->nbr = $query->num_rows();
            $result->result = $query->result_array();
            return $result;
        }
        
	
        /**
        * function  get_table_info($table_name)
        *   get tableName from information_schema
        *
        * @param string $table_name 
        * @return object sql result as array (query, nbr record, sql_result(object) ). 
        */  
        public function get_table_info($table_name)
            {
                $result = new stdClass();
                $sql_query= "SELECT TABLE_NAME FROM information_schema.TABLES 
	                    WHERE TABLE_SCHEMA='$this->database'
	                            AND TABLE_NAME LIKE '$table_name%'";
                $query= $this->db->query($sql_query);
                $result->sql = $sql_query;
                $result->nbr = $query->num_rows();
                $result->result = $query->result();
                return $result;
            }

        /**
        * function get_last_sub($table_name)
        * used when creating sub_table (Ctrl Display)
        * got greater sub_table created number
        *
        * @param string $table_name
        * @return object sql result as array (query, nbr record, sql_result) 
        */  
        public function get_last_sub($table_name)
        {
            $result = new stdClass();
            $sql_query = "SELECT TableName FROM tables 
                WHERE TableName LIKE '$table_name%' AND Root='1'
                ORDER by TableName DESC
                LIMIT 0,1";
            $query= $this->db->query($sql_query);
            $result->sql = $sql_query;
            $result->nbr = $query->num_rows();
            $result->result = $query->row();
            return $result;
        }

	/**
        *function is_table_lock($table_name,$single='')
        *       check if tables are in use
	*
        * @param string $table_name
        * @return object sql single result as object (query, nbr record, sql_result)
        **/
       public function is_table_lock($table_name,$single='')
      {
          # return
          #Database 	Table 	                      In_use 	Name_locked 
          #$this->database 	Demo_Demo_Rhizo_0_2_Order 	0 	0
          $result = new stdClass();
          if($single==1)
          {
              $sql_query= "SHOW OPEN TABLES FROM $this->database LIKE '$table_name'";
              $query= $this->db->query($sql_query);
              $result->sql = $sql_query;
               $result->nbr = $query->num_rows();
              $result->result = $query->row();
          }
          else
          {
              $sql_query= "SHOW OPEN TABLES FROM $this->database LIKE '$table_name%'";
              $query= $this->db->query($sql_query);
              $result->sql = $sql_query;
              $result->nbr = $query->num_rows();
              $result->result = $query->row();
          }
          return $result;
      }



    	/**
        *function remove_ref_tables 
        *
	*        Remove reference in Tables for specific table, used if error
        **/
	
	public function remove_ref_tables($tableID){
		$sql_query="DELETE FROM tables WHERE TableName='$tableID'";
		# log_message('debug', "genric::remove_ref_tables:: sql: $sql_query\n" );
	        $query= $this->db->query($sql_query);
	}

	/**
        *function get_analysis($id)
        * get statsistics for user $id
	*	
        * @param string $id
        * @return object sql result as object (query, nbr record, sql_result)
        **/
        public function get_analysis($id)
        {
            $result = new stdClass();
            $sql_query= "SELECT * FROM statistics WHERE user_id='$id'";
            $query= $this->db->query($sql_query);
            $result->sql = $sql_query;
            $result->nbr = $query->num_rows();
            $result->result = $query->result();
            return $result;
        }
        
        /**
        *function who_online($time='')
        * check users connected on website
        *
        * @param string $time
        * @return object $Data and sql result as object (query, nbr record, sql_result)
        **/
        public function who_online($time='')
        {
            if($time=='') $time=5;
            $result = new stdClass();
            $sql_query= "SELECT ip_address,timestamp,
                            FROM_unixtime(timestamp) as date, CAST(data AS CHAR(10000) CHARACTER SET utf8) as data
                            FROM ci_sessions
                            WHERE timestamp > unix_timestamp(DATE_SUB(now(), INTERVAL $time MINUTE));";
            $query= $this->db->query($sql_query);
            $result->sql = $sql_query;
            $result->nbr = $query->num_rows();
            $result->result = $query->result();
            $resultArr = array();
            
            foreach($result->result as $row)
            {
                #http://forum.codeigniter.com/thread-61330.html
                $session_data = $row->data;
                $return_data = array();  // array where you put your "BLOB" resolved data
                 
                $offset = 0;
                while ($offset < strlen($session_data)) 
                {
                   if (!strstr(substr($session_data, $offset), "|")) 
                    {
                      throw new Exception("invalid data, remaining: " . substr($session_data, $offset));
                    }
                      $pos = strpos($session_data, "|", $offset);
                      $num = $pos - $offset;
                      $varname = substr($session_data, $offset, $num);
                      $offset += $num + 1;
                      $data = unserialize(substr($session_data, $offset));
                      $return_data[$varname] = $data;  
                      $offset += strlen(serialize($data));
                  }
                  if(isset($return_data['username']))
                      $username =$return_data['username'] ;
                  else
                $username ="visitor not logged";
                $resultArr[$username]['ip_address'] = $row->ip_address ;
                $Date =$row->date ;
                list($Day,$Time) = explode(' ',$Date);
                $resultArr[$username]['date'] = $Day ;
                $resultArr[$username]['time'] = $Time ;
                $resultArr[$username]['timestamp'] = $row->timestamp ;
                $resultArr[$username]['username'] = $username ;
                
            }
            $result->Data = $resultArr;
            return $result;
        }
        
        /**
        *function create_Sql_table($DbInfo,$create_table_inDb)
        *   create table from tabular file upload
        *    
        * @param array $DbInfo 
        * @param string $create_table_inDb  sql request
        * @return object $Data informational result of query
        **/
        public function create_Sql_table($DbInfo,$create_table_inDb)
        {
             $master_group = $DbInfo['master_group'];
             $table_name = $DbInfo['table_name'];
             $IdOrganism = $DbInfo['IdOrganism'];
             $version  = $DbInfo['version'];
             $comment = $DbInfo['comment'];
             $file_name = $DbInfo['file_name'];
             $force_dump = $DbInfo['force_dump'];
            $Data = new stdclass;
            $Data->info = "Requetes SQL execut&eacute;s<br />";
           # $Data->info .= $insert_data;
            ################ DROP TABLE #################################
            if($force_dump==1)
            {
                 $sql_data_CT1  = "DROP TABLE IF EXISTS $table_name ;";
                
                $this->db->trans_begin();
                $this->db->db_debug = FALSE; 
                $this->db->query($sql_data_CT1); 
                if ($this->db->trans_status() === FALSE)
                {
                        $this->db->trans_rollback(); 
                        $Data->info .= "<br />Impossible de detruire la Table  $table_name<br />";                
                }
                else
                {
                    $this->db->trans_commit();
                    $Data->info .= "<br />Table  $table_name dropped<br />";
                }
            }
             ################ CREATE TABLE #################################
            #$create_table_inDb= $this->db->query($table_desc); 
            $this->db->trans_begin();
            $this->db->db_debug = FALSE; 
            $this->db->query($create_table_inDb); 
            if ($this->db->trans_status() === FALSE)
            {
                    $Data->info .= print_r($this->db->last_query(), TRUE);
                    $this->db->trans_rollback(); 
                    $Data->info .= "<br />Impossible de cr&eacute;er la Table  $table_name<br />";                
            }
            else
            {
                $this->db->trans_commit();
                $Data->info .= "<br />Table  $table_name cr&eacute;&eacute;<br />";
            }
            
            ####################### Update  table information
            $MasterGroup= $this->db->query("select id from groups where name='$master_group'")->row();
            $Master_Group= $MasterGroup->id;
            $submitter = $this->session->userdata('identity');
            #$organismId=1;
           # print "MasterGroup  $Master_Group /".print_r($MasterGroup,1)."<br />";
           
           $UpdateData =array('table_name' => "$table_name",'Master_Group' => "$Master_Group",
                              'IdOrganism' => "$IdOrganism",'submitter' => "$submitter",
                              'version' => "$version",'comment' => "$comment",
                              'file_name'  => "$file_name",'Root' => "1",'Child'=>'0');
           
           $update_tables= $this->update_table_info($UpdateData);
           
           $Data->info .= $update_tables->info;
           $IdTable= $update_tables->IdTable;
             ####################### Update  table_groups information
              $this->db->query("REPLACE INTO tables_groups (table_id ,group_id) VALUES ( '$IdTable', '$Master_Group')");
              ##############  Allow Admin  if $MasterGroup != 1!! ######################
              if ($MasterGroup != 1)
              {
                  $this->db->query("REPLACE INTO tables_groups (table_id ,group_id) VALUES ( '$IdTable', '1')");
              }
              
              $Data->info .= "\t\t table Tables tables_groups a jour IdTable $IdTable<hr />";
            return $Data;
        }
        
        
        /**
        *function   get_Idtable($tableName)
        *   get table IdTables
        *
        * @param string $tableName
        * @return object  sql single result as object (query, nbr record, sql_result)
        **/
        public function get_Idtable($tableName)
        {
            $result = new stdClass();
            $sql_query= "SELECT IdTables FROM tables WHERE TableName='$tableName'";
            $query= $this->db->query($sql_query);
            $result->sql = $sql_query;
            $result->nbr = $query->num_rows();
            $result->result = $query->row();
            
            return $result;
        }
        
        /**
        *function update_table_info($UpD)
        *   update table info in ref table 'tables'
        *
        * @param string $UpD
        * @return object  $Data informational result of query
        **/
        public function update_table_info($UpD)
        {
            $Data = new stdclass;
            $update_tables= "REPLACE INTO tables (
                                    TableName ,
                                    MasterGroup, Organism , Submitter, version, comment, original_file,Root,Child)
                             VALUES ('".$UpD['table_name']."','".$UpD['Master_Group']."','".$UpD['IdOrganism']."',
                                    '".$UpD['submitter']."','".$UpD['version']."','".addslashes($UpD['comment'])."',
                                    '".addslashes($UpD['file_name'])."','".$UpD['Root']."','".$UpD['Child']."');";
            $table_name = $UpD['table_name'];
            # print "update_table_info <br />update_tables $update_tables<br />";
            $this->db->trans_begin();
            $this->db->db_debug = FALSE; 
            $this->db->query($update_tables); 
            if ($this->db->trans_status() === FALSE)
            {
                    $Data->info .= print_r($this->db->last_query(), TRUE);
                    $this->db->trans_rollback(); 
                    $Data->info .= "<br />Impossible de MAJ Tables $table_name  $update_tables<br />";
                    $Data->info .= "";
                    $this->session->set_flashdata('message', $Data);
                   # redirect("create_table/load_csv");
            }
            else
            {
                 $this->db->trans_commit();
                 #$IdTable= $this->db->insert_id();
                 $IdTableQ= $this->db->query("SELECT IdTables FROM tables where TableName='$table_name'")->row();
                 $IdTable= $IdTableQ->IdTables;
                 $Data->IdTable = $IdTable;
                 $Data->info .= "<br />table Tables a jour IdTable $IdTable . Tables updated: $update_tables<hr />";
                
            }
            
            return $Data;
        }
    
        /**
        *function update_table_group_info($UpD)
        *   update table info in ref table 'tables_groups'
        *  only for dataset
        * @param string $UpD
        * @return object  $Data informational result of query (Idtable,info)
        **/
        public function update_table_group_info($UpD)
        {
            $Data = new stdclass;
            foreach($UpD as $key=>$values)
            {
                $update_tables= "REPLACE INTO tables_groups (table_id ,group_id)
                                 VALUES ('".$UpD[$key]['table_id']."','".$UpD[$key]['group_id']."');";
                $table_name = $UpD[$key]['table_name'];
                # print "update_table_info <br />update_tables $update_tables<br />";
                $this->db->trans_begin();
                $this->db->db_debug = FALSE; 
                $this->db->query($update_tables); 
                if ($this->db->trans_status() === FALSE)
                {
                        $Data->info .= print_r($this->db->last_query(), TRUE);
                        $this->db->trans_rollback(); 
                        $Data->info .= "<br />Impossible de MAJ Tables $table_name  $update_tables<br />";
                        $Data->info .= "";
                        $this->session->set_flashdata('message', $Data);
                       # redirect("create_table/load_csv");
                }
                else
                {
                    
                    $this->db->trans_commit();
                     #$IdTable= $this->db->insert_id();
                     $IdTableQ= $this->db->query("SELECT IdTables FROM tables where TableName='$table_name'")->row();
                     $IdTable=$IdTableQ->IdTables;
                     $Data->IdTable = $IdTable;
                     $Data->info .= "<br />MAJ group ".$UpD[$key]['group_id']."  for  ".$UpD[$key]['table_id']." . Tables  $table_name<hr />";
                    
                }
            }
            return $Data;
        }
        
        /**
        *function insert_Sql_data($insert_data,$DbInfo)
        *
        *
        * @param string $insert_data    
        * @param array $DbInfo
        * @return object  $Data informational result
        **/
        public function insert_Sql_data($insert_data,$DbInfo)
        {
            $Data = new stdclass;
            $table_name = $DbInfo['table_name'];
            $line = $DbInfo['line'];
            $Data->info = "Requetes SQL execut&eacute;s<br />";
            ################ INSERT DATA INTO TABLE #################################
            #$insert_data = $this->db->query($table_insert);
            $this->db->trans_begin();
            $this->db->db_debug = FALSE; 
            $this->db->query($insert_data); 
            if ($this->db->trans_status() === FALSE)
            {
                    $Data->info .= print_r($this->db->last_query(), TRUE);
                    $this->db->trans_rollback(); 
                    $Data->info .= "<br />Impossible d'ins&eacute;r&eacute;er des donn&eacute;es dans  la Table  $table_name<br />";
                    $this->session->set_flashdata('message', $Data);
                    redirect("create_table/load_csv");
            }
            else
            {            
                $this->db->trans_commit();
                $Data->info .= "\t\t  $line lignes ins&eacute;r&eacute;es <hr />";
            }
            return $Data;
        }
            
        /**
        *function update_Tables_On_Clustering($filename,$seuil,$id_organism)
        *   create ref in 'tables' for tables created on cluster from dataset $filename
        *
        * @param string $filename Dataset name
        * @param string $seuil Threshold
        * @param array $DbInfo
        * @return object $result 
        **/
         public function update_Tables_On_Clustering($filename,$seuil,$id_organism)
         {
            $submitter = $this->session->username;
            $getChildId = $this->get_Idtable($filename);
            $ChildId=$getChildId->result->IdTables;
            $newName=$filename."_".$seuil."_Cluster";
            $UpdateData =array('table_name' => "$newName",'Master_Group' => "1",
                          'IdOrganism' => "$id_organism",'submitter' => "$submitter",
                          'version' => "1",'comment' => "Cluster file for table $filename",
                          'file_name'  => "$filename",
                          'Child' => "$ChildId");
            $updTable= $this->update_table_info($UpdateData);
            $newName=$filename."_".$seuil."_Order";
            $UpdateData =array('table_name' => "$newName",'Master_Group' => "1",
                          'IdOrganism' => "$id_organism",'submitter' => "$submitter",
                          'version' => "1",'comment' => "Order file for table $filename",
                          'file_name'  => "$filename",'Root' =>"0",
                          'Child' => "$ChildId");
            $updTable= $this->update_table_info($UpdateData);
            
         }
         
        /**
        *function hasTranscriptsAnnot($annot)
        *       test if annotation table contains gene or transcripts
        *       if gene name has version (.1,.2) it is a transcript
        * @param : annotation table name
        * @return object sql single result as object (len)
        **/
         public function hasTranscriptsAnnot($annot)
         {
            $result =new stdClass();
            $sql_query="SELECT count(*) as len FROM $annot WHERE Gene_Name REGEXP '[.period.][1-9]' ";
            $query=$this->db->query($sql_query);
            $result->sql = $sql_query;
            $result->nbr = $query->num_rows();
            $result->result = $query->row('len');
            return $result;
         }
    
        /**
        *function hasTranscriptsValues($values)
        *       test if values table contains gene or transcripts
        *       if gene name has version (.1,.2) it is a transcript
        *@param : values table name
        * @return object sql single result as object (len)
        **/
         public function hasTranscriptsValues($values)
         {
              $result =new stdClass();
             //  SELECT count(*) as len FROM Paroi_Euca_Fluidigm WHERE Gene_Name LIKE '%.%'  
            $sql_query="SELECT count(*) as len FROM $values WHERE Gene_Name REGEXP '[.period.][1-9]' ";
            $query=$this->db->query($sql_query);
            $result->sql = $sql_query;
            $result->nbr = $query->num_rows();
            $result->result = $query->row('len');
            return $result;
         }
    
    
        /**                 
        * function  extract_annot($annot_table,$data_table,$child)
        *   extract organism annotation from Annot_Id_organism join with user table $data_table
        *   and create a new annotation table with matches Id only . 
        *   Avoid load full annotation with small user tables
        * @param string $annot_table        original annotation table name
        * @param string $data_table         user data table name
        * @param integer $child             IdTables of $data_table. Used in 'tables' via fct info_tables for child dependencies right
        * @param integer $update            1 to truncate table 0 create new annotation 
        * @return integer 
        */      
         public function extract_annot($annot_table,$data_table,$child,$update="0")
         {
             $this->load->dbforge();
             $result = new stdClass();
             
             // Table Creation //
            $newName="Annotation_$data_table";	
            if(!$this->db->table_exists($newName))
            {
                 
                 $fields=array(
                    'annot_id' =>array('type' => 'INT','constraint' => 10, 
                                        'unsigned' => TRUE,
                                        'auto_increment' => TRUE),
                    'Gene_Name' =>   array('type'=>'VARCHAR','constraint'=>'15'),
                    'Analyse'   =>   array('type'=>'VARCHAR','constraint'=>'21'),
                    'Signature' =>   array('type'=>'VARCHAR','constraint'=>'18'),
                    'Description' => array('type'=>'VARCHAR','constraint'=>'255'),
                    'misc'      => array( 'type'=>'CHAR','constraint'=>'15'),                        
                 );
                
                
                 $this->dbforge->add_field($fields);
                 $this->dbforge->add_key('annot_id', TRUE);
                 $attributes = array('ENGINE' => 'MyISAM');
                 $this->dbforge->create_table($newName, FALSE, $attributes);
            }
            if ($this->db->table_exists($newName) && $update==1)
            {
                $this->db->truncate($newName);   
            }
             
            // TEST TRANSCRITS //
             $testTransAnnot = $this->hasTranscriptsAnnot($annot_table);	
             $testTransData = $this->hasTranscriptsValues($data_table);
             $testTransAnnotLen=$testTransAnnot->result;
             $testTransDataLen=$testTransData->result;
    
            // Si les annotations et les data ne sont pas des transcrits //
             if($testTransAnnotLen == 0 && $testTransDataLen == 0)
             {
                 $sql_query="SELECT A1.Gene_Name, Analyse, Signature,Description,misc
                         FROM $annot_table A1 
                         INNER JOIN $data_table A2 on A2.Gene_Name=A1.Gene_Name";
                         $test =1;
             }
            // Si les annotations et les data sont des transcrits //	
             elseif($testTransAnnotLen != 0 && $testTransDataLen != 0)
             {
                $sql_query="SELECT A1.Gene_Name, Analyse, Signature,Description ,misc
                        FROM $annot_table A1 
                        INNER JOIN $data_table A2 on A2.Gene_Name=A1.Gene_Name";
                        $test =2;
            }
            // Si les annotations sont des transcrits mais pas les valeurs //
             elseif($testTransAnnotLen != 0 && $testTransDataLen == 0)
             { 	
                 $sql_query= "SELECT A1.Gene_Name, Analyse, Signature,Description,misc 
                         FROM $annot_table A1
                         INNER JOIN $data_table A2 on concat(A2.Gene_Name,'.1')=A1.Gene_Name";
                         $test =3;
             }
            // Si les annotations ne sont pas des transcripts mais les valeurs oui //
            elseif($testTransAnnotLen == 0 && $testTransDataLen != 0)
            {
                // delete two last character from transcript //
                  $sql_query="SELECT concat(A1.Gene_Name,'.',right(A2.Gene_Name,((CHAR_LENGTH(A2.Gene_Name))-(InStr(A2.Gene_Name,'.'))))) as Gene_Name, 
                         Analyse, Signature,Description,misc 
                         FROM $annot_table A1
                         INNER JOIN $data_table A2 on 
                         concat(A1.Gene_Name,'.',right(A2.Gene_Name,((CHAR_LENGTH(A2.Gene_Name))-(InStr(A2.Gene_Name,'.')))))=A2.Gene_Name";
                                $test =4;
            }
             $query= $this->db->query($sql_query);
             $result->sql = $sql_query;
             $result->nbr = $query->num_rows();
             $result->testTransAnnot =  $testTransAnnot;
             $result->testTransData =  $testTransData;
             $result->test =  $test;
             
            // Inserts. Use unbuffered_row . With big tables avoid memory overload //
            while ($res = $query->unbuffered_row())
            {
                    $this->db->insert($newName, $res);		
            }
            
            // Tables UPDATE //
            $explode=explode("_",$annot_table);	
            $id_organism=$explode[1];
            $submitter = $this->session->username;
            $UpdateData =array('table_name' => "$newName",'Master_Group' => "1",
                          'IdOrganism' => "$id_organism",'submitter' => "$submitter",
                          'version' => "1",'comment' => "Annotation file for table $data_table",
                          'file_name'  => "$data_table",'Root'=> "1",'Child' =>"$child");
            //$this->db->insert('tables', $data);
            $updTable= $this->update_table_info($UpdateData);
            $result->updTable =  $updTable;
            $result->UpdateData =  $UpdateData;
            return $result;
         }
        
        /**
        *function create_annot_table($table_name,$id_organism,$max_size,$file_name)
        *
        *   create annotation table from text import
        * @param string $table_name name of annotated table
        * @param integer $id_organism ref number of organism
        * @param integer $max_size  Gene_Name max lenght
        * @param string $file_name  name of uploaded file
        * @return object  $Data informational result (info ,error) 
        **/
        public function create_annot_table($table_name,$id_organism,$max_size,$file_name)
        {
            $result = new stdClass();
            $sql_query= "CREATE TABLE IF NOT EXISTS $table_name (
                              Annot_${id_organism}_ID int(10) unsigned NOT NULL AUTO_INCREMENT,
                              Gene_Name varchar($max_size) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
                              Analyse varchar(21) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
                              Signature varchar(18) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
                              Description varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
                              misc char(15) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
                              PRIMARY KEY (Annot_${id_organism}_ID),
                              KEY Gene_Name (Gene_Name),
                              KEY Signature (Signature)                          
                            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
            #print " Generic::create_annot_table($table_name,$id_organism,$max_size,$file_name) <br />SQL: $sql_query<br />";
            $this->db->trans_begin();
            $this->db->db_debug = FALSE; 
            $this->db->query($sql_query); 
            if ($this->db->trans_status() === FALSE)
            {
                    $this->db->trans_rollback(); 
                    $result->info .= "<br />Impossible de creer la Table  $table_name  : $sql_query<br />";  
                    $result->error=1;
            }
            else
            {
                $this->db->trans_commit();
                $result->info .= "<br />Table  $table_name created<br />";
                $result->error=0;
                $submitter = $this->session->username;
                $organisms = $this->get_organisms($id_organism);
                $Organism = $organisms->result->Organism;
                $UpdateData =array('table_name' => "$table_name",'Master_Group' => "1",
                              'IdOrganism' => "$id_organism",'submitter' => "$submitter",
                              'version' => "1",'comment' => "Annotation file for organism $Organism",
                              'file_name'  => "$file_name",'Root' => '1','Child'=>'0');
                $updTable= $this->update_table_info($UpdateData);
                $result->info .= $updTable->info;
            }
            return $result;
        }
    
        /**
        *function get_annotation_list($id='')
        *
        *   get list of annotation table  
        * @param integer $id table id
        * @return object $Data informational result & sql result as object (query, nbr record, sql_result)
        **/
        public function get_annotation_list($id='')
        {
            $result = new stdClass();
            $sql_query= "SELECT TABLE_NAME FROM information_schema.TABLES 
                    WHERE TABLE_SCHEMA='$this->database'
                    AND TABLE_NAME REGEXP  'Annotation_[0-9]'";
            if($id !="" ) 
            {
                $sql_query .= " SELECT TABLE_NAME FROM information_schema.TABLES 
                        WHERE TABLE_SCHEMA='$this->database'
                        AND TABLE_NAME ='Annotation_$id' ";        
            }
            $query= $this->db->query($sql_query);
            $result->sql = $sql_query;
            $result->nbr = $query->num_rows();
            $result->result = $query->result();
            if($result->nbr >0)
            {
                $Data=array();
                $Data[] = " ";
                foreach($result->result as $row)
                {
                    $table_name =$row->TABLE_NAME;
                    $new_sql="SELECT * FROM Organisms WHERE idOrganisms=SUBSTRING('$table_name',-1) ";
                    $res= $this->db->query($new_sql)->row();
                    $Data[$res->idOrganisms] = "Annotation_".$res->idOrganisms." ($res->Organism)";
                }
            }
            $result->Data= $Data;
            
            return $result;
        }
       
        
        /**function create_toolbox_table($table_name,$id_organism,$file_name)
        *
        *   create toolbox for organism
        * @param string $table_name name of toolbox table
        * @param integer $id_organism ref number of organism
        * @param string $file_name  name of uploaded file
        * @return object $result informational result (info ,error) 
        **/
        public function create_toolbox_table($table_name,$id_organism,$file_name)
        {
            $result = new stdClass();
            $sql_query= "CREATE TABLE IF NOT EXISTS $table_name (
                      toolbox_${id_organism}_ID int(10) NOT NULL AUTO_INCREMENT,
                      toolbox_name varchar(40) NOT NULL,
                      gene_name varchar(15) NOT NULL,
                      annotation varchar(25) DEFAULT NULL,
                      functional_class varchar(255) DEFAULT NULL,
                      biological_activity text,
                      WB_Db varchar(10) NOT NULL,
                      PRIMARY KEY (toolbox_${id_organism}_ID),
                      KEY gene_name (gene_name)
                    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
            $this->db->trans_begin();
            $this->db->db_debug = FALSE; 
            $this->db->query($sql_query); 
            if ($this->db->trans_status() === FALSE)
            {
                    $this->db->trans_rollback(); 
                    $result->info .= "<br />Impossible de creer la Table  $table_name  : $sql_query<br />";  
                    $result->error=1;
            }
            else
            {
                $this->db->trans_commit();
                $result->info .= "<br />Table  $table_name created<br />";
                $result->error=0;
                $submitter = $this->session->username;
                $organisms = $this->get_organisms($id_organism);
                $Organism = $organisms->result->Organism;
                $UpdateData =array('table_name' => "$table_name",'Master_Group' => "1",
                              'IdOrganism' => "$id_organism",'submitter' => "$submitter",
                              'version' => "1",'comment' => "Annotation file for organism $Organism",
                              'file_name'  => "$file_name",'Root'=> '0','Child'=>'0');
                $updTable= $this->update_table_info($UpdateData);
                $result->info .= $updTable->info;
                ##########  update tables_group #################
                ## get Idtables
                /* $IdTable = $updTable->IdTable;
                 $UpdateGrpData = array(0 =>array('table_name'=>$table_name, 'table_id' => $IdTable ,'group_id'=> "1"),
                                         1=>array('table_name'=>$table_name,'table_id' => $IdTable ,'group_id'=> "2")
                                    );
                # var_dump($UpdateGrpData);
                $updTableGrp= $this->update_table_group_info($UpdateGrpData);
                $result->info .= $updTableGrp->info;*/
                
            }
            return $result;
        }
    
        /**
        *function get_Subtables($organism)
        *
        *   get sub-tables of dataset
        * @param integer $organism ref number of organism
        * @return object $result sql result as Array
        **/
        public function get_Subtables($organism){
            $result =new stdclass;
            $original="Annotation_$organism";
            $query=$this->db->query("SELECT TableName FROM tables WHERE Organism='$organism' AND TableName LIKE 'Annotation_%' AND TableName<>'$original'");
            $result->nbr = $query->num_rows();
            $result->result =$query->result_array();
            return $result; 
        } 
    
          
        /**
        *function get_Toolbox_Names($organism)
        *
        *   get single toolbox fo organism
        * @param integer $organism ref number of organism
        * @return object $result sql result as Array
        **/
        public function get_Toolbox_Names($organism)
        {
            $toolbox="Toolbox_$organism";
            if($this->db->table_exists($toolbox))
            {
                    $query=$this->db->query("SELECT DISTINCT(toolbox_name) FROM $toolbox");
                    return $query->result_array();
            }
            else
            {
                    return array("");
            }
        }
    
        /**
        *function get_fClass_from_Toolbox($toolboxName,$organism)
        *
        *   get functional classes from toolbox
        * @param string $toolboxName name of toolbox
        * @param integer $organism ref number of organism
        * @return object $result sql result as Array
        **/
        public function get_fClass_from_Toolbox($toolboxName,$organism)
        {
            $toolTable="Toolbox_$organism";
            if($this->db->table_exists($toolTable))
            {
                if($toolboxName == 'all')
                {
                    $query=$this->db->query("SELECT DISTINCT(functional_class) FROM $toolTable");
                }
                else
                {
                    $query=$this->db->query("SELECT DISTINCT(functional_class) FROM $toolTable WHERE toolbox_name='$toolboxName'");	
                }
                return $query->result_array();
            }
            else
            {
                    return array("");
            }
        }
    
        /**
        *function get_Genes_from_Toolbox($organism,$tbName,$fClass,$wpDB)
        *
        *   get list of genes from toolbox
        * @param integer $organism ref number of organism
        * @param string $tbName name of toolbox table
        * @param string $fClass  functional class
        * @param string $wpDB true/false present in Wall Prot Db 
        * @return object $result sql result as Array
        **/
       public function get_Genes_from_Toolbox($organism,$tbName,$fClass,$wpDB)
       {
            $toolTable="Toolbox_$organism";
            if($this->db->table_exists($toolTable))
            {      
                    if($tbName == 'all')
                    {
                
                            if($fClass == 'none')
                            {
                                    if($wpDB != 'all')
                                    {
                                            $query=$this->db->query("SELECT gene_name,biological_activity,annotation FROM $toolTable WHERE WB_Db='$wpDB'");
                                    }
                                    else
                                    {
                                            $query=$this->db->query("SELECT gene_name,biological_activity,annotation FROM $toolTable");
                                    }
                            }
                
                            if($fClass != 'none')
                            {
                                    if($wpDB != 'all')
                                    {
                                            $query=$this->db->query("SELECT gene_name,biological_activity,annotation  FROM $toolTable WHERE functional_class='$fClass' AND WB_Db='$wpDB'");
                                    }
                                    else
                                    {
                                            $query=$this->db->query("SELECT gene_name,biological_activity,annotation  FROM $toolTable WHERE functional_class='$fClass'");
                                    }
                            }
                    }
    
                    else if($tbName != 'all')
                    {
    
                            if($fClass == 'none')
                            {
                                    if($wpDB != 'all')
                                    {
                                            $query=$this->db->query("SELECT gene_name,biological_activity,annotation  FROM $toolTable WHERE toolbox_name='$tbName' AND WB_Db='$wpDB'");
                                    }
                                    else
                                    {
                                            $query=$this->db->query("SELECT gene_name,biological_activity,annotation  FROM $toolTable WHERE toolbox_name='$tbName'");
                                    }
                            }
    
                            if($fClass != 'none')
                            {
                                    if($wpDB != 'all')
                                    {
                                            $query=$this->db->query("SELECT gene_name,biological_activity,annotation  FROM $toolTable WHERE functional_class='$fClass' AND toolbox_name='$tbName' AND WB_Db='$wpDB'");
                                    }
                                    else
                                    {
                                            $query=$this->db->query("SELECT gene_name,biological_activity,annotation  FROM $toolTable WHERE functional_class='$fClass' AND toolbox_name='$tbName'");
                                    }
                            }
                    }
                    return $query->result_array();
            }
    
            else
            {
                    return array("");
            } 
       }
    
        /**
        *function download_Results($table,$seuilName,$organism,$annot,$toolbox)
        *
        *   download result for current analysis
        * @param string $table name of annotated table
        * @param integer $seuilName  threshold
        * @param integer $organism ref number of organism
        * @param string $annot
        * @param string $toolbox
        * @return object $result sql result as Array
        **/
        public function download_Results($table,$seuilName,$organism,$annot,$toolbox)
        {
                $clusterTable=$table."_".$seuilName."_Cluster";
                $result = new stdclass;
                $annoTable="Annotation_$table";
                $toolboxTable="Toolbox_$organism";
                if(count($annot)==0 && count($toolbox)==0)
                {
                    $queryText="SELECT a.Gene_Name,b.cluster, b.group 
                    FROM $table AS a , $clusterTable AS b 
                    WHERE a.Gene_ID=b.Gene_ID";
                    $query=$this->db->query($queryText);
                    $result->sql = "1:".$queryText;
                }
                else if(count($annot)!=0 && count($toolbox)==0)
                {
                        $queryText=("SELECT a.Gene_Name,b.cluster, b.group,c.Signature ,c.Description 
                                        FROM $table AS a, $clusterTable AS b, $annoTable AS c 
                                        WHERE a.Gene_ID=b.Gene_ID 
                                        AND a.Gene_Name=c.Gene_Name 
                                        AND (");
                        $copy=$annot;
                        foreach($annot as $ann)
                        {
                                $queryText.="Analyse='$ann'";
                                if (next($copy)) 
                                {
                                        $queryText.=" OR ";
                                }
                        }
                        $queryText.=' )';
                        $query=$this->db->query($queryText);
                        $result->sql = "2:".$queryText;
                }
                else if(count($annot)==0 && count($toolbox)!=0)
                {
                        $queryText=("SELECT t1.Gene_Name , t2.cluster , t2.group ,t3.toolbox_name, t3.annotation,t3.biological_activity 
                                        FROM  $table t1
                                        INNER JOIN  $clusterTable t2 ON t1.Gene_ID = t2.Gene_ID 
                                        INNER JOIN  $toolboxTable t3 ON t1.Gene_Name LIKE CONCAT( t3.gene_name ,  '%' ) 
                                        WHERE ( ");
                        $copy=$toolbox;
                        foreach($toolbox as $tool)
                        {
                                $queryText.="toolbox_name='$tool'";
                                if (next($copy))
                                {
                                        $queryText.=" OR ";
                                }
                        }
                        $queryText.=' ) ORDER BY t2.cluster,t2.group';
                        //echo $queryText;
                        $result->sql = "3:".$queryText;
                        $query=$this->db->query($queryText);
                        
                }
                else if (count($annot)!=0 && count($toolbox)!=0)
                {
                        $queryText=("SELECT t1.Gene_Name , t2.cluster , t2.group ,t3.toolbox_name, t3.annotation,t3.biological_activity 
                                        FROM  $table t1
                                        INNER JOIN  $clusterTable t2 ON t1.Gene_ID = t2.Gene_ID 
                                        INNER JOIN  $toolboxTable t3 ON t1.Gene_Name LIKE CONCAT( t3.gene_name ,  '%' ) 
                                        WHERE( ");
                        $copy=$toolbox;
                        foreach($toolbox as $tool)
                        {
                                $queryText.="toolbox_name='$tool'";
                                if (next($copy))
                                {
                                        $queryText.=" OR ";
                                }
                        }
                        $queryText.=' ) ORDER BY t2.cluster,t2.group';
                        $result->sql = "4:".$queryText;
                        $query=$this->db->query($queryText);
                }
                $result->result = $query->result_array();
                return $result;
        }
        
        /**
        *function get_Gene_Annot($gene,$table,$annot)
        *
        *   cet annotation for gene
        * @param string $gene name of gene
        * @param string $table  name of annotated table
        * @param string $annot annotation ref
        * @return object $result  sql result as Array
        **/
        public function get_Gene_Annot($gene,$table,$annot)
        {
            $result = new stdclass;
            $annoTable="Annotation_$table";
            $queryText= "SELECT Signature ,Description
                    FROM $annoTable 
                    WHERE Gene_Name='$gene' 
                    AND (";
            $copy=$annot;
            foreach($annot as $ann)
            {
                $queryText.="Analyse='$ann'";
                if (next($copy)) 
                {
                    $queryText.=" OR ";
                }
            }
            $queryText.=' )';
            $query=$this->db->query($queryText);
            $result->sql = $queryText;
            $result->result = $query->result_array();
            return $result;
        
        }
        
        /**
        *function get_KEGG_Ref($keggName)
        *   get Keeg (enzyme) annotation for given Id
        *
        * @param string $keggName
        * @return string  annotation
        **/
        public function get_KEGG_Ref($keggName)
        {
            #$this->db = $this->load->database('Kegg',TRUE);
            $sql_query= "SELECT annotation FROM Ref_Enzymes WHERE nom='$keggName'";
            $query= $this->db->query($sql_query)->row();
            if(isset($query->annotation))
                $result = $query->annotation;
            else $result='';
            return $result;
        }
        
        /**
        *function get_KO_Ref($koName)
        *   get KO  annotation for given Id
        *
        * @param string $koName
        * @return string  annotation   
        **/
        public function get_KO_Ref($koName)
        {
            $sql_query= "SELECT annotation FROM Ref_KEGG WHERE nom='$koName'";
            $query= $this->db->query($sql_query)->row();
            if(isset($query->annotation))
                $result = $query->annotation;
            else $result='';
            return $result;
        }
        
        /**
        *function get_KOG_Ref($kogName)
        *   get KOG annotation for given Id
        *
        * @param string $kogName
        * @return string  annotation
        **/
        public function get_KOG_Ref($kogName)
        {
            $sql_query= "SELECT annotation FROM Ref_KOG WHERE nom='$kogName'";
            $query= $this->db->query($sql_query)->row();
            if(isset($query->annotation))
                $result = $query->annotation;
            else $result='';
            return $result;
        }
        
        /**
        *function get_PANTHER_Ref($Panther)
        *   get PANTHER annotation for given Id
        *
        * @param string $Panther
        * @return string  annotation
        **/
        public function get_PANTHER_Ref($Panther)
        {
            $sql_query= "SELECT annotation FROM Ref_PANTHER WHERE nom='$Panther'";
            $query= $this->db->query($sql_query)->row();
            if(isset($query->annotation))
                $result = $query->annotation;
            else $result='';
            return $result;
        }
        
        /**
        *function get_GO_Ref($GO)
        *       get GO annotation for given Id
        *
        * @param string $GO
        * @return object  $result (annotation,type)
        **/
        public function get_GO_Ref($GO)
        {   
            $result =new stdclass;
            $sql_query= "SELECT annotation,type FROM Ref_GO WHERE nom='$GO'";
            $query= $this->db->query($sql_query);
            $result->nbr = $query->num_rows();
            
            if($result->nbr>0)
            {   $res =$query->row();
                $result->annot = $res->annotation;
                $result->type = $res->type;
            }
            else
            {
                $result->annot = $result->type = "";
            }
            return $result;
        }
        
        /**
        *function  get_PFAM_Ref($pfamName)
        *   get PFAM annotation for given Id
        *
        * @param string $pfamName
        * @return string  annotation
        **/
        public function get_PFAM_Ref($pfamName)
        {
            #$this->db = $this->load->database('pfam',TRUE);
            $sql_query= "SELECT annotation FROM Ref_PFAM WHERE nom='$pfamName'";
            $query= $this->db->query($sql_query)->row();
            if(isset($query->annotation))
                $result = $query->annotation;
            else $result='';
            return $result;
        }

}
