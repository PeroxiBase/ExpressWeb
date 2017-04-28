<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
* The Expression Database.
*
*  Visualizer Class 
*
*This class contains functions running query in order to retrieve informations from
*tables created by computational pipeline
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<picard.sylvain3@gmail.com>
*@version 1.0
*@package        ExpressWeb
*@subpackage     Model
*/

class Visualizer extends CI_Model {

        public $title;
        public $content;
        public $date;
        public $nbr;
       
        /**  
        * Class constructor
        *
        * Initialize Visualize class
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
        *function get_geneId_from_geneName
        *
        *get gene ID from gene Name
	*
	*@return Gene_ID
        **/
        
	public function get_geneId_from_geneName($filename,$gene)
	{
		$query = $this->db->query("SELECT Gene_ID FROM $filename WHERE Gene_Name='$gene'");
		$res=$query->result_array();
		return $res[0]['Gene_ID'];
	}

        /**
        *function get_geneName_from_geneID
        *
        *get gene Name from gene ID
	*
	*@return Gene_Name
        **/
	
	public function get_geneName_from_geneID($filename,$geneID)
	{
		$query = $this->db->query("SELECT Gene_Name FROM $filename WHERE Gene_ID='$geneID'");
		$res=$query->result_array();
		return $res;
	}
	
        /**
        *function get_Cluster
        *
        *get clustering results from a specific table
	*
	*@return table_in_array
        **/

	public function get_Cluster($clusterTable,$filename)
	{
	    $result = new stdClass();
	    $sql_query = "SELECT a.*,b.Gene_Name 
	            FROM $clusterTable as a , $filename as b 
	            WHERE a.Gene_ID=b.Gene_ID 
	            ORDER BY cluster";
	    $result->sql = $sql_query;
	    if( !$this->db->simple_query($sql_query) )
	    {
	        $result->error = $this->db->error();
	        $result->nbr = 0;
	        return $result;
	    }
	    else
	    {
                $query = $this->db->query($sql_query);
                $result  = $query->result_array();
                return $result;
             }
            
	}

        /**
        *function get_Values
        *
        *get expression values for a specific table
	*
	*@return table_in_array
        **/
	
	public function get_Values($filename,$seuil)
	{
            $seuilName=str_replace(".","_",$seuil);
            $OrderTable=$filename."_".$seuilName."_Order";
            $query = $this->db->query("SELECT a.*, b.Gene_Name FROM ".$OrderTable." as a, ".$filename." as b WHERE a.Gene_ID=b.Gene_ID");
            return $query->result_array();
	}

        /**
        *function get_Values_from_GeneDict
        *
        *get expression values for specific genes in gene List from choosen table
	*
	*@return values(array)
        **/
	
	public function get_Values_from_GeneDict($filename,$seuil,$geneDict)
	{
            $values=array();
            $seuilName=str_replace(".","_",$seuil);
            $OrderTable=$filename."_".$seuilName."_Order";
            foreach($geneDict as $gene)
            {
                $index=$gene;
                $query = $this->db->query("SELECT a.*, b.Gene_Name FROM $OrderTable as a, $filename as b WHERE a.Gene_ID=$index AND a.Gene_ID=b.Gene_ID");
                foreach($query->result_array() as $res)
                {
                    array_push($values,$res);    
                }
            }
            return $values;		
	}

        /**
        *function get_Cluster_from_GeneDict
        *
        *get clustering results for specific genes in gene List from choosen table
	*
	*@return values(array)
        **/

	public function get_Cluster_from_GeneDict($filename,$seuil,$geneDict)
	{
            $values=array();
            $seuilName=str_replace(".","_",$seuil);
            $tableTest=$filename."_".$seuilName."_Cluster";
            foreach($geneDict as $gene)
            {
                $index=($gene['id']);
                $query = $this->db->query("SELECT * FROM ".$tableTest." WHERE Gene_ID=".$index);
                foreach($query->result_array() as $res)
                {
                        array_push($values,$res);    
                }
            }
            return $values;
	}

        /**
        *function get_Table_Comment
        *
        *get comments for specific table
	*
	*@return comments_in_array
        **/
	
	public function get_Table_Comment($filename)
	{
            $query = $this->db->query("SELECT comment FROM tables WHERE TableName='".$filename."'");
            return $query->result_array();
	}

        /**
        *function get_Column_Names
        *
        *get column names == experimental condition for specific table
	*
	*@return conditions_in_array
        **/
	
	public function get_Column_Names($filename)
	{
            $query = $this->db->query("SELECT COLUMN_NAME 
                                        FROM INFORMATION_SCHEMA.COLUMNS 
                                        WHERE TABLE_SCHEMA='$this->database' 
                                        AND TABLE_NAME='".$filename."'");
            return $query->result_array();
	}

        /**
        *function get_Organism
        *
        *get concerned organism for specific table 
	*
	*@return organism Name
        **/
	
	public function get_Organism($filename)
	{
            $query= $this->db->query("SELECT Organism FROM tables WHERE TableName='".$filename."'");
            return $query->row();		
	}		

        /**
        *function get_Genes
	*
        *get genes in choosen table
	*
	*@return geneName list
        **/
	
	public function get_Genes($filename)
	{
            $query=$this->db->query("SELECT Gene_Name FROM ".$filename);
            return $query->result_array();		
	}

        /**
        *function get_Analyse
        *
        *get analysis contained in organism annotation Table
	*
	*@return Analysis list if table exists
        **/

	public function get_Analyse($organism)
	{
	    $result = new stdClass();
            $annoTable="Annotation_".$organism;
            $errorMsg="There is no Annotation Table for this Organism, please provide one.";
            
            if($this->db->table_exists($annoTable))
            {
                $sql_query = "SELECT DISTINCT Analyse FROM `".$annoTable."` ORDER BY Analyse";
                $query=$this->db->query($sql_query);
                $result->sql = $sql_query;
                $result->nbr = $query->num_rows();
                $result->result = $query->result_array();
                return $result;
            }
            else
            {
                $result->sql = "";
                $result->nbr = 0;
                $result->result = $errorMsg; 
                return $result;
            }
	}

        /**
        *function get_Signatures
        *
        *get signatures contained in organism annotation Table for specified analysis
	*
	*@return Signatures if table exists
        **/

	public function get_Signatures($filename,$analyse)
	{
            $annoTable="Annotation_".$filename;
            $errorMsg="There is no Annotation Table for this Organism, please provide one.";
            if($this->db->table_exists($annoTable))
            {
                $query=$this->db->query("SELECT Signature FROM `$annoTable` WHERE Analyse='$analyse'");
                return $query->result_array();
            }
            else
            {
                return $errorMsg; 
            }
	}

        /**
        *function get_Annot_Gene
        *
        *get annotation description from in organism annotation Table for speciefied signature
	*
	*@return descriptions if table exists
        **/

	public function get_Annot_Gene($filename,$signature)
	{
            $annoTable="Annotation_".$filename;
            $errorMsg="There is no Annotation Table for this Organism, please provide one.";
            if($this->db->table_exists($annoTable))
            {
                $query=$this->db->query("SELECT Gene_Name, Description FROM `$annoTable` WHERE Signature='$signature'");
                return $query->result_array();
            }
            else
            {
                return $errorMsg; 
            }
	}
	
        /**
        *function save_Modif_Table 
        *
        *create new values tables from sub-selection in show table details menu
        *
	*@return newName ; name if the newly created table 
        **/
	
	public function save_Modif_Table($filename,$conditions,$organism,$submitter,$group)
	{
            $this->load->dbforge();
            ### Name for New Table //
            ## get last sub_table ID !!
            $get_lastId = $this->generic->get_last_sub($filename."_");
            $last_id = explode("_",$get_lastId->result);
            $last_id = end($last_id ) + 1;
            $newName=$filename."_".$last_id;
            #log_message('debug', "visualizer::save_Modif_Table($filename,$conditions,$organism,$submitter,$group):: last_id: $last_id\n newName: $newName\n" );
            
            // GET VALUES //
            $queryText="SELECT ";
            foreach($conditions as $cond)
            {
                    if($cond == "Gene_ID")
                    {
                            $queryText.="`".$cond."`";
                    }
                    else
                    {	
                            $queryText.=", `".$cond."`";		
                    }
            }
            $queryText.=" FROM `".$filename."`";
            $queryRes=$this->db->query($queryText)->result_array();

            // CREATE TABLE //
            $colnames=array_keys($queryRes[0]);
            $fields=array();
            foreach($colnames as $col)
            {
                $tab=array();
                if($col == 'Gene_Name'){
                        $tab['type']='VARCHAR';
                        $tab['constraint']='14';
                }
                else if($col == 'Gene_ID'){
                        $tab['type']='INT';
                        $tab['constraint']=10;
                        $tab['unsigned']=true;	
                }
                else{
                        $tab['type']='DOUBLE';
                }
                $fields[$col]=$tab;
            }
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('Gene_ID', TRUE);
            $engine = array('ENGINE' => 'MyISAM');
            $this->dbforge->create_table($newName,FALSE,$engine);
            foreach($queryRes as $res)
            {
                    $this->db->insert($newName, $res); 
            }

            // ADD DEPENDECIES IN TABLES AND TABLEGROUP //
            // TABLES //
            $query = $this->db->query("SELECT id FROM groups WHERE name='".$group[0]."'");
            $queryRes=$query->result_array();
            $masterGroup=$queryRes[0]['id'];
            $data=array(
                    'TableName' => $newName,
                    'MasterGroup' =>$masterGroup,
                    'Organism' => $organism,
                    'Submitter' => $submitter,
                    'version' => '1.0',
                    'comment' => "Sub-Table comming from ".$filename ,
                    'original_file' => $filename,
                    'Root' =>1
            ); 
            $this->db->insert('tables', $data);

            // TABLE_GROUP //
            $query = $this->db->query("SELECT IdTables FROM tables WHERE TableName='".$newName."'");
            $queryRes=$query->result_array();
            $newID=$queryRes[0]['IdTables'];
            
            $query = $this->db->query("SELECT IdTables FROM tables WHERE TableName='".$filename."'");
            $queryRes=$query->result_array();
            $oldID=$queryRes[0]['IdTables'];
            $groups = $this->db->query("SELECT group_id FROM tables_groups WHERE table_id='".$oldID."'")->result_array();
            
            foreach($groups as $gr)
            {
                $data=array('table_id' => $newID,'group_id' => $gr['group_id']);
                $this->db->insert('tables_groups', $data);
            }
            $result = new stdclass;
            $result->name = $newName;
            $result->Child = $newID;
            return $result;
	}
	

        public function lookup_table($table_name,$max='10')
        {
            $sql_query= "SELECT * FROM $table_name LIMIT 0,$max";
            $errorMsg="There is no Table with this name: $table_name";
            if($query= $this->db->query($sql_query))
            {
                return $query->result_array();
            }
            else 
            {
                return $errorMsg; 
            }
        }
        
        public function dim_table($table_name)
        {
            $sql_query= "SELECT count(*) as count FROM $table_name";
            $query= $this->db->query($sql_query);
            $lines= $query->row();
            
            return  $lines->count;
        }
}
