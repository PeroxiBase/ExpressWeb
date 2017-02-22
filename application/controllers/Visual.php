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

class Visual extends MY_Controller {

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
		// Your own constructor code
		$this->output->enable_profiler(false);
		$this->load->library('form_validation');
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
	    $GetWS=$this->expression_lib->working_space('Upload'); // Load user folder
	    $this->load->model('generic');
            if(isset($GetWS->Path))
            {
                $Path= $GetWS->Path;
                $pid = $GetWS->pid;
		$userID=$this->session->user_id;
                $this->session->set_userdata('Path',$Path);
                $this->session->set_userdata('pid',$pid);
            }   
       
            // User variables // 
           $username = $this->session->userdata('username');
           $pid=trim(strstr(microtime(),' '));	
           $data['pid']=$pid;

            // Get Database Tables for User //
           $userTables=$this->generic->get_table_members($userID); // see Generic.php model
           $tables=array();
           foreach($userTables->result as $row)
           {
                    if($userTables->nbr > 20)
                    {
                            array_push($tables,($row['TableName'])); // Table Names for Select in view main.php
                    }
           }
           $data['tables']=$userTables;
           $data['contents']= 'main';
           $data['title']= "$this->header_name: Visual";	       
           $data['footer_title']= "$this->footer_title";
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
		// session control //
		if (isset($_SESSION['username']))
		{
			$username=$this->session->userdata['username'];
			$pid= $this->expression_lib->getPid();
			#
			$this->session->set_userdata('pid',$pid);
			$Path =$this->session->userdata('Path');
			$data['dir']=$Path;
			$data['username']=$username;
			$data['pid']=$pid;			
			$data['footer_title']= "$this->footer_title";
			$file = array();
			// Add session variables , file and thrshold(seuil) used //	
			$filename=$_POST['file'];
			$seuil=/*1-*/$_POST['clusterSeuil'];
			$seuilName=str_replace(".","_",$seuil);
			$this->session->set_userdata('seuilName',$seuilName);
			$this->session->set_userdata('fileName',$filename);
			$this->session->set_userdata('seuil',$seuil);
			$this->session->set_userdata('errorCpt',0);
			// Display results according to the chossen option ( global clustering or gene Specific correlation ) //
				
			// Gene Specific //
			if(isset($_POST['geneSelect']) && $_POST['geneSelect']=='geneChecked')
			{
				$gene=$_POST['gene'];
				$data['seuil']=$_POST['corTh'];
				$data['gene']=$gene;	
				$res=$this->visualizer->get_CoexMatrix($filename,$seuil,$gene); // Extract the values and correlation for each gene //	
				echo "<pre id=coexMatrix style='display:none'>";
				print_r(json_encode($res)); // Load to view //
				echo "</pre>";
				$this->load->view('genespe',$data);

			}

			// Global Clustering //
			else
			{
				// Get filename threshold and option(simple or double clustering) Double is actually disabled //
				$data['seuil']=$_POST['clusterSeuil'];
				$data['name']=$this->session->userdata['fileName'];
				
				$optionclus=0;// Simple clustering by default //
				if(isset($_POST['doubleclus']) && $_POST['doubleclus']== 'doubleC')
				{
					$optionclus=1;
				}
				$data['option']=$optionclus;		
				
				// Countdown Construction //
				if( isset($POST['hf']))
				{
					$data['h']=$_POST['hf'];
					$data['m']=$_POST['mf'];
					$data['s']=$_POST['sf'];
				}
				else
				{
					$data['h']='00';
					$data['m']='00';
					$data['s']='0';
				}
				// R and Python scipts, launched on the a Computer Cluster to construct clusters and network//
				// Clustering Results are uploaded into our databse, network results are files in the $Path directory //
				$launch=$this->config->item('launch_cluster');
				$command=$launch." ".$pid." ".$filename." ".$seuil." ".$username." ".$optionclus;
				
				// Parse names for database table selection //
				$seuilName=str_replace(".","_",$seuil);
				$tableTest=$filename."_".$seuilName."_Cluster"; // Clustering Result Table //
				
				
				
				// If results table doesn't exists , launch script else don't launch //
				if(!$this->db->table_exists($tableTest))
				{
				    // add new tables names in Tables/
                                    $orga=$this->visualizer->get_Organism($filename);
                                    $organism=$orga[0]['Organism'];
                                    $data['organism']=$organism;
                                    $this->generic->update_Tables_On_Clustering($filename,$seuilName,$organism);	
                                    $web_path = $this->config->item('web_path');
                                    $work_cluster = $this->config->item('work_cluster');
        
                                    $Copy_files= array('clean.sh','config.R','DBClustering.R','DBCreateNetwork.py','execute_bash.sh','ExpressWeb.conf');
                                                                       
                                    foreach($Copy_files as $files)
                                    {
                                        $cp =exec("cp -f ${web_path}/assets/scripts/$files ${work_cluster}/scripts/",$RF);
                                        #print "cp ${web_path}/assets/scripts/$files ${work_cluster}/scripts/<br /> \n";
                                    } 
                                    // Load waiting views with the countdown //
                                    
                                    
                                    $work_scripts = $this->config->item('work_scripts');
                                    $jobfile = "$work_scripts/Job_$pid.txt"; // File containg job status on the cluster // 
                                    $EndFile = "$work_scripts/EndJob_$pid.txt";
                                    $EndSimilarityFile = "$work_cluster/files/$username/${filename}_Ended";
                                    $EndNetworkFile = "$work_cluster/files/$username/EndJob_".$filename.$seuilName.".json";
                                    $loop_time=1000;
                                    
                                    $data['EndSimilarityFile']= $EndSimilarityFile;
                                    $data['EndNetworkFile']= $EndNetworkFile;
                                    $data['EndFile']= $EndFile;
                                    $data['jobfile']= $jobfile;
                                    $data['work_cluster']= $work_cluster;
                                                                        
                                    $data['title'] = "Clustering computing";
                                    $data['content'] ='waittemp';
                                    $data['loop_time']= $loop_time;
                                    if(file_exists($EndFile))  unlink($EndFile);
                                    $this->load->view('waittemp',$data);
                                    
                                    exec("$command >>$jobfile &");
				}
				else
				{
				    
				    $loop_time = 10;
                                    $data['title'] = "$this->header_name: Preparing data";
                                    $data['content'] ='waittemp';
                                    $data['loop_time']= $loop_time;
                                    $work_scripts = $this->config->item('work_scripts');
                                    $EndFile = "$work_scripts/EndJob_$pid.txt";
                                    $this->session->set_userdata('processed_'.$pid,'1');
                                    $this->load->view('waittemp',$data);
                                  //  
				}
			}
		}
		// If user not connected, back to login page //
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
		
		// predefined configuration variable config/ExpressWeb.php
		$network=$this->config->item('network');
		$maxError=$this->config->item('maxError');		
		$web_path = $this->config->item('web_path');
		$work_cluster = $this->config->item('work_cluster');
		$work_scripts = $this->config->item('work_scripts');
		
		$option=$_POST['option'];
		$pid=$_POST['pid'];
		// Get session data //
		$start_memory = memory_get_usage();
		$username=$this->session->userdata['username'];
		$name=$this->session->userdata['fileName'];	
		$seuil=$this->session->userdata['seuil'];	
		$seuilName=$this->session->userdata['seuilName'];		
		$dir=$this->session->userdata['working_path'];
		$filename=$this->session->fileName;
		$errorCpt=$this->session->userdata('errorCpt');
		
                $processed= $this->session->userdata('processed_'.$pid);
		// Get POST data
			
		
		$fileTest=$network."/Nodes".$filename."_".$seuilName.".json";
		$jobfile = "$work_scripts/Job_$pid.txt"; // File containg job status on the cluster // 
		$EndFile = "$work_scripts/EndJob_$pid.txt";
		$EndSimilarityFile = "$work_cluster/files/$username/${filename}_Ended";
		$EndNetworkFile = "$work_cluster/files/$username/EndJob_".$filename.$seuilName.".json";
		$tableTest = $name."_".$seuilName."_Cluster";
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
		
                if($processed !='1' && $errorCpt<$maxError && !file_exists($EndFile))
                {
                   // Get countdown values //
                    $data['pid']=$pid;
                    $data['h']=$_POST['hf'];
                    $data['m']=$_POST['mf'];
                    $data['s']=$_POST['sf'];
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
                            $data['s']=$data['s']+6;
                    }
                    sleep(10); // wait before loading the view //
                    $errorCpt++;
                    $this->session->set_userdata('errorCpt',$errorCpt);
                    $data['title'] = "$this->header_name Clustering computing $errorCpt";
                    $data['contents'] ='waittemp';
                    $data['loop_time']= $loop_time;
                    $this->load->view('templates/template',$data);
		}
		// If table and files are created //	
		//elseif ( $this->db->table_exists($tableTest) && file_exists($EndFile) )
		else
		{
		    
		    $status="";
		    if(file_exists($EndFile))
		    {
		        // check files have been transfered !!
		        if(!file_exists($fileTest))
		        {
		            $message = "Your data have been computed but results files are missing.<br />\n";
		            if ( $this->db->table_exists($tableTest))
		            {
		                // Delete tables if created //	
                                $this->load->dbforge();
                                if($this->db->table_exists($tableTest))
                                {
                                        $this->dbforge->drop_table($tableTest);
                                }
                                $tableValues=$name."_".$seuilName."_Order";
                                if($this->db->table_exists($tableValues))
                                {
                                        $this->dbforge->drop_table($tableValues);
                                }
                                // delete ref in tables //
                                $this->generic->remove_ref_tables($tableTest);
                                $this->generic->remove_ref_tables($tableValues);
		            }
		            $data = array(
                                     'contents' => 'error_page',
                                     'title' => "$this->header_name File transfert problems",
                                     'footer_title'=> $this->footer_title,
                                     'message' => $message,
                                     'back' => anchor("visual","Back to Run processing")
                                     );
                                 $this->load->view('templates/template',$data);
		        }
		        
                        $Status_Job = exec("tail -1 $jobfile");
                        
                        #$Status_Job ="Job ended with code 1";
                        switch ($Status_Job)
                        {
                            case "Job ended with code 0":
                                $status = "Job end successfully";
                                $delEndJob = exec("rm $EndFile");
                                $ReportFile = "Report_$pid.txt";
                                $delEndJob = exec("mv $jobfile ${network}$ReportFile");
                                $next = 1;
                                $this->session->set_userdata('processed_'.$pid,'1');
                                break;
                            case "Job ended with code 1":
                                $ReportFile = "Report_$pid.txt";
                                $status = "Problem occurs while processing similarity step<br />";
                                $status .= "Please look log file $ReportFile content for debugging purpose<br />";
                                $delEndJob = exec("mv $EndFile ${network}EndJob_${pid}_Err1.txt");
                                $delEndJob = exec("cp $jobfile ${network}$ReportFile");
                                $next = 0;
                                $this->session->set_userdata('processed_'.$pid,'0');
                                break;
                            case "Job ended with code 2":
                                $ReportFile = "Report_$pid.txt";
                                $status = "Problem occurs while processing networking step<br />";
                                $status .= "Please look log file $ReportFile content for debugging purpose<br />";
                                $delEndJob = exec("mv $EndFile ${network}EndJob_${pid}_Err1.txt");
                                $status .= " EndJob_$pid.txt have been moved to ${network}$ReportFile <br />";
                                $delEndJob = exec("mv $jobfile ${network}$ReportFile");
                                $next = 0;
                                $this->session->set_userdata('processed_'.$pid,'0');
                                break;
        
                        }
                         $processed= $this->session->userdata('processed_'.$pid);
                         
                    }
                    $data['status'] = $status ;
                    
                    if($processed == 0)
                    {   
                        $web_path =strlen($web_path)+1;
                        $Path = "../".substr($network,$web_path);
                        $data = array(
                                 'contents' => 'error_page',
                                 'title' => "$this->header_name: Clustering Error",
                                 'message' => $status,
                                 'ReportFile' => $ReportFile ,
                                 'Path' => $Path,
                                 'back' => $this->back()
                                 );
                        $this->session->set_userdata('processed_'.$pid,'1');
                        $this->load->view('templates/template',$data);
                    }
                    else
                    {
                        
                       /*  */
                        // Extract Clusters and normalized values from DB //
                        $data['orderName']=$tableTest;
                        $groupfile= $this->visualizer->get_Cluster($tableTest,$filename);
                        if(isset($groupfile->error) && $groupfile->error != 0) 
                        {
                                 $data = array(
                                     'contents' => 'error_page',
                                     'title' => "$this->header_name: Clustering Results",
                                     'footer_title'=> $this->footer_title,
                                     'message' => $groupfile->error,
                                     'back' => anchor("visual","Back to Run processing")
                                     );
                                 $this->load->view('templates/template',$data);
                                 
                        }
                        else 
                        {
                            $data['debug']= $groupfile;
                            $data['groupFile']=json_encode($groupfile);
                        //  $data['groupFile']=json_encode($this->visualizer->get_Cluster($tableTest,$filename));
                            $data['orderFile']=json_encode($this->visualizer->get_Values($filename,$seuil));
                            $orga=$this->visualizer->get_Organism($filename);
                            $organism=$orga[0]['Organism'];
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
                            if($analyseRes != "There is no Annotation Table for this Organism, please provide one.")
                            {
                                    foreach($analyseRes as $res)
                                    {
                                            $ana=$res['Analyse'];
                                            array_push($analyse,$ana);	
                                    }
                            }
                            $data['analyse']=json_encode($analyse);
                            // Read Network Files //
                            $web_path =strlen($web_path)+1;
                            $Path = "../../".substr($network,$web_path);
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
                            $this->load->view('templates/template',$data);
                         }
                    }
		}
	}

	/** 
	* function coex
	* 
	* used from result page, display gene order by correlation score from selected gene
	* read similarity matrix in assets/similarity
	* display corrPage
	*
	**/
	
	public function coex(){
			
		$username=$this->session->userdata['username'];
		$name=$this->session->userdata['fileName'];	
		$seuil=$this->session->userdata['seuil'];	
		$seuilName=$this->session->userdata['seuilName'];
		$data['seuil']=$seuil;
		$data['filename']=$name;
		$data['geneID']=$_POST['geneID'];
		$data['geneName']=$_POST['geneName'];

		// EXTRACT GENE SIM VALUES FROM MATRIX //
		$similarity = $this->config->item('similarity');
		$simDir=base_url($similarity);
		$simFile="$simDir/$name"."_Similarity";
		$data['simFile']=$simFile;	
		
		$orga=$this->visualizer->get_Organism($name);
		$organism=$orga[0]['Organism'];
		$data['organism']=$organism;
		
		$analyseRes=$this->visualizer->get_Analyse($organism);
		$analyse=array();
		foreach($analyseRes as $res)
		{
			$ana=$res['Analyse'];
			array_push($analyse,$ana);	
		}
		$data['analyse']=json_encode($analyse);
		
		// LOAD VIEW //
		$data['contents']='corrPage';
		$data['title']= "$this->header_name: Correlation Results";
		$data['footer_title']= $this->footer_title;
		$this->load->view('templates/template',$data);				
	}
	

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
		$organism=$orga[0]['Organism'];
		$fileName=$_POST['file'];
		if( isset($_POST['annot']) )
		{
			$annotation=$_POST['annot'];
		}
		else
		{ 
			$annotation=array();
		}
		if( isset($_POST['toolbox']) )
		{
			$toolbox=$_POST['toolbox'];
		}
		else
		{
			$toolbox=array();
		}
		$results=$this->generic->download_Results($table,$seuilName,$organism,$annotation,$toolbox);
		$file=$dir.$fileName.".csv";
		if(file_exists($file))
		{
			unlink($file);
		}
		$line="Gene_Name\tCluster\tGroup\tSignature\tannotation\tbiological_activity\n";
		foreach($results as $res)
		{
			$gene=$res['Gene_Name'];
			$cluster=$res['cluster'];
			$group=$res['group'];

			// if clustering results with annotation
			if(count($annotation)>0 && count($toolbox)==0 && isset($res['Signature']) && !isset($res['annotation']))
			{ 
				$sign=$res['Signature'];
				$annot="";
				$b_a="";
				$line.=$gene."\t".$cluster."\t".$group."\t".$sign."\t".$annot."\t".$b_a."\n";
			}
			// if clustering results for specified toolbox
			else if(count($annotation)==0 && count($toolbox)>0 && !isset($res['Signature']) && isset($res['annotation']))
			{
				$annot=$res['annotation'];
				$b_a=$res['biological_activity'];
				$sign="";
				$line.=$gene."\t".$cluster."\t".$group."\t".$sign."\t".$annot."\t".$b_a."\n";
			}	
			// if clustering results for specified toolbox with annotation
			else if(count($annotation)>0 && count($toolbox)>0 && isset($res['annotation']) )
			{
				$annot=$res['annotation'];
				$b_a=$res['biological_activity'];
				$signatures=$this->generic->get_Gene_Annot($gene,$table,$annotation);
				foreach($signatures as $signat)
				{
					$sign=$signat['Signature'];
					$line.=$gene."\t".$cluster."\t".$group."\t".$sign."\t".$annot."\t".$b_a."\n";
				}
			}
			// if clustering results only
			else
			{
				$sign="";
				$annot="";
				$b_a="";
				$line.=$gene."\t".$cluster."\t".$group."\t".$sign."\t".$annot."\t".$b_a."\n";
			}
		}

		// create CSV file for download et echo url
		file_put_contents($file, $line);
		$file=base_url($file);		
		echo $file;
	}
}
