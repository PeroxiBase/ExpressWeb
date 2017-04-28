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
       $current_pid = "";
       // command line to check if launcher_cluster.sh and execute_bash.sh are running
       $returnV =exec("ps -U $apache_user -o pid:1,ppid:1,state,stime,time,command:1|grep 'launch_cluster\|execute_bash'",$return);
       if(count($return)>2)
       {
           
            //sh execute_bash.sh 1492676433 Cell_Myko3 0.35 Rhett 0 31638
            foreach($return as $key=>$val)
            {
                if(!preg_match("/grep/",$val))
                {
                    if(preg_match("/execute_bash/",$val))
                    {
                        $temp = explode(" ",$val);
                        $current_pid = $temp[7];
                    }
                }
            }
           $Data = "<legend>Process Activity on cluster</legend>\n";
           $Data .= "<table class=\"table table-bordered table-condensed\">\n";
           $PrimFGeader=false;
           $x=1;
           $kill ="";
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
                  $process_pid  = $list[7];
                  for($i=5;$i<count($list);$i++)
                  {
                      $COMMAND .= $list[$i]." ";
                  }
                  if( preg_match("/execute_bash\.sh |launch_cluster/",$COMMAND))
                  $kill="<button class=\"Kill btn btn-primary btn-xs\" type=\"button\" value=\"$PID|$process_pid\" />$PID|$process_pid</button>";
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
                    $kill="<button class=\"Qdel btn btn-primary\" value=\"$job_ID\" />$job_ID</button>";
                    
                    $Data .= "<tr><td>$kill</td><td>$job_ID</td><td>$prior</td><td>$name</td><td>$user</td><td>$state</td><td>$submit</td><td>$queue</td><td>$slots</td><td>$task_ID</td></tr>\n";
               }
           }
          
           $Data .= "   </tbody>\n";
           $Data .= "</table>\n";
       }
       $Data .= " <button class=\"qstat_shrt btn btn-primary btn-xs\" value=\"$current_pid\" />qstat shrt</button> &nbsp;";
       $Data .= " <button class=\"qstat_long btn btn-primary btn-xs\" value=\"$current_pid\" />qstat</button> &nbsp;";
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
          list($ProcessId,$pid)=explode("|",$_POST['ProcessId']);
          $work_scripts  = $this->config->item('work_scripts');
          
          $cmd = exec("kill -9 $ProcessId");
          $html= "ProcessId  $ProcessId / command: $cmd";
          $cmd = exec("echo 'Admin kill running job ' >>$work_scripts/EndJob_$pid.txt");
          $cmd = exec("echo '\nJob ended with code 20' >>$work_scripts/Job_$pid.txt");
          #redirect(base_url()."Dashboard/index", 'refresh');
          return $html;
    }
    
    /**
    * function  qstat_shrt
    *   ajax show number of jobs on cluster
    *
    * @param string $param2 
    * @return integer 
    */  
    public function qstat_shrt()
    {
          $ProcessId=$_POST['ProcessId'];
          $qstat = $this->config->item('qstat');
          $cmd = exec("$qstat |wc -l",$ret_cmd,$st);          
          $html= $ret_cmd[0]." jobs on cluster<br />";
          print  $html;
    }
    
    /**
    * function  qstat
    *   list jobs on cluster
    *
    * @param string $param2 
    * @return integer 
    */  
     public function qstat()
    {
        $ProcessId=$_POST['ProcessId'];
        $qstat = $this->config->item('qstat');
        $cmd = exec("$qstat",$ret_cmd,$st);
        
        $html= "";
        $i=1;
        foreach($ret_cmd as $key=>$val)
        {
            $val = preg_replace("/\s{1,}/","\t",$val);
            $Fields=explode("\t",$val);
            $nbFields= count($Fields);
            $slots="";
            if($nbFields==10)
                list($processId,$job_ID,$prior,$name,$user,$state,$submit,$queue,$slots,$task_ID) = explode("\t",$val);
            else
                list($processId,$job_ID,$prior,$name,$user,$state,$submit,$queue) = explode("\t",$val);
            
            $html .= "$i\t$job_ID\t$prior\t$name\t$user\t$state\t$submit\t$queue\t$slots\t$task_ID\n";
            $i++;
        }
        print  $html;
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
          $qdel = $this->config->item('qdel');
          
          $apache_user = $this->config->item('apache_user');
          $work_scripts  = $this->config->item('work_scripts');
          $grep_cmd = "grep $ProcessId ${work_scripts}*|head -1|cut -d ' ' -f 1";
          $do_grep= exec($grep_cmd,$grep);
          $cmd = exec("$qdel $ProcessId",$ret1);
          $html.= "ProcessId  $ProcessId / command: $cmd\n";
          if(preg_match("/Job/",$do_grep))
          {
                $temp= substr($do_grep,strlen($work_scripts));
                $temp = trim(strstr($temp,"_"),"_");
                $pid=trim($temp,".txt:");
                $cmd = exec("echo 'Admin kill running job ' >>$work_scripts/EndJob_$pid.txt");
                $cmd = exec("echo '\nJob ended with code 20' >>$work_scripts/Job_$pid.txt");
                $cmd = exec("bash ${work_scripts}clean.sh $ProcessId",$ret,$code);
                $html .= "\n ret bash ${work_scripts}clean.sh $ProcessId code $code \n";
                $returnV =exec("ps -U $apache_user -o pid:1,command:1|grep 'launch_cluster\|execute_bash\|sleep'",$return);
                foreach($return as $key=>$val)
                {
                   if(!preg_match("/grep/",$val))
                   {
                      $list=explode(" ",$val);
                      $PID = $list[0];
                      $COMMAND = $list[1];
                      $cmd = exec("kill -9 $PID");
                   }
                }
          }
          else
          {
              $html .= "Job have been stopped (timeout) ";
          }
          print "<pre>$html</pre>";
    }
}
