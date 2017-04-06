<?php
/**
* The Expression Database.
*
*  Display Class 
*
*This class contains functions interacting with visualizer Model
*Those function are used for retrieve information for specific tables
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<picard.sylvain3@gmail.com>
*@version 1.0
*@package        ExpressWeb
*@subpackage     Controller
*@category       Libraries
*/
defined('BASEPATH') OR exit('No direct script access allowed');

class Display extends MY_Controller {

        /**  
        * Class constructor
        *
        * Initialize Display class
        *
        * @return        void
        */

	public function __construct()
	{
		parent::__construct();
		$this->output->enable_profiler(false);
		$this->load->model('visualizer');
	}
	
	/** 
	*function showTable
	*
	*get comments and conditions for a specific table
	*used in main page after clicking on 'Show Table'
	*
	**/
	public function showTable()
	{
	    $this->load->library("expression_lib");
            $username=$this->session->userdata['username'];
            $pid=$this->session->userdata['pid'];
            $Path =$this->session->userdata('Path');
            $file = array();
            ### check if current user belong to group Demo only
            $userDemo = $this->expression_lib->in_Demo_grp();
            // Add session variables , file and thrshold(seuil) used //     
            $filename=$this->input->post('file');
            $seuil= (1 - $this->input->post('seuil') );
            $seuilName=str_replace(".","_",$seuil);
            $this->session->set_userdata('seuilName',$seuilName);
            $this->session->set_userdata('fileName',$filename);
            $this->session->set_userdata('seuil',$seuil);
            $data['title'] = "$this->header_name: Show table";
            $data['comment']=$table=$this->visualizer->get_Table_Comment($filename);
            $data['column']=$table=$this->visualizer->get_Column_Names($filename);
            $data['userDemo'] = $userDemo;
            $this->load->view('tableView',$data);
	}

	/**
	*function saveTable
	*
	*After usign previous function, user can sub-select conditions and create a new table
	*This function use the model to create this new table 
	*return @name, string, name of the newly ceated table
	*
	**/
	public function saveTable()
	{
            $conditions=$this->input->post('conditions');
            $filename=$this->input->post('filename');
            $orga=$this->visualizer->get_Organism($filename);
            $organism=$orga->Organism;
            $submitter=$this->session->userdata('username');
            $status = "";
            if($submitter != "demo")
            {
                $group=$this->session->userdata('groups');
                $create_sub_table=$this->visualizer->save_Modif_Table($filename,$conditions,$organism,$submitter,$group);
                $name = $create_sub_table->name;
                $child = $create_sub_table->Child;
                $originalAnnot="Annotation_$organism";
                if($this->db->table_exists($originalAnnot))
                {
                    if($this->db->table_exists($name))
                    {   
                        $status = $this->generic->extract_annot($originalAnnot,$name,$child );
                        # log_message('debug', "Display::saveTable:: 96 $name exist raw status  ".print_r($status,1)."\n");
                        if($status->updTable->info !="") $status=$name;
                    }
                }
                print $status;
            }
            return;
	}
	
	/**
	*function getSignature
	*
	*Use model to extract signture in specific annotation table
	*return @signRes, JSON array with results
	*
	**/
	public function getSignature()
	{
		$analyse=$this->input->post('analyse');
		$file=$this->input->post('file');
		$signRes=$this->visualizer->get_Signatures($file,$analyse);
		print_r(json_encode($signRes));		
	}
	
	/**
	*function getGenesAnnot
	*
	*extract annotation for genes in table
	*return @geneRes, JSON array containing annotations
	**/	
	public function getGenesAnnot()
	{
		$signature=$this->input->post('signature');
		$file=$this->input->post('file');
		if($signature !='' && $file !="" )
		{
                    $geneRes=$this->visualizer->get_Annot_Gene($file,$signature);
                    print_r(json_encode($geneRes));
		}
	}
	
	/**
	*function getClusters
	*
	*get clustering results from database for specific table
	*return @clusterRes, JSON array containg clustering results for each genes
	**/
	public function getClusters()
	{
		$filename=$this->input->post('filename');
		$orderTable=$this->input->post('orderTable');
		$clusterRes=$this->visualizer->get_Cluster($orderTable,$filename);
		print_r(json_encode($clusterRes));
	}
	
	/**
	*function getClustersValues
	*
	*get expression values from database from gene List
	*return @valuesRes, JSON array containg expression values for each gene
	**/
	public function getClustersValues()
	{
		$filename=$this->input->post('filename');
		$geneDict=$this->input->post('geneDict');
		$seuil=$this->input->post('seuil');
		if($filename !='' && $geneDict !="" &&  $seuil!="")
		{
                    $valuesRes=$this->visualizer->get_Values_from_GeneDict($filename,$seuil,$geneDict);
                    print_r(json_encode($valuesRes));
		}
	}
	
	/**
	*function getGenes
	*
	*get all genes name for a specific table
	*return @geneRes, JSON array wontaing all genes names
	**/
	public function getGenes()
	{
		$filename=$this->input->post('filename');
		if($filename !='' )
		$genesRes=$this->visualizer->get_Genes($filename);
		{
		    print_r(json_encode($genesRes));
		}
	}
	
	/**
	*function getNamefromID
	*
	*get genes Name from genes ID in specific table
	*return @geneNames, JSON array containg all genes names
	**/
	public function getNamefromID()
	{
		$filename=$this->input->post('filename');
		$geneID=$this->input->post('geneID');
		$geneNames=array();
		foreach($geneID as $ID)
		{
			$gene=$this->visualizer->get_geneName_from_geneID($filename,$ID);
			$name=$gene[0]['Gene_Name'];
			array_push($geneNames,$name);
		}
		print_r(json_encode($geneNames));
	}

	/**
	*function detail
	*
	**/
	public function detail()
	{
            $table_name = urldecode($this->uri->segment(3));
            $detail = $this->visualizer->lookup_table($table_name,10);
            $size=$table=$this->visualizer->dim_table($table_name);
            $column=$table=$this->visualizer->get_Column_Names($table_name);
            $comment=$table=$this->visualizer->get_Table_Comment($table_name);
            $data = array(
                'title'=>"$this->header_name:  $table_name detail",
                'contents' => 'detail',
                'footer_title' => $this->footer_title,        
                'table_name' => $table_name,
                'detail' => $detail,
                'size' => $size,
                'column' => $column,
                'comment' => $comment
            );
            $this->load->view('templates/template', $data);
        }

	/**
	*function CSV_to_JSON
	*convert CSV file into JSON array
	*
	**/
	public function CSV_to_JSON()
	{
            $file=$this->input->post('filename');
            $geneID=intval($this->input->post('geneID'));
            $csv=array();
            $lines = file($file, FILE_IGNORE_NEW_LINES);
            $good_del=",";
            foreach ($lines as $key => $value)
            {
                if($key == $geneID)
                {
                    $csv[$key] = str_getcsv($value,$good_del);
                }
            }
            print_r(json_encode($csv[$geneID]));
	}	
}	
?>
