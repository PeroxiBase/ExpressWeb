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
        if(!$this->ion_auth->is_admin())
        {
            redirect(base_url()."welcome/restricted", 'refresh');
        }
        $organisms = $this->generic->get_organisms();
        $data = array(
           'title'=>"The Expression Database: Import Toolbox ",
           'contents' => 'upload/import_toolbox',
           'footer_title' => $this->footer_title,
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
        if(!$this->ion_auth->is_admin())
        {
            redirect(base_url()."welcome/restricted", 'refresh');
        }
	$this->load->dbforge();
	$this->load->library('form_validation');
	$info ="";
	if(isset($_POST['selectID']) && isset($_FILES['upload_file'] ) )
	{
            $id= $this->input->post('selectID');
            $toolbox="Toolbox_$id";
            $annotFile=$_FILES['upload_file']['tmp_name'];
            $filename=$_FILES['upload_file']['name'];            
            $Force_Update = $this->input->post('Force_Update');
            $header = $this->input->post('header');

            ############### READ TOOLBOX FILE //
            $csv = array();
            $lines = file($annotFile, FILE_IGNORE_NEW_LINES);
            $error=FALSE;
            $upload_error = "";
            
            #############  find seprator used and check field's number
            $del=$this->expression_lib->readCSV($lines[0]);
            $good_del=$del->delimeter;
            $csv_count = count(str_getcsv($lines[0],"$good_del"));
            
            if($csv_count >6 ) 
            {
                $data = array(
                        'title'=>"$this->header_name: Error in file",
                        'contents' => 'upload/error',
                        'footer_title' => $this->footer_title,
                        'success' => 'success',
                        'error' => "File $file_name contains $csv_count fields !!. Upload aborted",                         
                     );
                
                $this->load->view('templates/template', $data);                 
            }
            else
            {
                foreach ($lines as $key => $value)
                {
                    $csv[$key] =str_getcsv($value,$good_del);
                }
                
                ### remove first line if header
		if(isset($header) && $header ==1) array_shift($csv);
		
                ## check if table exist. If not create it
                if (!$this->db->table_exists($toolbox))
                {
                    ### recover masterGroups
                    $user_id  =$this->session->userdata['user_id'];
                    $get_MasterGrp = $this->generic->get_users_group($user_id);
                    $MasterGrp = array('0' => '1');
                    foreach($get_MasterGrp->result as $row)
                    {
                        $grp_name = $row['name'];
                        $group_id = $row['group_id'];
                        if($grp_name != "demo" OR $grp_name != "members")
                        {
                            array_push($MasterGrp,$group_id);
                        }
                    }
                    $create_toolbox = $this->generic->create_toolbox_table($toolbox,$id,$filename,$MasterGrp);
                   # print "try to create table $toolbox <br />".print_r($create_toolbox,1)."<hr />";
                   $info .= $create_toolbox->info;
                }
                
                ##### if force update truncate table
		if($Force_Update ==1) 
                {
                    $Table_already_set="";
                    $truncate = $this->db->query("TRUNCATE $annoTable");   
                    #$upload_error .= "Erase previous table $annoTable. return : <pre>".print_r($truncate,1)."</pre><br />";
                }
                
                // ADD INTO TOOLBOX TABLE //
                $nl=1;
                foreach($csv as $line)
                {
                    if(count($line) <5 ) 
		     {
		         $info .= "error on line $i . nbr fields ".count($line)."<br />";
		         $i++;
		         continue;
		     }
                    $size0 = strlen($line[0]);$size1 = strlen($line[1]);$size3 = strlen($line[3]);
                    if( strlen($line[0])<=40 && strlen($line[1])<=15 && strlen($line[3])<=255 )
                    {
                        $info .= "$nl GL: ".implode($line,"\t")." <br />";
                        $data=array(
                                'toolbox_name' => $line[0],
                                'gene_name' => strtoupper($line[1]),
                                'annotation' => $line[2],
                                'functional_class' => $line[3],
                                'biological_activity' => $line[4],
                                'WB_Db' => strtoupper($line[5])
                                );
                        ### check if exist in table
                        $query = $this->db->select('*')->where($data)->get($toolbox);
                        ## data not present in toolbox
                        if(count($query->result_array()) ==0 )
                        {
                                $this->db->insert($toolbox, $data);
                        }
                    }
                    else
                    {   
                        $info .= "$nl WL: ".implode($line,"\t")."<br />";
                        $info .= "  size0 ".$line[0]." $size0  size1 ".$line[1]." $size1  size3 ".$line[3]." $size3<br />\n";
                    }
                    $nl++;
                }
                
                // LOAD RESULTS VIEW //
                
                $organisms = $this->generic->get_organisms();
                $data = array(
                        'title'=>"The Expression Database: Import Toolbox ",
                        'contents' => 'upload/import_toolbox',
                        'footer_title' => $this->footer_title,
                        'success' => 'success',
                        'info' => $info, 
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
        if($this->ion_auth->in_group('members'))
        {
            $file=$this->session->fileName;
            $orgaRes=$this->visualizer->get_Organism($file);
            $organism=$orgaRes->Organism;
            $toolboxes=$this->generic->get_Toolbox_Names($organism);
            print_r(json_encode($toolboxes));
	}
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
	$organism=$orgaRes->Organism;
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
	$organism=$orgaRes->Organism;
	$tbName=$_POST['tbName'];
	$fClass=$_POST['fClass'];
	$wpDB=$_POST['wpDB'];
	$genes=$this->generic->get_Genes_from_Toolbox($organism,$tbName,$fClass,$wpDB);
	print_r(json_encode($genes));	
   }	   
}
