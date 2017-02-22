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
class Generic extends CI_Model {

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
        *function 
        *
	*
        **/
        
        public function get_query_row($sql_query)
        {
            $query= $this->db->query($sql_query);
            return $query->row();
        }
        
	/**
        *function 
        *
	*
        **/
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
        *function 
        *
	*
        **/
        public function get_table_members($id='')
        {
            $result = new stdClass();
            $sql_query= "SELECT IdTables,TableName ,MasterGroup ,Organism ,Submitter,t.version ,
                                comment,tg.group_id 
                        FROM tables as t
                        INNER JOIN tables_groups as tg on tg.table_id=IdTables
                        INNER JOIN users_groups as u on u.group_id= tg.group_id";             
             if($id !="" ) $sql_query .= "            WHERE user_id=$id";
             $sql_query .= "    GROUP BY IdTables";
            $query= $this->db->query($sql_query);
            $result->sql = $sql_query;
            $result->nbr = $query->num_rows();
            $result->result = $query->result_array();
            return $result;
        }
        
	/**
        *function 
        *
	*
        **/
        public function get_tables($id='')
        {
            $result = new stdClass();
            $sql_query= "SELECT IdTables,TableName ,MasterGroup,name ,o.Organism ,idOrganisms,
                                Submitter,version ,comment,Root
                        FROM tables as t
                        INNER JOIN Organisms as o on idOrganisms=t.Organism
                        INNER JOIN groups as g on g.id= MasterGroup ";
            if($id !="" ) $sql_query .= " WHERE IdTables ='$id' ";
            $sql_query .= " ORDER by TableName ";
            $query= $this->db->query($sql_query);
            $result->sql = $sql_query;
            $result->nbr = $query->num_rows();
            $result->result = $query->result_array();
            return $result;
        }
        
	/**
        *function 
        *
	*
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
        *function 
        *
	*
        **/
        public function get_users($id='')
        {
            $result = new stdClass();
            $sql_query= "SELECT id, `username`, `first_name`, `last_name`, `company`,`email`
                FROM `users`";
            if($id !="" ) $sql_query .= " WHERE id ='$id' ";
            $query= $this->db->query($sql_query);
            $result->sql = $sql_query;
            $result->nbr = $query->num_rows();
            $result->result = $query->result_array();
            return $result;
        }
        
	/**
        *function 
        *
	*
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
        *function 
        *
	*
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
        *function 
        *
	*
        **/
        public function get_table_group($id='')
        {
            $result = new stdClass();
            $sql_query= "SELECT t.id,table_id,group_id,name 
                        FROM  tables_groups as t
                        INNER join groups as g on g.id= group_id ";
                        
            $sql_query= "SELECT t.id,table_id,group_id,name ,tablename
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
        *function 
        *
	*
        **/
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
        *function 
        *
	*
        **/
        public function get_removable_table()
        {
            
            $result = new stdClass();
            $sql_query= "SELECT TABLE_NAME,ENGINE,TABLE_ROWS,DATA_LENGTH,
                                CREATE_TIME,IdTables,TableName
                        FROM information_schema.TABLES as inf
                        LEFT JOIN tables as clu on clu.TableName=inf.TABLE_NAME
                        WHERE `TABLE_SCHEMA`='$this->database'";
            $query= $this->db->query($sql_query);
            $result->sql = $sql_query;
            $result->nbr = $query->num_rows();
            $result->result = $query->result_array();
            return $result;
        }
        
	/**
        *function 
        *
	*
        **/
        public function get_table_info($table_name)
            {
                $result = new stdClass();
                $sql_query= "SELECT TABLE_NAME FROM information_schema.TABLES 
	                    WHERE `TABLE_SCHEMA`='$this->database'
	                            AND TABLE_NAME LIKE '$table_name%'";
                $query= $this->db->query($sql_query);
                $result->sql = $sql_query;
                $result->nbr = $query->num_rows();
                $result->result = $query->result();
                return $result;
            }

	/**
        *function 
        *
	*
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
                  $result->result = $query->result();
              }
              return $result;
          }

	/**
        *function 
        *
	*
        **/
      public function insert_project($project_data)
      {
          $result = new stdClass();
          $sql_query= "INSERT INTO user_project
                    (`uprj_uacc_fk`, `uprj_project_name`, `uprj_project_comment`,
                    `uprj_project_visibility`, `uprj_project_shared`, `uprj_project_date_start`, `uprj_project_date_end`)
                    VALUE('".$project_data['uprj_uacc_fk']."' , '". $project_data['uprj_project_name']."' , '".
                    $project_data['uprj_project_comment']."' , '".$project_data['uprj_project_visibility']."' , '".
                    $project_data['uprj_project_shared']."' , '".$project_data['uprj_project_date_start']."' , '".
                    $project_data['uprj_project_date_end']."')";
           
            $this->db->trans_begin();
            $this->db->db_debug = FALSE; 
            $this->db->query($sql_query);
            
            if ($this->db->trans_status() === FALSE)
            {
                // generate an error... or use the log_message() function to log your error
                $message = $this->db->error(); 
                $this->db->trans_rollback();
            } 
            else 
            {
                $this->db->trans_commit();
                $message=1;
            }
         # $query= $this->db->query($sql_query);
          $result->sql = $sql_query;
          $result->message = $message;
          return $result;
      }

	/**
        *function 
        *
	*
        **/
      public function check_project($user_id)
    {
      $check_project="SELECT * 
              FROM `user_project`  
              INNER JOIN users on id=uprj_uacc_fk
              WHERE `uprj_uacc_fk` ='$user_id' ";
      $response = $this->db->query($check_project);
      $nbr=$response->num_rows();
      if ( $nbr>0)
      {
        foreach ($response->result_array() as $row)
        {
           $working_project=$row['uprj_project_name'];
           $id_project=$row['uprj_id'];
        }
        $this->session->set_userdata('working_project', $working_project);
        #$this->session->set_userdata('name'] => $this->auth->session_data));
        $this->session->set_userdata('working_project_id',$id_project);
                    
      }
      return $response;
    }

	/**
        *function 
        *
	*
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
        *function 
        *
	*
        **/
    public function who_online($time='')
    {
        if($time=='') $time=5;
        $result = new stdClass();
        $sql_query= "SELECT `ip_address`,`timestamp`,
                        FROM_unixtime(`timestamp`) as date, CAST(data AS CHAR(10000) CHARACTER SET utf8) as data
                        FROM `ci_sessions`
                        WHERE `timestamp` > unix_timestamp(DATE_SUB(now(), INTERVAL $time MINUTE));";
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
              #var_dump($return_data);
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
           # $resultArr[]['extra'] =  ;
            
        }
        $result->Data = $resultArr;
       # $result->Data = $result->result ;
        return $result;
    }
    
	/**
        *function 
        *
	*
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
             $sql_data_CT1  = "DROP TABLE IF EXISTS `$table_name` ;";
            
            $this->db->trans_begin();
            $this->db->db_debug = FALSE; 
            $this->db->query($sql_data_CT1); 
            if ($this->db->trans_status() === FALSE)
            {
                    $this->db->trans_rollback(); 
                    $Data->info .= "<br />Impossible de detruire la Table  `$table_name`<br />";                
            }
            else
            {
                $this->db->trans_commit();
                $Data->info .= "<br />Table  `$table_name` dropped<br />";
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
                $Data->info .= "<br />Impossible de cr&eacute;er la Table  `$table_name`<br />";                
        }
        else
        {
            $this->db->trans_commit();
            $Data->info .= "<br />Table  `$table_name` cr&eacute;&eacute;<br />";
        }
        
        ####################### Update  table information
        $MasterGroup= $this->db->query("select id from groups where name='$master_group'")->row();
        $Master_Group= $MasterGroup->id;
        $submitter = $this->session->userdata('identity');
        #$organismId=1;
       # print "MasterGroup  $Master_Group /".print_r($MasterGroup,1)."<br />";
       
       $UpdateData =array('table_name' => "$table_name",'Master_Group' => "$Master_Group",
                          'IdOrganism' => "$IdOrganism",'submitter' => "$submitter",
                          'version' => "$version",'comment' => "$comment",'file_name'  => "$file_name");
       
       $update_tables= $this->update_table_info($UpdateData);
       
       $Data->info .= $update_tables->info;
       $IdTable= $update_tables->IdTable;
         ####################### Update  table_groups information
          $this->db->query("REPLACE INTO `tables_groups`(`table_id` ,`group_id`) VALUES ( '$IdTable', '$Master_Group')");
          ##############  Allow Admin !! ######################
          $this->db->query("REPLACE INTO `tables_groups`(`table_id` ,`group_id`) VALUES ( '$IdTable', '1')");
          
          $Data->info .= "\t\t table Tables tables_groups a jour IdTable $IdTable<hr />";
        return $Data;
    }
    
	/**
        *function 
        *
	*
        **/
    public function update_table_info($UpD)
    {
        $Data = new stdclass;
        $update_tables= "REPLACE INTO `tables` (
                                `TableName` ,
                                `MasterGroup`, `Organism` , `Submitter`, `version`, `comment`, `original_file`)
                         VALUES ('".$UpD['table_name']."','".$UpD['Master_Group']."','".$UpD['IdOrganism']."',
                                '".$UpD['submitter']."','".$UpD['version']."','".addslashes($UpD['comment'])."','".addslashes($UpD['file_name'])."');";
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
             $IdTableQ= $this->db->query("SELECT `IdTables` FROM `tables` where `TableName`='$table_name'")->row();
             $IdTable=$IdTableQ->IdTables;
             $Data->IdTable = $IdTable;
             $Data->info .= "<br />table Tables a jour IdTable $IdTable . Tables updated: $update_tables<hr />";
            
        }
        
        return $Data;
    }

	/**
        *function 
        *
	*
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
                $Data->info .= "<br />Impossible d'ins&eacute;r&eacute;er des donn&eacute;es dans  la Table  `$table_name`<br />";
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
        *function 
        *
	*
        **/
     public function update_Tables_On_Clustering($filename,$seuil,$id_organism)
     {
	$submitter = $this->session->username;
	$newName=$filename."_".$seuil."_Cluster";
        $UpdateData =array('table_name' => "$newName",'Master_Group' => "1",
                      'IdOrganism' => "$id_organism",'submitter' => "$submitter",
                      'version' => "1",'comment' => "Cluster file for table $filename",'file_name'  => "$filename");
	$updTable= $this->update_table_info($UpdateData);
	$newName=$filename."_".$seuil."_Order";
        $UpdateData =array('table_name' => "$newName",'Master_Group' => "1",
                      'IdOrganism' => "$id_organism",'submitter' => "$submitter",
                      'version' => "1",'comment' => "Order file for table $filename",'file_name'  => "$filename");
	$updTable= $this->update_table_info($UpdateData);
   	
     }
     
	/**
        *function 
        *test if annotation table contains gene or transcripts
	*if gene name has version (.1,.2) it is a transcript
	*@param : annotation table name
        **/
     public function hasTranscriptsAnnot($annot)
     {
	$sql_query="SELECT count(*) as len FROM `$annot` WHERE `Ref_Gene` REGEXP '.[1-9]$'";
	$query=$this->db->query($sql_query);
	return $query->result_array();
     }

	/**
        *function 
        *test if values table contains gene or transcripts
	*if gene name has version (.1,.2) it is a transcript
	*@param : values table name
        **/

     public function hasTranscriptsValues($values)
     {
         //  SELECT count(*) as len FROM `Paroi_Euca_Fluidigm` WHERE `Gene_Name` LIKE '%.%'  
	$sql_query="SELECT count(*) as len FROM `$values` WHERE `Gene_Name` REGEXP '.[1-9]$'";
	$query=$this->db->query($sql_query);
	return $query->result_array();
     }

	/**
        *function 
        *get annotation for specific gene from Organism annotation table
	*create file-specific annotation table
	*@params: global annotation table and values table
        **/
     public function extract_annot($annot_table,$data_table)
     {
	 $this->load->dbforge();
         $result = new stdClass();

	// TEST TRANSCRITS //
	 $testTransAnnot = $this->hasTranscriptsAnnot($annot_table);	
	 $testTransData = $this->hasTranscriptsValues($data_table);
	 $testTransAnnotLen=$testTransAnnot[0]['len'];
	 $testTransDataLen=$testTransData[0]['len'];

	// Si les annotations et les data ne sont pas des transcrits //
	 if($testTransAnnotLen == 0 && $testTransDataLen == 0){
		 $sql_query="SELECT `Gene_Name`, `Analyse`, `Signature`,`Description` FROM `$annot_table` inner join `$data_table` on `Gene_Name`=`Ref_Gene`";
	 }
	// Si les annotations et les data sont des transcrits //	
	 elseif($testTransAnnotLen != 0 && $testTransDataLen != 0){
		$sql_query="SELECT `Gene_Name`, `Analyse`, `Signature`,`Description` FROM `$annot_table` inner join `$data_table` on `Gene_Name`=`Ref_Gene`";
	}
	// Si les annotations sont des transcrits mais pas les valeurs //
	 elseif($testTransAnnotLen != 0 && $testTransDataLen == 0){ 	
		 $sql_query= "SELECT `Gene_Name`, `Analyse`, `Signature`,`Description` FROM $annot_table
			 inner join $data_table on concat(Gene_Name,'.1')=Ref_Gene";
	 }
	// Si les annotations ne sont pas des transcripts mais les valeurs oui //
	elseif($testTransAnnotLen == 0 && $testTransDataLen != 0){
		// delete two last character from transcript //
		$sql_query="SELECT SUBSTR(`Gene_Name`, 1, CHAR_LENGTH(`Gene_Name`) - 2) AS Gene_Name, `Analyse`, `Signature`,`Description` 
				FROM `$annot_table`
				inner join `$data_table` on SUBSTR(`Gene_Name`, 1, CHAR_LENGTH(`Gene_Name`) - 2)=`Ref_Gene`";
	}
         $query= $this->db->query($sql_query);
         $result->sql = $sql_query;
         $result->nbr = $query->num_rows();
        # $result->result = $query->result();


		// Table Creation //
         $newName="Annotation_$data_table";	
	 $fields=array(
	    'annot_id' =>array('type' => 'INT','constraint' => 10, 
	                        'unsigned' => TRUE,
	                        'auto_increment' => TRUE),
            'Gene_Name' =>   array('type'=>'VARCHAR','constraint'=>'15'),
            'Analyse'   =>   array('type'=>'VARCHAR','constraint'=>'21'),
            'Signature' =>   array('type'=>'VARCHAR','constraint'=>'18'),
            'Description' => array('type'=>'VARCHAR','constraint'=>'255'),
	 );
	
	
	 $this->dbforge->add_field($fields);
	 $this->dbforge->add_key('annot_id', TRUE);
	 $this->dbforge->create_table($newName);
	 
		// Inserts //
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
                      'version' => "1",'comment' => "Annotation file for table $data_table",'file_name'  => "$data_table");
	//$this->db->insert('tables', $data);
	$updTable= $this->update_table_info($UpdateData);
        #return $result;
        return $testTransAnnotLen;
     }
    
	/**
        *function 
        *
	*
        **/
    public function create_annot_table($table_name,$id_organism,$max_size,$file_name)
    {
        $result = new stdClass();
        $sql_query= "CREATE TABLE IF NOT EXISTS `$table_name` (
                          `Annot_${id_organism}_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
                          `Ref_Gene` varchar($max_size) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
                          `Analyse` varchar(21) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
                          `Signature` varchar(18) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
                          `Description` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
                          `misc` char(15) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
                          PRIMARY KEY (`Annot_${id_organism}_ID`),
                          KEY `Ref_Gene` (`Ref_Gene`),
                          KEY `Signature` (`Signature`)                          
                        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
        #print " Generic::create_annot_table($table_name,$id_organism,$max_size,$file_name) <br />SQL: $sql_query<br />";
        $this->db->trans_begin();
        $this->db->db_debug = FALSE; 
        $this->db->query($sql_query); 
        if ($this->db->trans_status() === FALSE)
        {
                $this->db->trans_rollback(); 
                $result->info .= "<br />Impossible de creer la Table  `$table_name`  : $sql_query<br />";  
                $result->error=1;
        }
        else
        {
            $this->db->trans_commit();
            $result->info .= "<br />Table  `$table_name` created<br />";
            $result->error=0;
            $submitter = $this->session->username;
            $organisms = $this->get_organisms($id_organism);
            $Organism = $organisms->result->Organism;
            $UpdateData =array('table_name' => "$table_name",'Master_Group' => "1",
                          'IdOrganism' => "$id_organism",'submitter' => "$submitter",
                          'version' => "1",'comment' => "Annotation file for organism $Organism",'file_name'  => "$file_name");
            $updTable= $this->update_table_info($UpdateData);
            $result->info .= $updTable->info;
        }
        return $result;
    }

	/**
        *function 
        *
	*
        **/
    public function get_annotation_list($id='')
    {
        
        $result = new stdClass();
        $sql_query= "SELECT TABLE_NAME FROM information_schema.TABLES 
                    WHERE `TABLE_SCHEMA`='$this->database'
                    AND TABLE_NAME REGEXP  'Annotation_[0-9]'";
        if($id !="" ) 
        {
            $sql_query .= " SELECT TABLE_NAME FROM information_schema.TABLES 
                    WHERE `TABLE_SCHEMA`='$this->database'
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
         foreach(   $result->result as $row)
         {
             $table_name =$row->TABLE_NAME;
             $new_sql="SELECT * FROM `Organisms` WHERE `idOrganisms`=SUBSTRING('$table_name',-1) ";
             $res= $this->db->query($new_sql)->row();
           //  array_push($Data,array('idOrganisms' =>"$res->idOrganisms",'Organism' => "$res->Organism", 
             //    'Annotat$Dataion' =>"Annotation_".$res->idOrganisms.""));
              $Data[$res->idOrganisms] = "Annotation_".$res->idOrganisms." ($res->Organism)";
         }
        }
        $result->Data= $Data;
        
        return $result;
    }
   
	/**
        *function 
        *
	*
        **/
    public function get_Subtables($organism){
        $result =new stdclass;
	$original="Annotation_$organism";
	$query=$this->db->query("SELECT TableName FROM tables WHERE Organism='$organism' AND TableName LIKE 'Annotation_%' AND TableName<>'$original'");
	#print " get_Subtables($organism) SELECT TableName FROM tables WHERE Organism='$organism' AND TableName LIKE 'Annotation_%' AND TableName<>'$original'";
	$result->nbr = $query->num_rows();
	$result->result =$query->result_array();
	return $result; 
    } 

	/**
        *function 
        *
	*
        **/
    public function get_Toolbox_Names($organism){
	$toolbox="Toolbox_$organism";
	if($this->db->table_exists($toolbox)){
		$query=$this->db->query("SELECT DISTINCT(`toolbox_name`) FROM `$toolbox`");
		return $query->result_array();
	}
	else{
		return array("");
	}
    }

	/**
        *function 
        *
	*
        **/
    public function get_fClass_from_Toolbox($toolboxName,$organism){
	$toolTable="Toolbox_$organism";
	if($this->db->table_exists($toolTable)){
		if($toolboxName == 'all'){
			$query=$this->db->query("SELECT DISTINCT(`functional_class`) FROM $toolTable");
		}
		else{
			$query=$this->db->query("SELECT DISTINCT(`functional_class`) FROM $toolTable WHERE `toolbox_name`='$toolboxName'");	
		}
		return $query->result_array();
	}
	else{
		return array("");
	}
    }

	/**
        *function 
        *
	*
        **/
   public function get_Genes_from_Toolbox($organism,$tbName,$fClass,$wpDB){
	$toolTable="Toolbox_$organism";
	if($this->db->table_exists($toolTable)){      
		if($tbName == 'all'){
	    
			if($fClass == 'none'){
				if($wpDB != 'all'){
					$query=$this->db->query("SELECT `gene_name`,`biological_activity`,`annotation` FROM $toolTable WHERE `WB_Db`='$wpDB'");
				}
				else{
					$query=$this->db->query("SELECT `gene_name`,`biological_activity`,`annotation` FROM $toolTable");
				}
			}
	    
			if($fClass != 'none'){
				if($wpDB != 'all'){
					$query=$this->db->query("SELECT `gene_name`,`biological_activity`,`annotation`  FROM $toolTable WHERE `functional_class`='$fClass' AND `WB_Db`='$wpDB'");
				}
				else{
					$query=$this->db->query("SELECT `gene_name`,`biological_activity`,`annotation`  FROM $toolTable WHERE `functional_class`='$fClass'");
				}
			}
		}

		else if($tbName != 'all'){

			if($fClass == 'none'){
				if($wpDB != 'all'){
					$query=$this->db->query("SELECT `gene_name`,`biological_activity`,`annotation`  FROM $toolTable WHERE `toolbox_name`='$tbName' AND `WB_Db`='$wpDB'");
				}
				else{
					$query=$this->db->query("SELECT `gene_name`,`biological_activity`,`annotation`  FROM $toolTable WHERE `toolbox_name`='$tbName'");
				}
			}

			if($fClass != 'none'){
				if($wpDB != 'all'){
					$query=$this->db->query("SELECT `gene_name`,`biological_activity`,`annotation`  FROM $toolTable WHERE `functional_class`='$fClass' AND `toolbox_name`='$tbName' AND `WB_Db`='$wpDB'");
				}
				else{
					$query=$this->db->query("SELECT `gene_name`,`biological_activity`,`annotation`  FROM $toolTable WHERE `functional_class`='$fClass' AND `toolbox_name`='$tbName'");
				}
			}

		}

		return $query->result_array();
	}

	else{
		return array("");
	} 
   }

	/**
        *function 
        *
	*
        **/
	public function download_Results($table,$seuilName,$organism,$annot,$toolbox){
		$clusterTable=$table."_".$seuilName."_Cluster";
		$annoTable="Annotation_$table";
		$toolboxTable="Toolbox_$organism";
		#SELECT a.`Gene_Name`,b.`cluster`, b.`group` FROM `Perox_Demo_100` AS a, `Perox_Demo_100_0_2_Cluster` AS b WHERE a.`Gene_ID`=b.`Gene_ID`
		if(count($annot)==0 && count($toolbox)==0){
			$query=$this->db->query("SELECT a.`Gene_Name`,b.`cluster`, b.`group` FROM $table AS a , $clusterTable AS b WHERE a.`Gene_ID`=b.`Gene_ID`");
		}
		else if(count($annot)!=0 && count($toolbox)==0){
			$queryText=("SELECT a.`Gene_Name`,b.`cluster`, b.`group`,c.`Signature` 
					FROM `$table` AS a, `$clusterTable` AS b, `$annoTable` AS c 
					WHERE a.`Gene_ID`=b.`Gene_ID` 
					AND a.`Gene_Name`=c.`Gene_Name` 
					AND (");
			$copy=$annot;
			foreach($annot as $ann){
				$queryText.="`Analyse`='$ann'";
				if (next($copy)) {
      	  				$queryText.=" OR ";
    				}
			}
			$queryText.=' )';
			$query=$this->db->query($queryText);
		}
		else if(count($annot)==0 && count($toolbox)!=0){
			$queryText=("SELECT t1.`Gene_Name` , t2.`cluster` , t2.`group` , t3.`annotation`,t3.`biological_activity` 
					FROM  `$table` t1
					INNER JOIN  `$clusterTable` t2 ON t1.`Gene_ID` = t2.`Gene_ID` 
					INNER JOIN  `$toolboxTable` t3 ON t1.`Gene_Name` LIKE CONCAT( t3.`gene_name` ,  '%' ) 
					WHERE( ");
			$copy=$toolbox;
			foreach($toolbox as $tool){
				$queryText.="`toolbox_name`='$tool'";
				if (next($copy)) {
      	  				$queryText.=" OR ";
    				}
			}
			$queryText.=' ) ORDER BY t2.`cluster`,t2.`group`';
			//echo $queryText;
			$query=$this->db->query($queryText);
		}
		else if (count($annot)!=0 && count($toolbox)!=0){
			$queryText=("SELECT t1.`Gene_Name` , t2.`cluster` , t2.`group` , t3.`annotation`,t3.`biological_activity` 
					FROM  `$table` t1
					INNER JOIN  `$clusterTable` t2 ON t1.`Gene_ID` = t2.`Gene_ID` 
					INNER JOIN  `$toolboxTable` t3 ON t1.`Gene_Name` LIKE CONCAT( t3.`gene_name` ,  '%' ) 
					WHERE( ");
			$copy=$toolbox;
			foreach($toolbox as $tool){
				$queryText.="`toolbox_name`='$tool'";
				if (next($copy)) {
      	  				$queryText.=" OR ";
    				}
			}
			$queryText.=' ) ORDER BY t2.`cluster`,t2.`group`';
			$query=$this->db->query($queryText);
		}
		return $query->result_array();
	}
	
	/**
        *function 
        *
	*
        **/
	public function get_Gene_Annot($gene,$table,$annot){
		$annoTable="Annotation_$table";
		$queryText=("SELECT `Signature` 
				FROM `$annoTable` 
				WHERE `Gene_Name`='$gene' 
				AND (");
		$copy=$annot;
		foreach($annot as $ann){
			$queryText.="`Analyse`='$ann'";
			if (next($copy)) {
				$queryText.=" OR ";
			}
		}
		$queryText.=' )';
		$query=$this->db->query($queryText);
		return $query->result_array();
				
	}
}
