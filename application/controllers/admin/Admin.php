<?php
/**
* The Expression Database.
* Admin Class
*
* This class enable you to CRUD admin SQL tables submitted by users
* manage groups , users access to table via groups membership
*
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage     Controller
*/
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends MY_Controller {

     /**
     * Class constructor
     *
     * Initialize Admin class
     *
     * @return	void
     */
     public function __construct()
    {
        //  Obligatoire
        parent::__construct();
        
        $this->output->enable_profiler(true);        
        $this->load->helper(array('language'));
        $this->lang->load('auth');
        if (!$this->ion_auth->logged_in())
        {
            // redirect them to the login page
            redirect(base_url()."auth/login", 'refresh');
        }
    }
     
    public function index()
    {        
        $data = array(
          'title'=>"$this->header_name: ",
          'contents' => "admin/dashboard_view",
          );
        $this->load->view("templates/template", $data);
    }
    
    public function dashboard()
    {        
       
        $data = array(
          'title'=>"$this->header_name: admin_users",
          'contents' => 'admin/dashboard_view',
          'message' => ''
          );
        $this->load->view("templates/template", $data);
    }
    
    public function create_table()
    {
        redirect('../create_table');
    }
    
    public function manage_tables()
    {     
        $groups=$this->generic->get_table_group();
        $tables = $this->generic->get_tables();
        $data = array(
          'title'=>"$this->header_name: manage_tables",
          'contents' => 'admin/manage_tables',
          'tables' => $tables,
          'groups' => $groups
          );
        $this->load->view("templates/template", $data);
    }

    public function remove_tables()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('TableName','Name of Table' , 'required');
            $this->form_validation->set_rules('delete_table[]','List of tables to delete' , 'required');
            if ($this->form_validation->run() == FALSE)
	    {
	        $message = " Please select a table to remove ".print_r($_POST,1)."!";
                $this->session->set_flashdata('message', $message);
                $listeTbls =$this->generic->get_removable_table();
                $tables = $this->generic->get_tables();
                $data = array(
                   'title'=>"$this->header_name: Remove Table ",
                   'contents' => 'admin/remove_tables',
                   'listeTbls' => $listeTbls->result,
                   'tables' => $tables,
                  );
                $this->load->view('templates/template', $data);
	    }
	    else
	    {
	        $TableName  = set_value('TableName'); 
	        $delete_table  = set_value('delete_table'); 
	        $master_table  = set_value('master_table'); 
	        $sql_delete ="";
	        $Do_delete =1;
	        $is_locked= "";
	        $debug ="TableName |$TableName| delete_table $delete_table<br />";
	        $network=$this->config->item('network');
	        $similarity = $this->config->item('similarity');
	        // Check  if we delete the master table $TableName.
	        // If yes delete all other tables contained in master_table even if user doesn't select it
	        // for deletion.
				
	        if(in_array($TableName,$delete_table) )
	        {
	            $debug .= "DEB: 127 in_array(delete_table,TableName) $TableName<br />";
	            // delete all tables !!
	            foreach($master_table as $key => $value)
	            {
			$IdTables = $key;
			$MasterTableName=$value;
			$debug .="DEB 133master_table $key value $value <br />";
                        $delete =$this->generic->get_table_info($MasterTableName);
                        $debug .= "DEB 135:  $delete->sql  <br />";
                        
                        foreach($delete->result as $row)
                        {
                            $is_locked= $this->generic->is_table_lock($row->TABLE_NAME,1);
                            $debug .= "is_table_lock  $is_locked->sql <br />In_use ?: $is_locked->result->In_use<br />";
                            if($is_locked->result->In_use == 0)
                            {
                                $this->db->trans_begin();
                                $query_drop = "DROP table $row->TABLE_NAME;";
                                if($Do_delete) 
                                {
                                    $drop_db = $this->db->query($query_drop);
                                    
                                }
                                if ($this->db->trans_status() === FALSE) 
                                {
                                    $this->db->trans_rollback(); 
                                    $sql_delete .=" WARN : |$query_drop| <br /> unable to remove  $row->TABLE_NAME<br />";
                                   # print "  WARN : DROP table $row->TABLE_NAME; unable to remove  $row->TABLE_NAME<br />";
                                    #break;
                                }
                                else
                                {
                                    $sql_delete .="Table $row->TABLE_NAME deleted<br /><ul>";
                                    $this->db->trans_commit();
                                }
                                
                                if( $row->TABLE_NAME == $MasterTableName)
                                {
                                    // remove ref in table && table_groups
                                    $this->db->trans_begin();
                                    $query_delete_grp ="DELETE FROM `tables_groups` WHERE  `table_id`='$IdTables';"; 
                                    if($Do_delete)  
                                    {
                                         $drop_db = $this->db->query($query_delete_grp);
                                    }
                                   if ($this->db->trans_status() === FALSE)
                                    { 
                                        $this->db->trans_rollback(); 
                                        $sql_delete .=" WARN : |$query_delete_grp| <br />unable to update status for table $row->TABLE_NAME in tables_groups<br />";
                                        
                                        #break;
                                    }
                                    else
                                    {
                                        $sql_delete .=" <li>Table $row->TABLE_NAME removed from tables_groups</li>";
                                        $this->db->trans_commit();
                                    }
                                    
                                    $this->db->trans_begin();
                                    $query_delete_table ="DELETE FROM `tables` WHERE  `IdTables`='$IdTables';"; 
                                    if($Do_delete)  
                                    {
                                         $drop_db = $this->db->query($query_delete_table);
                                    }
                                   if ($this->db->trans_status() === FALSE)
                                    { 
                                        $this->db->trans_rollback(); 
                                        $sql_delete .=" WARN : |$query_delete_grp| <br />unable to update status for table $row->TABLE_NAME in tables<br />";
                                        
                                        #break;
                                    }
                                    else
                                    {
                                        $sql_delete .= " <li>Table $row->TABLE_NAME removed from tables</li>";
                                         // delete computed files in network and similarity dir
                                        $jsonName = preg_replace("/_Cluster|_Order/","",$value);
                                        $sql_delete .=" <li>Search file ${network}Edges$jsonName.json </li>";
                                        if(file_exists("${network}Edges$jsonName.json"))
                                        {
                                            unlink("${network}Edges$jsonName.json");
                                            $sql_delete .=" <li>Edges$jsonName.json removed from $network</li>";
                                            unlink("${network}Nodes$jsonName.json");
                                            $sql_delete .=" <li>Nodes$jsonName.json removed from $network</li>";
                                        }
                                        $sql_delete .=" </ul><hr />";
                                        $this->db->trans_commit();
                                        
                                    }
                                }
                            }
                            else
                            {
                                $sql_delete .=" WARN : table locked !! $is_locked->sql<br />";
                            }
                        } // foreach delete->result
                    } // foreach master_table
	        } // IF TableName in delete_table
	        else
	        {
	            $debug .= "DEB: 203 Delete other table than $TableName <br />";	             
                    foreach($delete_table as $key=>$value)
                    {
                        $is_locked = $this->generic->is_table_lock($value,1);
                         $debug .= "is_table_lock  $is_locked->sql <br />In_use ?: ".$is_locked->result->In_use."<br />";
                           
                        $debug .= "DEB: 207  sql: $is_locked->sql <br>nb $is_locked->nbr<br />";
                        if($is_locked->result->In_use==0)
                        {
                             $this->db->trans_begin();
                            $query_drop = "DROP table $value;";
                            if($Do_delete) 
                            {
                                $Do_del = $this->db->query($query_drop); 
                            }
                            if ($this->db->trans_status() === FALSE) 
                            {
                                $this->db->trans_rollback(); 
                                $sql_delete .=" unable to remove  $value / |$query_drop|<br />";
                                #break;
                            }
                            else
                            {
                                $sql_delete .="Table $value deleted<br /><ul>";
                                $this->db->trans_commit();
                            }
                             // remove ref in table_groups
                             $this->db->trans_begin();
                            $IdTables = array_search($value,$master_table);
                            $query_delete_grp ="DELETE FROM `tables_groups` WHERE  `table_id`='$IdTables';";
                            
                            if($Do_delete)
                            {
                                $DoDel = $this->db->query($query_delete_grp);
                            }
                            
                            if ($this->db->trans_status() === FALSE)
                            { 
                                $this->db->trans_rollback(); 
                                $sql_delete .=" WARN : |$query_delete_grp| <br />unable to update status for table $value in tables_groups<br />";                                
                                #break;
                            }
                            else
                            {
                                $sql_delete .="<li>Table $value removed from tables_groups</li>";
                                $this->db->trans_commit();
                            }
                            // remove ref in table
                            $this->db->trans_begin();
                            $IdTables = array_search($value,$master_table);
                            $query_delete_table ="DELETE FROM `tables` WHERE  `IdTables`='$IdTables';"; 
                            if($Do_delete)
                            {
                                 $DoDel = $this->db->query($query_delete_table);
                            }
                            
                            if ($this->db->trans_status() === FALSE)
                            { 
                                $this->db->trans_rollback(); 
                                $sql_delete .=" WARN : |$query_delete_grp| <br />unable to update status for table $value in tables<br />";
                                
                                #break;
                            }
                            else
                            {
                                $sql_delete .=" <li>Table $value removed from tables</li>";
                                
                                // delete computed files in network and similarity dir
                                
                                $jsonName = preg_replace("/_Cluster|_Order/","",$value);
                                $sql_delete .=" <li>Search file ${network}Edges$jsonName.json </li>";
                                if(file_exists("${network}Edges$jsonName.json"))
                                {
                                    unlink("${network}Edges$jsonName.json");
                                    $sql_delete .=" <li>Edges$jsonName.json removed from $network</li>";
                                    unlink("${network}Nodes$jsonName.json");
                                    $sql_delete .=" <li>Nodes$jsonName.json removed from $network</li>";
                                }
                                $sql_delete .=" </ul><hr />";
                                $this->db->trans_commit();
                                
                            }
                            
                           /* */
                        }
                        else
                        {
                            $sql_delete .=" WARN225 : table locked !! <br />";
                        }        
                    }
	        }
	   
                $data = array(
                      'title'=>"$this->header_name: manage_tables",
                      'contents' => 'admin/update_result',
                      'POST' => $_POST,
                      'sql_update_table' => '',
                      'update_result' => $sql_delete ,
                      'error' => "Opps master_table $master_table",
                      'currentGroups' =>  $is_locked,
                      'debug' => $debug
                      );
                    $this->load->view("templates/template", $data);
            }
        }
        else
        {
            #$listeTbls = $this->db->list_tables();
            $listeTbls =$this->generic->get_removable_table();
            $tables = $this->generic->get_tables();
            $data = array(
               'title'=>"$this->header_name: Remove Table ",
               'contents' => 'admin/remove_tables',
               'listeTbls' => $listeTbls->result,
               'tables' => $tables,
              );
            $this->load->view('templates/template', $data);
        }
    }    
 
    public function edit_table()
    {
         if (isset($_POST) && !empty($_POST))
        {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('TableName','Name of Table' , 'required');
            $this->form_validation->set_rules('Submitter','Submitter' , 'required');
            $this->form_validation->set_rules('version','version' , 'required');
            if ($this->form_validation->run() === TRUE)
	    {
	        
	        $IdTables  = set_value('IdTables');
                $TableName  = set_value('TableName'); 
                $MasterGroup  = set_value('MasterGroup');
                $GroupName  = set_value('name'); 
                $Organism  = set_value('Organism');
                $Submitter  = set_value('Submitter');
                $version  = set_value('version');
                $comment  = set_value('comment');
                $groups  = set_value('groups');
                $currentGroups  = set_value('currentGroups');
                ######### update table ######################
                $sql_update_table = "UPDATE tables 
                 SET TableName = '$TableName',MasterGroup = '$MasterGroup',
                 Organism = '$Organism',Submitter = '$Submitter',version ='$version',comment='$comment'
                WHERE IdTables='$IdTables' ";
                
                $do_update= $this->db->query($sql_update_table);
                $sql_groups_update ="";
                
                
                //foreach(
                foreach($groups as $key=>$group_id)
                {
                    if(in_array($group_id, $currentGroups))
                    {
                            $IdTblGrp=array_search($group_id,$currentGroups);
                            $sql_groups_update .="UPDATE tables_groups 
                                            set table_id='$IdTables' ,group_id= '$group_id'
                                           WHERE id=$IdTblGrp;<br /> ";
                          $sql_groups ="UPDATE tables_groups set table_id='$IdTables' ,group_id= '$group_id' WHERE id=$IdTblGrp;";
                           
                            unset($currentGroups[$IdTblGrp]);
                    }
                    else
                    {
                       $sql_groups = "INSERT INTO tables_groups (id,table_id,group_id)
                                    VALUES(NULL,'$IdTables','$group_id');";
                       $sql_groups_update .="REPLACE into tables_groups (table_id,group_id) values('$IdTables','$group_id');<br />";
                       #$sql_groups ="REPLACE into tables_groups (table_id,group_id) values('$IdTables','$group_id');";
                    }
                    $do_update_group= $this->db->query($sql_groups);
                }
                
                if(count($currentGroups) >0)
                {
                    foreach($currentGroups as $id=>$group)
                    {
                        $sql_groups_update .="DELETE FROM tables_groups 
                                              WHERE id=$id ;<br /> ";
                        $sql_delete = "DELETE FROM tables_groups WHERE id=$id ;";
                         $do_delete_group= $this->db->query($sql_delete);
                    }
                }
                $error = $currentGroups;
                redirect('admin/manage_tables');
            }
            else
            {
                $data = array(
                  'title'=>"$this->header_name: manage_tables",
                  'contents' => 'admin/update_result',
                  'POST' => $_POST,
                  'sql_update_table' => '',
                  'sql_groups_update' => '',
                  'error' =>'Opps'
                  );
                $this->load->view("templates/template", $data);
            }
        }
        else
        {
            $id= urldecode($this->uri->segment(3));
            $tables = $this->generic->get_tables($id);
            $currentGroups=$this->generic->get_table_group($id);
            $organisms= $this->generic->get_organisms();
            $groups=$this->ion_auth->groups()->result_array();
            
            foreach ($tables->result as $row)
            {
                $IdTables  = $row['IdTables'];
                $TableName  = $row['TableName']; 
                $MasterGroup  = $row['MasterGroup'];
                $GroupName  = $row['name']; 
                $Organism  = $row['idOrganisms'];
                $Submitter  = $row['Submitter'];
                $version  = $row['version'];
                $comment  = $row['comment'];
                $Root = $row['Root'];
            }
            
            $options_group = array();
            foreach ($groups as $group)
            {
                $gID=$group['id'];
                $gname=$group['name'];
                $options_group[$gID] =$gname;
            }
            
             $options_organisms = array();
            foreach ($organisms->result as $orga)
            {
                $gID=$orga['idOrganisms'];
                $gname=$orga['Organism'];
                $options_organisms[$gID] =$gname;
            }
            $data = array(
                'title'=>"$this->header_name: edit table $id ",
                'description' => 'La description de la page pour les moteurs de recherche',
                'keywords' => 'les, mots, clés, de, la, page',
                'contents' => 'admin/edit_table',          
                'IdTables' => $IdTables ,  
                'TableName' => $TableName ,  
                'MasterGroup' => $MasterGroup ,  
                'options_group' => $options_group,
                'options_organisms' => $options_organisms,
                'GroupName' => $GroupName ,  
                'Organism' => $Organism , 
                'Submitter' => $Submitter ,  
                'version' => $version , 
                'comment' => $comment ,
                'groups' => $groups,
                'currentGroups' => $currentGroups
              );
            $this->load->view("templates/template", $data);
        }
    }
    
    public function manage_users()
    {        
        $groups=$this->generic->get_users_group();
        $users = $this->generic->get_users();
        $data = array(
          'title'=>"$this->header_name: Users managment",
          'description' => 'La description de la page pour les moteurs de recherche',
          'keywords' => 'les, mots, clés, de, la, page',
          'contents' => 'admin/manage_users',
          'message' => '',
          'users' => $users,
          'groups' => $groups
          );
        $this->load->view("templates/template", $data);
    }
    
    public function edit_user()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('username','Login Name' , 'required');
            $this->form_validation->set_rules('first_name','first_name' , 'required');
            $this->form_validation->set_rules('last_name','last_name' , 'required');
            $this->form_validation->set_rules('company','company' , 'required');
            if ($this->form_validation->run() === TRUE)
	    {
	        
	        $Id  = set_value('Id');
                $username  = set_value('username'); 
                $first_name  = set_value('first_name');
                $last_name  = set_value('last_name');
                $company  = set_value('company');
                $groups  = set_value('groups');
                $currentGroups  = set_value('currentGroups');
                ######### update table ######################
                $sql_update_table = "UPDATE users 
                 SET username = '$username',first_name = '$first_name',
                 last_name = '$last_name',company = '$company'
                 WHERE id='$Id' ";
                
                $do_update= $this->db->query($sql_update_table);
                $sql_groups_update ="";
                
                
                //foreach(
                foreach($groups as $key=>$group_id)
                {
                    if(in_array($group_id, $currentGroups))
                    {
                        $IdTblGrp=array_search($group_id,$currentGroups);
                        $sql_groups_update .="UPDATE users_groups set user_id='$Id' ,group_id= '$group_id' WHERE id=$IdTblGrp;<br /> ";                           
                        unset($currentGroups[$IdTblGrp]);
                    }
                    else
                    {
                       $sql_groups_update .="INSERT INTO users_groups (id,user_id,group_id) VALUES(NULL,'$Id','$group_id');<br /> ";
                      
                    }
                    $do_update_group= $this->db->query($sql_groups);
                }
                
                if(count($currentGroups) >0)
                {
                    foreach($currentGroups as $id=>$group)
                    {
                        $sql_groups_update .="DELETE FROM users_groups  WHERE id=$id ;<br /> ";
                        $sql_delete = "DELETE FROM users_groups WHERE id=$id ;";
                        $do_delete_group= $this->db->query($sql_delete);
                    }
                }
                $error = $currentGroups;
                redirect('admin/manage_users');
            }
            else
            {
                $data = array(
                  'title'=>"$this->header_name: Error Update User",
                  'contents' => 'admin/update_result',
                  'POST' => $_POST,
                  'sql_update_table' => '',
                  'sql_groups_update' => '',
                  'error' =>'Opps'
                  );
                $this->load->view("templates/template", $data);
            }
        }
        else
        {
            $id= urldecode($this->uri->segment(3));
            $users = $this->generic->get_users($id);
            $currentGroups=$this->generic->get_users_group($id);
            $groups=$this->ion_auth->groups()->result_array();
            
            foreach ($users->result as $row)
            {
                $Id  = $row['id'];
                $username  = $row['username']; 
                $first_name  = $row['first_name'];
                $last_name  = $row['last_name']; 
                $company  = $row['company'];
                $email = $row['email'];
            }
            
            $options_group = array();
            foreach ($groups as $group)
            {
                $gID=$group['id'];
                $gname=$group['name'];
                $options_group[$gID] =$gname;
            }
            
            $data = array(
              'title'=>"$this->header_name: edit table $id ",
              'description' => 'La description de la page pour les moteurs de recherche',
              'keywords' => 'les, mots, clés, de, la, page',
              'contents' => 'admin/edit_user',          
              'Id' => $Id ,  
                'username' => $username ,  
                'first_name' => $first_name ,  
                'last_name' => $last_name,
                'company' => $company,
              'groups' => $groups,
              'email' =>$email,
              'currentGroups' => $currentGroups->result
              );
            $this->load->view("templates/template", $data);
        }
    }
    
    
    public function manage_organism()
    {        
        $organisms = $this->generic->get_organisms();
        $data = array(
          'title'=>"$this->header_name: Organisms managment",
          'description' => 'La description de la page pour les moteurs de recherche',
          'keywords' => 'les, mots, clés, de, la, page',
          'contents' => 'admin/manage_organisms',
          'message' => '',
          'Organisms' => $organisms
          );
    $this->load->view("templates/template", $data);
    }
    
    public function edit_organism()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('Organism','Name of Organism' , 'trim|required');
            if ($this->form_validation->run() === TRUE)
	    {
	        $idOrganisms  = set_value('idOrganisms');
                $Organism  = set_value('Organism'); 
                $Max_transcript_size  = set_value('Max_transcript_size'); 
                ######### update table ######################
                $sql_update_table = "UPDATE Organisms SET Organism = '$Organism' ,Max_transcript_size = '$Max_transcript_size' WHERE idOrganisms='$idOrganisms' ";
                $do_update= $this->db->query($sql_update_table);
                redirect('admin/manage_organism');
            }             
            else
            {
                    $id= urldecode($this->uri->segment(3));
                    $organisms= $this->generic->get_organisms($id);
                    $Organism =$organisms->result->Organism;
                    $Max_transcript_size = $organisms->result->Max_transcript_size;
                    $data = array(
                      'title'=>"$this->header_name: edit organism $Organism",
                      'contents' => 'admin/edit_organism',          
                      'idOrganisms' => $id,  
                      'Organism' => $Organism ,  
                      'Max_transcript_size' => $Max_transcript_size,
                      'options_organisms' => $options_organisms,
                      );
                    $this->load->view("templates/template", $data);
            }
        }
        else
        {
        $id= urldecode($this->uri->segment(3));
        $organisms= $this->generic->get_organisms($id);
        $Organism =$organisms->result->Organism;
        $Max_transcript_size = $organisms->result->Max_transcript_size;
        $data = array(
          'title'=>"$this->header_name: edit organism $Organism",
          'contents' => 'admin/edit_organism',          
          'idOrganisms' => $id,  
          'Organism' => $Organism , 
          'Max_transcript_size' => $Max_transcript_size
        //  'options_organisms' => $options_organisms,
          );
        $this->load->view("templates/template", $data);
        }
    }
  
    public function add_organism()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('Organism','Name of Organism' , 'trim|required');
            if ($this->form_validation->run() === TRUE)
	    {
	        $idOrganisms  = set_value('idOrganisms');
                $Organism  = set_value('Organism'); 
                $Max_transcript_size  = set_value('Max_transcript_size'); 
                ###### check #####################
                $check= $this->db->query("SELECT Organism FROM Organisms WHERE Organism='$Organism' ");
                if($check->num_rows() >0)
                {
                     $message = " This Organism $Organism is already in Database";
                     $this->session->set_flashdata('message', $message);
                     $data = array(
                      'title'=>"$this->header_name: Add organism ",
                      'contents' => 'admin/add_organism',    
                      );
                    $this->load->view("templates/template", $data);
                }
                else
                {
                    ######### update table ######################
                    $sql_update_table = "INSERT INTO Organisms  (Organism,Max_transcript_size ) VALUES ('$Organism' ,'$Max_transcript_size')";
                    $do_update= $this->db->query($sql_update_table);

                    ## Create Toolbox Table ##
                    $checkID= $this->db->query("SELECT idOrganisms FROM Organisms WHERE Organism='$Organism' ");
                    $orgID=$checkID->result_array();
                    $ID=$orgID[0]['idOrganisms'];
                    $toolboxTable="Toolbox_$ID";
                    if(! $this->db->table_exists($toolboxTable)){
                        $sql_create_toolbox="CREATE TABLE $toolboxTable (
                            toolbox_".$ID."_ID INT(10) PRIMARY KEY NOT NULL AUTO_INCREMENT,
                            toolbox_name VARCHAR(40) NOT NULL,
                            gene_name VARCHAR(15) NOT NULL ,
                            annotation VARCHAR(25),
                            functional_class VARCHAR(255),
                            biological_activity TEXT,
                            WB_Db VARCHAR(10) NOT NULL
                        )";
                        $this->db->query($sql_create_toolbox);
                    }   
                    redirect('admin/manage_organism');

                }
            }             
            else
            {
                    $id= urldecode($this->uri->segment(3));
                    $organisms= $this->generic->get_organisms();
                    
                    $options_organisms = array();
                    foreach ($organisms->result as $orga)
                    {
                        $gID=$orga['idOrganisms'];
                        $gname=$orga['Organism'];
                        if($gID== $id) $Organism = $gname;
                        $options_organisms[$gID] =$gname;
                        $Max_transcript_size = $orga['Max_transcript_size'];
                    }                           
                    $data = array(
                      'title'=>"$this->header_name: Add organism ",
                      'contents' => 'admin/add_organism',          
                      'idOrganisms' => $id,  
                      'Organism' => $Organism ,  
                      'Max_transcript_size' => $Max_transcript_size,
                      'options_organisms' => $options_organisms,
                      );
                    $this->load->view("templates/template", $data);
            }
        }
        else
        {
          $data = array(
              'title'=>"$this->header_name: Add organism ",
              'contents' => 'admin/add_organism',          
              );
          $this->load->view("templates/template", $data);
        }
    }
  
    
    public function admin_users()
    {        
        $data = array(
          'title'=>"$this->header_name: admin_users",
          'description' => 'La description de la page pour les moteurs de recherche',
          'keywords' => 'les, mots, clés, de, la, page',
          'contents' => 'admin/login',
          'message' => ''
          );
        $this->load->view("templates/template", $data);
    }
    
}
