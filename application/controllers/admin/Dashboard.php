<?php
/**
* The Expression Database.
*
* Dashboard : Admin dashboard. 
*    Users and group managment. Process managment
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@version 1.0
*@package expressionWeb
*@subpackage     Controller
*/
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Dashboard extends MY_Controller 
{
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
        
        $this->output->enable_profiler(false);
        $this->load->helper(array('language'));
        $this->lang->load('auth'); 
        if (!$this->ion_auth->logged_in())
        {
                redirect(base_url()."auth/login", 'refresh');
        }
        
    }
    
    /**
    * function index 
    * 
    * @method array whoOnline() list user currently loggued on ExpressWeb  
    * @param html $ActiveProcess 
    */  
    public function index()
    {
        $whoOnline = $this->generic->who_online();
        $ActiveProcess = $this->active_process();
        $data = array(
              'title'=>"$this->header_name: Dashboard",
              'contents' => 'auth/admin/dashboard_view',
              'footer_title' => $this->footer_title,
              'whoOnline' => $whoOnline,
              'ActiveProcess' => $ActiveProcess
              );
            $this->load->view('templates/template', $data);
    }
    
    /**
    *  function active_process
    *  Check process launch for clustering calculation 
    *  Create html table with process list and button for killing  process
    * @see Dashboard::kill_process
    * @see Dashboard::index
    * @return html 
    */  
    public function active_process()
    {
       $apache_user = $this->config->item('apache_user');
       $check_cluster = $this->config->item('check_cluster');
       $process = new stdclass;
       $Data ="";
       // command line to check if launcher_cluster.sh and execute_bash.sh are running
       $returnV =exec("ps -U $apache_user -o pid:1,ppid:1,state,stime,time,command:1|grep 'launch_cluster\|execute_bash\|sleep'",$return);
       if(count($return)>2)
       {
           $Data = "<legend>Process Activity on cluster for $apache_user  </legend>\n";
           $Data .= "<table class=\"table table-bordered table-condensed\">\n";
           $PrimFGeader=false;
           $x=1;
           foreach($return as $key=>$val)
           {
               if(!preg_match("/grep/",$val))
               {
                   if($x==1)
                   {
                       
                       $Data .= "       <thead>\n";
                       $Data .= "          <tr><th>Kill</th><th>PID</th><th>Child</th><th>STAT</th><th>START</th><th>TIME</th><th>COMMAND</th></tr>\n";
                       $Data .= "       </thead>\n";
                       $Data .= "       <tbody>\n"; 
                       $PrimFGeader=true;
                   }
                  $list=explode(" ",$val);
                  $PID = $list[0];
                  $PPID = $list[1];
                  $STAT = $list[2];
                  $START = $list[3];
                  $TIME = $list[4];
                  $COMMAND ="";
                  for($i=5;$i<count($list);$i++)
                  {
                      $COMMAND .= $list[$i]." ";
                  }
                  $kill="<button class=\"Kill btn btn-primary\" type=\"button\" value=\"$PID\" />";
                  $Data .= "<tr><td>$kill</td><td>$PID</td><td>$PPID</td><td>$STAT</td><td>$START</td><td>$TIME</td><td>$COMMAND</td></tr>\n";
               }
               $x++;
           }
           $returnQs =exec($check_cluster,$returnQ);
           #job-ID  prior   name       user         state submit/start at     queue                          slots ja-task-ID
           #-----------------------------------------------------------------------------------------------------------------
           #171948 506.63050 test.r     apache_user   r     04/07/2016 09:25:48 workq@node002                      1
           if(count($returnQ)>0 && preg_match("/job-ID/",$returnQ[0]))
           {
               array_shift($returnQ);
               array_shift($returnQ);
               if($PrimFGeader==false)
               {
                   $Data .= "       <thead>\n";                                                      
                   $Data .= "          <tr><th>Kill</th><th>job-ID</th><th>prior</th><th>name</th><th>user</th><th>state</th><th>submit/start at</th><th>queue</th><th>slots</th><th>ja-task-ID</th></tr>\n";
                   $Data .= "       </thead>\n";
                   $Data .= "       <tbody>\n";
               }
               else
               {
                   $Data .= "   </tbody>\n";
                   $Data .= "</table>\n";
                   $Data .= "<table class=\"table table-bordered table-condensed\">\n";
                   $Data .= "       <thead>\n";  
                   $Data .= "          <tr><th>Kill</th><th>job-ID</th><th>prior</th><th>name</th><th>user</th><th>state</th><th>submit/start at</th><th>queue</th><th>slots</th><th>ja-task-ID</th></tr>\n";
                   $Data .= "       </thead>\n";
               }
               $task_ID="";
               foreach($returnQ as $key=>$val)
               {
                    $val = preg_replace("/\s{1,}/","\t",$val);
                    $Fields=explode("\t",$val);
                    $nbFields= count($Fields);
                    $slots="";
                    if($nbFields==10)
                        list($processId,$job_ID,$prior,$name,$user,$state,$submit,$queue,$slots,$task_ID) = explode("\t",$val);
                    else
                            list($processId,$job_ID,$prior,$name,$user,$state,$submit,$queue) = explode("\t",$val);
                    $kill="<button class=\"Qdel btn btn-primary btn-xs\" value=\"$processId\" />$job_ID</button>";
                    
                    $Data .= "<tr><td>$kill</td><td>$job_ID</td><td>$prior</td><td>$name</td><td>$user</td><td>$state</td><td>$submit</td><td>$queue</td><td>$slots</td><td>$task_ID</td></tr>\n";
               }
           }
          
           $Data .= "   </tbody>\n";
           $Data .= "</table>\n";
       }
       $process->apache=$Data;
       return $process;
    }
    
    /**
    * function kill_process 
    * delete process on cluster
    * @param integer $_POST['ProcessId']
    * @return string $html 
    */   
    public function kill_process(){
          $ProcessId=$_POST['ProcessId'];
          
          $cmd = exec("kill -9 $ProcessId");
          $html= "ProcessId  $ProcessId / command: $cmd";
          #redirect(base_url()."Dashboard/index", 'refresh');
          return $html;
    }
    
    
    /**
    * function qdel_process
    * 
    * delete cluster job
    * @param integer $_POST['ProcessId'] Id of cluster job to delete
    * @return string $html 
    */  
    public function qdel_process(){
          $ProcessId=$_POST['ProcessId'];          
          $cmd = exec("qdel $ProcessId");
          $html= "ProcessId  $ProcessId / command: $cmd";
          return $html;
    }
}
