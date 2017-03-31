<?php
/**
* The Expression Database.
*
*  Visual Class 
*
* This class loads the main pages and run calculations
* The third function is used to download clustering results from database
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<picard.sylvain3@gmail.com>
*@version 1.0
*@package        ExpressWeb
*@subpackage     Controller
*/

defined('BASEPATH') OR exit('No direct script access allowed');

class Visual extends MY_Controller 
{

    /**  
    * Class constructor
    *
    * Initialize Visual class
    *
    * @return        void
    */
    public function __construct()
    {
            parent::__construct();
            #### Your own constructor code
            $this->output->enable_profiler(false);
            $this->load->helper(array('language'));
            $this->load->library("expression_lib"); 
            $this->lang->load('auth');
            $this->load->model('visualizer');
            if (!$this->ion_auth->logged_in())
            {
                redirect("auth/login", 'refresh');
            }            
    }
    
    /**
    * function index
    * 
    * test user connection with init_user function
    *
    **/	
    public function index()
    {
            $this->init_user();
    }

    /**
    * function howPage
    *
    *  display user guide page
    *
    **/

    public function howPage()
    {
       $data['contents']= 'how_it_works';
       $data['title']= "$this->header_name: How to";
       $data['footer_title']= "$this->footer_title";
       $this->load->view('templates/template_visual',$data);
    }	


    /**
    * function init_user
    *
    * Test user connection and retrieve 
    * usable tables for user
    * load main view 
    *
    **/
    public function init_user()
    {
        #### Load user folder. 
        $GetWS=$this->expression_lib->working_space('','Upload'); 
        if(isset($GetWS->Path))
        {
            $Path= $GetWS->Path;
            $pid = $GetWS->pid;
            $userID=$this->session->user_id;
            $this->session->set_userdata('Path',$Path);
            $this->session->set_userdata('pid',$pid);
        }   
    
       #### User variables #### 
       $username = $this->session->userdata('username');
       ### check if current user belong to group Demo only
       ### running jobs and create sub_selection of dataset will be forbiden
       $userDemo = $this->expression_lib->in_Demo_grp();
    
       #### Get Database Tables for User ####
       $userTables=$this->generic->get_table_members($userID); #### see Generic.php model
       $tables=$tables_tmp=array();
       $option_div ="";
       ###### for each $userTables search for precomputed Cluster
       ###### result will be displayed on select in main view
       foreach($userTables->result as $row)
       {
            $IdTables = $row['IdTables'];
            $tableName = $row['TableName'];
            $get_child = $this->generic->get_child($IdTables);
            if($get_child->nbr >0)
            {
                $tbl_seuil ="";
                $option_div .="<div class=\"$tableName box bg\" >pre-calculated threshold: "; 
                foreach($get_child->result as $row)
                {
                    $IdTables2 = $row->IdTables;
                    $tableName2 = $row->TableName;
                    $tbl_seuil = trim(preg_replace("/$tableName|_Cluster/","",$tableName2),"_");
                    $tbl_seuil = preg_replace("/_/",".",$tbl_seuil);
                    $option_div .=" <a class=\"seuil_val\">$tbl_seuil</a> ";
                }
                $option_div .="</div>\n";
            }
            else
            {
            $option_div .="<div class=\"$tableName box bg\" >dataset have not been computed</div>"; 
            }
       }
       
       $data = array(
          'title'=> "$this->header_name: Visual",
          'contents' => 'main',
          'footer_title' => $this->footer_title,
          'tables' => $userTables,
          'userTables' => $tables,
          'pid' => $pid,
          'option_div' =>$option_div,
          'userDemo' =>$userDemo
          );
       $this->load->view('templates/template_visual',$data);
    }
    
    /**
    * function load
    *
    * run scripts for clustering definition
    * retrieve parameters from main page form and launch command
    * if results already exists in database, show results without running calculations
    * 
    **/

    public function load()
    {
        #### session control ####
        if (isset($_SESSION['username']))
        {
            $username=$this->session->userdata['username'];
            $pid= $this->expression_lib->getPid();
            $this->session->set_userdata('pid',$pid);
            $Path =$this->session->userdata('Path');
            $data['dir']=$Path;
            $data['username']=$username;
            $data['pid']=$pid;			
            $data['footer_title']= "$this->footer_title";
            $file = array();
            #### Add session variables , file and threshold(seuil) used ####
            ################  clean post ########################
            $this->load->library('form_validation');
            $filename = $this->input->post('file');            
            $seuil= $this->input->post('clusterSeuil');
            if(!isset($filename) OR !isset($seuil) )
            {
                $message = "A problem occurs. No filename or threshold value available.<br /> Please start again.<br /> Contact Web manager if problem occurs one more time";
                $this->session->set_flashdata('message', $message);
                redirect('visual/fatal');
            }
            $seuilName=str_replace(".","_",$seuil);
            $this->session->set_userdata('seuilName',$seuilName);
            $this->session->set_userdata('fileName',$filename);
            $this->session->set_userdata('seuil',$seuil);
            $this->session->set_userdata('errorCpt',0);
            #### Display results according to the choosen option ( global clustering or gene Specific correlation ) ####
                        
            #### Gene Specific ####
            if($this->input->post('geneSelect') && $this->input->post('geneSelect') =='geneChecked')
            {
                    $gene=$_POST['gene'];
                    $data['seuil']= $this->input->post('corTh');
                    $data['gene']=$gene;
                    #### Extract the values and correlation for each gene ####
                    $res=$this->visualizer->get_CoexMatrix($filename,$seuil,$gene); 	
                    echo "<pre id=coexMatrix style='display:none'>";
                    print_r(json_encode($res)); 
                    echo "</pre>";
                    #### Load to view ####
                    $this->load->view('genespe',$data);
            }

            #### Global Clustering ####
            else
            {
                #### Get filename threshold and option(simple or double clustering) Double is actually disabled ####
                $data['seuil']= $this->input->post('clusterSeuil');
                $data['name']=$this->session->userdata['fileName'];
                
                $optionclus=0;#### Simple clustering by default. Set to 1 to debug cluster's jobs ####
                if($this->input->post('doubleclus') && $this->input->post('doubleclus') == 'doubleC')
                {
                        $optionclus=1;
                }
                $data['option']=$optionclus;		
                
                #### Countdown Construction ####
                if( $this->input->post('hf'))
                {
                        $data['h']= $this->input->post('hf');
                        $data['m']= $this->input->post('mf');
                        $data['s']= $this->input->post('sf');
                }
                else
                {
                        $data['h']='00';
                        $data['m']='00';
                        $data['s']='0';
                }
                #### R and Python scripts, launched on the a Computer Cluster to construct clusters and network####
                #### Clustering Results are uploaded into our databse, network results are files in the $Path directory ####
                $launch=$this->config->item('launch_cluster');
                $web_path = $this->config->item('web_path');
                $work_cluster = $this->config->item('work_cluster');
                $work_scripts = $this->config->item('work_scripts');
                $qdelay = $this->config->item('qdelay');
                
                $command=$launch." ".$pid." ".$filename." ".$seuil." ".$username." ".$optionclus;
                
                #### Parse names for database table selection ####
                $seuilName=str_replace(".","_",$seuil);
                #### Clustering Result Table ####
                $tableTest=$filename."_".$seuilName."_Cluster";
                
                #### File contains job status on the cluster and results datas. #### 
                $jobfile = "$work_scripts/Job_$pid.txt"; 
                $EndFile = "$work_scripts/EndJob_$pid.txt";
                $EndSimilarityFile = "$work_cluster/files/$username/${filename}_Ended";
                $EndNetworkFile = "$work_cluster/files/$username/EndJob_".$filename.$seuilName.".json";
                
                #### If results table doesn't exists , launch script else don't launch ####
                if(!$this->db->table_exists($tableTest))
                {
                    #### add new tables names in Tables/
                    $orga=$this->visualizer->get_Organism($filename);
                    $organism=$orga->Organism;
                    $data['organism']=$organism;
                    #### create a temporary entry in reference table 'tables'
                    $this->generic->update_Tables_On_Clustering($filename,$seuilName,$organism);
                    
                    #### Copy scripts files in cluster location
                    $Copy_files= array('clean.sh','config.R','DBClustering.R','DBCreateNetwork.py','execute_bash.sh','ExpressWeb.conf');

                    foreach($Copy_files as $files)
                    {
                        $cp =exec("cp -f ${web_path}/assets/scripts/$files ${work_cluster}/scripts/",$RF);
                    }
                    
                    #### Load waiting views with the countdown ####                    
                    #### first view used to launch job cluster.
                    #### after 10 sec load visual/show
                    $loop_time= 10;
                    
                    $data['EndSimilarityFile']= $EndSimilarityFile;
                    $data['EndNetworkFile']= $EndNetworkFile;
                    $data['EndFile']= $EndFile;
                    $data['jobfile']= $jobfile;
                    $data['work_cluster']= $work_cluster;
                                                        
                    $data['title'] = "$this->header_name: Clustering computing 0";
                    
                    $data['message'] = "computing ...   ";
                    $data['loop_time']= $loop_time*1000;
                    if(file_exists($EndFile))  unlink($EndFile);
                    $this->load->view('templates/header',$data);
                    $this->load->view('templates/menu',$data);
                    $this->load->view('wait',$data);
                    $this->load->view('templates/footer',$data);
                    exec("$command >>$jobfile &");
                }
                else
                {
                    $loop_time = 1;
                    $data['title'] = "$this->header_name: Preparing data";
                    $data['contents'] ='wait';
                    $data['message'] = "Preparing data..";
                    $data['loop_time']= $loop_time*1000;
                    $data['EndFile']= $EndFile;
                    $work_scripts = $this->config->item('work_scripts');
                    $EndFile = "$work_scripts/EndJob_$pid.txt";
                    $this->session->set_userdata('processed_'.$pid,'1');
                    $this->load->view('templates/template',$data);
                  ####  
                }
            }
        }
        #### If user not connected, back to login page ####
        else
        {
                $this->init_user();
        }
    }

    /**
    * function show
    *
    * launched by waittemp view
    * test if calculations are finished
    * if not, print chronometer and test again
    * if yes, display results page
    * if too much test are runned, display error Page
    * see config file for max number of test
    *
    **/
	
    public function show()
    {
        $t0= microtime(true);
        
        #### predefined configuration variable config/ExpressWeb.php
        $network = $this->config->item('network');
        $maxError = $this->config->item('maxError');
        $qdelay = $this->config->item('qdelay');
        $web_path = $this->config->item('web_path');
        $work_cluster = $this->config->item('work_cluster');
        $work_scripts = $this->config->item('work_scripts');
        $option = $pid = "";
        if( $this->input->post('option') ) $option= $this->input->post('option');
        if( $this->input->post('pid') ) 
        {
            $pid= $this->input->post('pid');            
        }
        else
        {
            #### may be a bug if show didn't receive pid as post value ....
            $pid = $this->session->userdata['pid'];
        }
        
        #### Get session data ####
        $start_memory = memory_get_usage();
        $username=$this->session->userdata['username'];
        $name=$this->session->userdata['fileName'];	
        $seuil=$this->session->userdata['seuil'];	
        $seuilName=$this->session->userdata['seuilName'];		
        $dir=$this->session->userdata['working_path'];
        $filename=$this->session->fileName;
        $errorCpt=$this->session->userdata('errorCpt');        
        $processed= $this->session->userdata('processed_'.$pid);
        #$processed= 1;
        #### Get Files data        
        $fileTest=$network."/Nodes".$filename."_".$seuilName.".json";
        $jobfile = "$work_scripts/Job_$pid.txt"; #### File containg job status on the cluster #### 
        $EndFile = "$work_scripts/EndJob_$pid.txt";
        $EndSimilarityFile = "$work_cluster/files/$username/${filename}_Ended";
        $EndNetworkFile = "$work_cluster/files/$username/EndJob_".$filename.$seuilName.".json";
        $tableTest = $name."_".$seuilName."_Cluster";
        $tableValues = $name."_".$seuilName."_Order";
        $loop_time=1000;
        
        $data['seuil']=$seuil; 
        $data['seuilName']=$seuilName;
        $data['option']=$option;
        $data['pid'] = $pid;
        $data['filename']=$filename;	
        $data['EndSimilarityFile']= $EndSimilarityFile;
        $data['EndNetworkFile']= $EndNetworkFile;
        $data['EndFile']= $EndFile;
        $data['jobfile']= $jobfile;                
        $data['work_cluster']= $work_cluster;		
        $data['footer_title']= $this->footer_title;
        
        if( $processed == 0 && $errorCpt<$maxError && !file_exists($EndFile) )
        {
           #### Get countdown values ####
            $data['pid']=$pid;
            $data['h']= $this->input->post('hf');
            $data['m']= $this->input->post('mf');
            $data['s']= $this->input->post('sf');
            if($data['s']=='60')
            {
                    $data['s']='0';
                    $data['m']+=1;
            }
            if($data['m']=='60')
            {
                    $data['m']='0';
                    $data['h']+=1;
            }
            else
            {
                    $data['s']=$data['s']+1;
            }
             #### wait before loading the view ####
             
            #sleep($qdelay);
            $errorCpt++;
            $this->session->set_userdata('errorCpt',$errorCpt);
            $data['title'] = "$this->header_name : Clustering computing $errorCpt";
            $data['contents'] ='waittemp';
            $data['message'] = "";
            $data['loop_time']= $qdelay*1000; ## *1000 for javascript timer...
            $this->load->view('templates/template',$data);
        }
        elseif( $errorCpt==$maxError && !file_exists($EndFile))
        {
            #### problem during qsub. job reject or job in qwait state
            ####  recover qsub job Id , delete job, delete running_job, JobId_$pid..., clean file/$username/*
            $lines = file($jobfile, FILE_IGNORE_NEW_LINES);
            $error=FALSE;
            $jobId= "";
            foreach ($lines as $key => $value)
            {
                if(preg_match("/user_id/",$value))
                {
                    $jobId=strstr($value,"job_name");
                    break;
                }
            }
            
            $message = "job launched with Id $jobId crash!  ";
            ####  remove tables in reference 'tables' and in Db
            $message .= $this->remove_ref($tableTest,$tableValues);
            #### set processed status
            $this->session->set_userdata('processed_'.$pid,'1');
            $data = array(
                 'contents' => 'error_page',
                 'title' => "$this->header_name : Job not executed",
                 'footer_title'=> $this->footer_title,
                 'message' => $message,
                 'back' => anchor("visual","Back to Run processing.")
                 );
             $this->load->view('templates/template',$data);
             exit;
        }
        #### If table and files are created ####	
      #  elseif ( $this->db->table_exists($tableTest) && file_exists($EndFile) )
        else
        {
            
            $status="";
            if(file_exists($EndFile))
            {
                #### Cluster job ended. Need to check return status
                #### check if results files have been transfered !!
                if(!file_exists($fileTest))
                {
                    $message = "Your data have been computed but results files are missing.<br />\n";
                    ####  remove tables in reference 'tables' and in Db
                    $message .= $this->remove_ref($tableTest,$tableValues);
                    $this->session->set_userdata('processed_'.$pid,'3');
                    $data = array(
                             'contents' => 'error_page',
                             'title' => "$this->header_name File transfert problems",
                             'footer_title'=> $this->footer_title,
                             'message' => $message,
                             'back' => anchor("visual","Back to Run processing")
                             );
                     $this->load->view('templates/template',$data);
                     exit;
                }
                
                $Status_Job = exec("tail -1 $jobfile");
                $ReportFile = "Report_$pid.txt";
                #$Status_Job ="Job ended with code 1";
                switch ($Status_Job)
                {
                    case "Job ended with code 10":
                        $status = "Problem occurs while launching qsub process<br />";
                        $status .= "Please look log file $ReportFile content for debugging (10) purpose<br />";
                        $delEndJob = exec("mv $EndFile ${network}EndJob_${pid}_Err1.txt");
                        $delEndJob = exec("mv $jobfile ${network}$ReportFile");
                        $next = 0;
                        $this->session->set_userdata('processed_'.$pid,'2');
                        break;
                    case "Job ended with code 9":
                        ##### missing argument provide to launch_cluster.sh 
                        $status = "Problem occurs while launching qsub process<br />";
                        $status .= "Please look log file $ReportFile content for debugging (9) purpose<br />";
                        $next = 0;
                        $this->session->set_userdata('processed_'.$pid,'2');
                        break;
                    case "Job ended with code 1":
                        $status = "Problem occurs while processing similarity step<br />";
                        $status .= "Please look log file <b>$ReportFile</b> content for debugging (1) purpose<br />";
                        $delEndJob = exec("mv $EndFile ${network}EndJob_${pid}_Err1.txt");
                        $delEndJob = exec("mv $jobfile ${network}$ReportFile");
                        $next = 0;
                        $this->session->set_userdata('processed_'.$pid,'2');
                        break;
                    case "Job ended with code 2":
                        $status = "Problem occurs while processing networking step<br />";
                        $status .= "Please look log file <b>$ReportFile</b> content for debugging (2) purpose<br />";
                        $delEndJob = exec("mv $EndFile ${network}EndJob_${pid}_Err1.txt");
                        $status .= " EndJob_$pid.txt have been moved to ${network}$ReportFile <br />";
                        $delEndJob = exec("mv $jobfile ${network}$ReportFile");
                        $next = 0;
                        $this->session->set_userdata('processed_'.$pid,'2');
                        break;
                    case "Job ended with code 4":
                        $status = "Missing parameters !! Job can not be launched<br />";
                        $status .= "Please look log file <b>$ReportFile</b> content for debugging (4) purpose<br />";
                        $delEndJob = exec("mv $EndFile ${network}EndJob_${pid}_Err1.txt");
                        $status .= " EndJob_$pid.txt have been moved to ${network}$ReportFile <br />";
                        $delEndJob = exec("mv $jobfile ${network}$ReportFile");
                        $next = 0;
                        $this->session->set_userdata('processed_'.$pid,'2');
                        break;
                        
                    case "Job ended with code 0":
                        $status = "Job end successfully";
                        $status .= "Please look log file <b>$ReportFile</b> content for debugging purpose<br />";
                        $delEndJob = exec("rm $EndFile");                       
                        $delEndJob = exec("mv $jobfile ${network}$ReportFile");
                        $next = 1;
                        $this->session->set_userdata('processed_'.$pid,'1');
                        break;    

                }
                 $processed= $this->session->userdata('processed_'.$pid);
                 
            }
            $data['status'] = $status ;
            
            if($processed == 2)
            {
                #################### remove $tableTest && $tableValues reference table
                ####  remove tables in reference 'tables' and in Db
                $status .= $this->remove_ref($tableTest,$tableValues);
                
                $web_path =strlen($web_path)+1;
                $Path = "../".substr($network,$web_path);
                $data = array(
                         'contents' => 'error_page',
                         'title' => "$this->header_name: Clustering Error",
                         'footer_title' => $this->footer_title,
                         'message' => $status,
                         'ReportFile' => $ReportFile ,
                         'Path' => $Path,
                         'back' => anchor("../visual","Back to Run processing")
                         );
                
                $this->session->set_userdata('processed_'.$pid,'3');
                $this->load->view('templates/template',$data);
               # exit;
            }
            if($processed == 3)
            {
                ##### user try to refresh error page !! 
                ##### redirect to visual 
                redirect('visual');
                exit;
            }
            if($processed == 1)
            {
                #### Extract Clusters and normalized values from DB ####
                $data['orderName']=$tableTest;
                $groupfile= $this->visualizer->get_Cluster($tableTest,$filename);
                if(isset($groupfile->error) && $groupfile->error != 0) 
                {
                     $this->session->set_userdata('processed_'.$pid,'3');
                     $data = array(
                         'contents' => 'error_page',
                         'title' => "$this->header_name: Clustering Extraction failed",
                         'footer_title'=> $this->footer_title,
                         'message' => $groupfile->error,
                         'back' => anchor("../visual","Back to Run processing")
                         );
                     $this->load->view('templates/template',$data);
                     
                    # exit;
                }
                else 
                {
                    $data['debug']= $groupfile;
                    $data['groupFile']=json_encode($groupfile);
                    $data['orderFile']=json_encode($this->visualizer->get_Values($filename,$seuil));
                    $orga=$this->visualizer->get_Organism($filename);
                    $organism=$orga->Organism;
                    $data['organism']=$organism;

                    $genesRes=$this->visualizer->get_Genes($filename);
                    $genes=array();
                    foreach($genesRes as $res)
                    {
                            $gene=$res['Gene_Name'];
                            array_push($genes,$gene);                            
                    }	
                    $data['genes']=json_encode($genes);
                    $analyseRes=$this->visualizer->get_Analyse($organism);
                    $analyse=array();
                    if($analyseRes->nbr >0) # != "There is no Annotation Table for this Organism, please provide one.")
                    {
                        foreach($analyseRes->result as $res)
                        {
                            $ana=$res['Analyse'];
                            array_push($analyse,$ana);	
                        }
                    }
                    $data['analyse']=json_encode($analyse);
                    #### Read Network Files ####
                    $data['debug']= $analyseRes->sql;
                    $web_path =strlen($web_path)+1;
                    $Path = "../".substr($network,$web_path);
                    $dir= $Path; 
                    $data['nodesFile']=$dir."Nodes".$filename."_".$seuilName.".json";
                    $data['edgesFile']=$dir."Edges".$filename."_".$seuilName.".json";
                    $data['contents']='resPage';
                    $t1 =microtime(true);
                    $time_process = $t1 - $t0;
                    $data['time_process']= $time_process;
                    $data['processed']= $processed;
                    $data['title']= "$this->header_name: Clustering Results";
                    $data['footer_title']= $this->footer_title;
                    $this->load->view('templates/template_show',$data);
                 }
            }
         }
    } ### End Fct show

    /** 
    * function coex
    * 
    * used from result page, display gene order by correlation score from selected gene
    * read similarity matrix in assets/similarity
    * display corrPage
    *
    **/
	
    public function coex()
    {
        $username=$this->session->userdata['username'];
        $name=$this->session->userdata['fileName'];	
        $seuil=$this->session->userdata['seuil'];	
        $seuilName=$this->session->userdata['seuilName'];
        ################  clean post ########################
        $this->load->library('form_validation');
        $geneID = $this->input->post('geneID');
        $geneName = $this->input->post('geneName');
        
        if(preg_match('/ - /',$geneName))
        {            
            $geneName =strstr($geneName,' - ',1);
	}
        
        #### EXTRACT GENE SIM VALUES FROM MATRIX ####
        $similarity = $this->config->item('similarity');
        $simDir=$similarity;
        $simFile="$simDir/$name"."_Similarity";
        
        $orga=$this->visualizer->get_Organism($name);
        $organism=$orga->Organism;
        
        $analyseRes=$this->visualizer->get_Analyse($name);
        $analyse=array();
        foreach($analyseRes->result as $res)
        {
                $ana=$res['Analyse'];
                array_push($analyse,$ana);	
        }
        
        #### LOAD VIEW ####
        $data = array(
            'title'=>"$this->header_name:  Correlation Results",
            'contents' => "corrPage",
            'footer_title' => $this->footer_title,
            'seuil' => $seuil,
            'filename' => $name,
            'geneID' => $geneID,
            'geneName' => $geneName,
            'simFile' => $simFile,
            'organism' => $organism,
            'analyse' => json_encode($analyse),
          );
        
        $this->load->view('templates/template_show',$data);				
    } ### End Fct coex ###
    

    /**
    * function download
    *
    * retrieve paramaters from download section of results page
    * extract clustering results and annotation from database
    * export in CSV file
    * @file: results file 
    **/
    public function download()
    {
        $table=$this->session->userdata['fileName'];
        $seuilName=$this->session->userdata['seuilName'];
        $dir=$this->session->userdata['working_path'];
        $orga=$this->visualizer->get_Organism($table);
        $organism=$orga->Organism;
        ################  clean post ########################
        $this->load->library('form_validation');
        $fileName = $this->input->post('file');
        
        if( $this->input->post('annot')  )
        {
                $annotation=$this->input->post('annot') ;
        }
        else
        { 
                $annotation=array();
        }
        
        if( isset($_POST['toolbox']) )
        {
                $toolbox= $this->input->post('toolbox');
        }
        else
        {
                $toolbox=array();
        }
        
        $this->session->set_userdata('dwnd_sql','');
        $this->session->set_userdata('dwnd','');
        $results=$this->generic->download_Results($table,$seuilName,$organism,$annotation,$toolbox);
        $this->session->set_userdata('dwnd_sql',$results->sql);
        $file=$dir.$fileName.".csv";
        #### remove any previously created download file with same name
        if(file_exists($file))
        {
                unlink($file);
        }
        ##### header
        $line="Gene_Name\tCluster\tGroup\tSignature\tannotation\tbiological_activity\n";
        foreach($results->result as $res)
        {
            $gene=$res['Gene_Name'];
            $cluster=$res['cluster'];
            $group=$res['group'];
            #### if clustering results with annotation
            if(count($annotation)>0 && count($toolbox)==0 && isset($res['Signature']) && !isset($res['annotation']))
            {                 
                    $sign=$res['Signature'];                     
                    $annot= $res['Description'];
                    $b_a="";
                    $line.=$gene."\t".$cluster."\t".$group."\t".$sign."\t".$annot."\t".$b_a."\n";
                    $this->session->set_userdata('dwnd',"1 annot >0 toll=0 sign $sign !annot $annot ");
            }
            #### if clustering results for specified toolbox
            else if(count($annotation)==0 && count($toolbox)>0 && !isset($res['Signature']) && isset($res['annotation']))
            {
                    $annot=$res['annotation'];
                    $b_a=$res['biological_activity'];
                    $sign=$res['toolbox_name'];
                    $line.=$gene."\t".$cluster."\t".$group."\t".$sign."\t".$annot."\t".$b_a."\n";
                $this->session->set_userdata('dwnd',"2 annot =0 toll>0 !sign $sign annot $annot ");
            }	
            #### if clustering results for specified toolbox with annotation
            else if(count($annotation)>0 && count($toolbox)>0 && isset($res['annotation']) )
            {
                    $annot=$res['annotation'];
                    $b_a=$res['biological_activity'];
                    $sign=$res['toolbox_name'];
                    $signatures=$this->generic->get_Gene_Annot($gene,$table,$annotation);
                    ####/ create genes list + annot
                    foreach($signatures->result as $signat)
                    {
                            $signA=$signat['Signature'];
                            $desc = $signat['Description'];
                            $line.=$gene."\t".$cluster."\t".$group."\t".$signA."\t".$desc."\t\n";
                    }
                    #### create gene + toolbox
                    $line.=$gene."\t".$cluster."\t".$group."\t$sign\t".$annot."\t".$b_a."\n";
                    $this->session->set_userdata('dwnd',"3 annot >0 toll>0 $signatures->sql  annot $annot ");
            }
            #### if clustering results only
            else
            {
                 $this->session->set_userdata('dwnd',"ELSE annot >0 toll>0  annot $annot ");
                    $sign="";
                    $annot="";
                    $b_a="";
                    $line.=$gene."\t".$cluster."\t".$group."\t".$sign."\t".$annot."\t".$b_a."\n";
            }
        }

        #### create CSV file for download et echo url
        file_put_contents($file, $line);
        $file=base_url($file);
        print $file;
        exit;
    } ### End fct Download ####

    /**
    * function remove_ref
    *   delete table in Db and in references table 'tables'
    *  
    * @param string $tableTest 
    * @return string $message 
    */  
    public function remove_ref($tableTest,$tableValues)
    {
        log_message('debug', "remove_ref($tableTest,$tableValues)" );
        $message = "";
        ### May not have been created if cluster bug .... ###
        if ( $this->db->table_exists($tableTest))
        {
            
            #### Delete tables if created ####	
            $this->load->dbforge();
            if($this->db->table_exists($tableTest))
            {
                $this->dbforge->drop_table($tableTest);
                $message .= "* Table $tableTest have been dropped<br />";
            }
            
           
            if($this->db->table_exists($tableValues))
            {
                $this->dbforge->drop_table($tableValues);
                $message .= "* Table $tableValues have been dropped<br />";
            }
            
        }
        #### delete ref in tables ####
        log_message('debug', "remove_ref:: call remove_ref_tables $tableTest \n" );
        $this->generic->remove_ref_tables($tableTest);
        log_message('debug', "remove_ref:: call remove_ref_tables $tableValues \n" );
        $this->generic->remove_ref_tables($tableValues);
        $message .= "* Tables $tableTest and $tableValues <br /> have been removed from references tables<br />";
        log_message('debug', "remove_ref($tableTest) message:\n $message" );
        return $message;
    } ### End Fct remove_ref ###
   
    /**
    * function fatal()
    *   on cluster error dispaly info
    * @staticvar integer $staticvar 
    * @param string $param1 
    * @param string $param2 
    * @return integer 
    */  
    public function fatal()
    {
      $data = array(
          'title'=> "$this->header_name: Fatal Error",
          'contents' => 'fatal',
          'footer_title' => $this->footer_title,
          'back' => $this->back(),
          );
       $this->load->view('templates/template_visual',$data);
    }
    
   
}
