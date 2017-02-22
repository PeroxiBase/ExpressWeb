<?php
/**
* The Expression Database.
*
*  Toolbox Class 
*
* This class upload, parse toolbox (annotation data) files submitted by users
* Uploaded data are analysed to create MYSQl table and insert into it parsed data
* * Data are structured as this:
*       Toolbox Name 	        Gene Name 	Annotation 	Functional Class 	Biological Activity 	Presence in WallProt Databse
*       Flavonoid biosynthesis 	AT2G37040 	PAL1, AtPAL1 	Flavonoid metabolism 	Phe ammonia lyase 	NO
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package expressionWeb
*@subpackage     Controller
*/
defined('BASEPATH') OR exit('No direct script access allowed');

class Toolbox extends MY_Controller 
{
    
     public $required_tag;
     
     
     /**
     * Class constructor
     *
     * Initialize Toolbox class
     *
     * @return        void
     */
    public function __construct()
    {
        //  Obligatoire
        parent::__construct();
        $this->output->enable_profiler(false);
        $this->load->helper(array('language'));
        $this->lang->load('auth');
        $this->required_tag = "<a title=\"required\" class=\"glyphicon glyphicon-asterisk\"></a>";
        if (!$this->ion_auth->logged_in())
        {
                // redirect them to the login page
                redirect(base_url()."auth/login", 'refresh');
        }
        if(!$this->ion_auth->is_admin())
        {
            redirect(base_url()."welcome/restricted", 'refresh');
        }
        $this->load->model("visualizer");
        $this->load->library("expression_lib"); 
        
    }
    
    /**
    * function  index
    *  display toolbox form to upload user file
    * @param string $organisms list of organisms in Db
    * @return integer 
    */  
    public function index()
    {
        $organisms = $this->generic->get_organisms();
        $data = array(
           'title'=>"The Expression Database: Import Toolbox ",
           'contents' => 'upload/import_toolbox',
           'success' => 'none',
           'organisms' => json_encode($organisms->result),
          );
        $this->load->view('templates/template', $data);
    }
   
    /**
    * function 
    * load csv file containing toolbox annotation into database 
    * load view displaying transacttion status 
    *  
    */  
    public function load_toolbox()
    {
	$this->load->dbforge();
	if(isset($_POST['selectID']) && isset($_FILES['upload_file'] ) )
	{
            $id=$_POST['selectID'];
            $toolbox="Toolbox_$id";
            $annotFile=$_FILES['upload_file']['tmp_name'];
            $tables=$this->generic->get_Subtables($id);

            // READ TOOLBOX FILE //
            $csv = array();
            $lines = file($annotFile, FILE_IGNORE_NEW_LINES);
            $error=FALSE;
            foreach ($lines as $key => $value)
            {
                $del=$this->expression_lib->readCSV($value);
                $good_del=$del->delimeter;
                $csv[$key] = str_getcsv($value,$good_del);
            }

            array_shift($csv);

            // ADD INTO TOOLBOX TABLE //
            foreach($csv as $line)
            {
                if( strlen($line[0])<=40 && strlen($line[1])<=15 && strlen($line[3])<=255 )
                {
                    $data=array(
                            'toolbox_name' => $line[0],
                            'gene_name' => strtoupper($line[1]),
                            'annotation' => $line[2],
                            'functional_class' => $line[3],
                            'biological_activity' => $line[4],
                            'WB_Db' => strtoupper($line[5])
                            );
                    $query = $this->db->select('*')->where($data)->get($toolbox);
                    if(count($query->result_array()) ==0 )
                    {
                            $this->db->insert($toolbox, $data);
                    }
                    else
                    {
                            $match=$query->result_array();
                    }
                }
            }
            
            // LOAD RESULTS VIEW //
            if($error == FALSE)
            { 
                $organisms = $this->generic->get_organisms();
                $data = array(
                        'title'=>"The Expression Database: Import Toolbox ",
                        'contents' => 'upload/import_toolbox',
                        'success' => 'success',
                        'organisms' => json_encode($organisms->result),
                     );
                $this->load->view('templates/template', $data);
            }
	}
    }
    
    /**
    * function   
    *retrieve toolbox annotation for specified organism 
    *@params fileName
    *@return JSON array containing annotations
    */  
    public function getToolboxes()
    {
	$file=$this->session->fileName;
	$orgaRes=$this->visualizer->get_Organism($file);
	$organism=$orgaRes[0]['Organism'];
	$toolboxes=$this->generic->get_Toolbox_Names($organism);
	print_r(json_encode($toolboxes));
    }

    /**
    * function 
    *retreive functionnal class from toolbox matching specific fileName  
    * @params fileName
    * @return JSON array containing functional classes
    */  
   public function get_fClass()
   {
	$file=$this->session->fileName;
	$orgaRes=$this->visualizer->get_Organism($file);
	$organism=$orgaRes[0]['Organism'];
	$toolboxName=$_POST['toolbox'];
	$fClass=$this->generic->get_fClass_from_Toolbox($toolboxName,$organism);
	print_r(json_encode($fClass));
   }

    /**
    * function 
    * retrieve gene names in Toolbox from toolbox by toolbox name with or without additionall parameters 
    * @params: filename, toolbox name, functionnal class, functional class
    * @return JSON array containing gene name, biological activty and annotation
    */  
   public function get_Genes_Toolbox()
   {
	$file=$this->session->fileName;
	$orgaRes=$this->visualizer->get_Organism($file);
	$organism=$orgaRes[0]['Organism'];
	$tbName=$_POST['tbName'];
	$fClass=$_POST['fClass'];
	$wpDB=$_POST['wpDB'];
	$genes=$this->generic->get_Genes_from_Toolbox($organism,$tbName,$fClass,$wpDB);
	print_r(json_encode($genes));	
   }	   
}
