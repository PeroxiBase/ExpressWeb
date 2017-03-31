<?php
session_start();


if(!isset($_SESSION['running_job']))
{
    
    $admin_name = $_POST['admin_name'];
    $web_path = $_POST['web_path'];
    $work_cluster = $_POST['work_cluster'];
    $_SESSION['work_cluster'] = $work_cluster;
    $_SESSION['web_path'] = $web_path; 
    #print "⊤op sessions: ".print_r($_SESSION,1)."<br />\n";
    #print "⊤op POST: ".print_r($_POST,1)."<br />\n";
    $launch_cluster = "$web_path/assets/scripts/launch_cluster.sh 12345678 Myco_AnnotTest 0.9 $admin_name 1 >>$work_cluster/scripts/Job_12345678.txt &";
    $End_file = "$work_cluster/scripts/EndJob_12345678.txt" ;
    if(!file_exists($End_file))
    {
        print "<html>
            <head>
                <link rel=\"stylesheet\" href=\"bootstrap.css\"/>                
                <script type=\"text/javascript\" src=\"../assets/js/jquery.min.js\"></script>
                <title>Expression Database App : Check cluster command </title>
            </head>\n";
        print "<META http-EQUIV=\"Refresh\" CONTENT=\"10; url=\"check_cluster.php?pid=12345678\" >";
        
        print "<body>\n";
        print "<br /><br />\n";
        print "<div id=\"right\">Please wait.. Launch cluster command... \n";
        print "     <img src=\"wait28.gif\" width=\"28\" heigth=\"28\" alt=\"process\" /></div>";            
        print "<hr /><b>Launch script : $launch_cluster </b><hr /><pre>\n";
        
        $do_launch_cluster = system("$launch_cluster",$Res);
        $_SESSION['running_job'] = "1";
        
        print "Run job sessions: ".print_r($_SESSION,1)."<br />\n";
        print "JOb status : ".print_r($Res,1)."<br />\n";
    }
    else
    {
        print "<html>
            <head>
                <link rel=\"stylesheet\" href=\"bootstrap.css\"/>                
            <script type=\"text/javascript\" src=\"../assets/js/jquery.min.js\"></script>
                <title>Expression Database App : Check cluster command </title>
            </head>\n";
        print "<META http-EQUIV=\"Refresh\" CONTENT=\"2; url=\"check_cluster.php?pid=12345678\" >";
        
        print "<body>\n";
        $_SESSION['running_job'] = "1";
        print "Test already done!";
    }
    print "</body>\n";
    print "</html>\n"; 
}
else
{
  $web_path = $_SESSION['web_path'];
  $work_cluster = $_SESSION['work_cluster'];
  $End_file = "$work_cluster/scripts/EndJob_12345678.txt" ;
  if(file_exists($End_file))
  {
    ?>
        <!DOCTYPE html>
        <html>
        <head>
            <link rel="stylesheet" href="bootstrap.css"/>
            
        <script type="text/javascript" src="../assets/js/jquery.min.js"></script>
            <title>Expression Database App : Check cluster command result</title>
        </head>
        <body>
        <div class="container">
            <div class="row-fluid">
                <?php 
                $web_path = $_SESSION['web_path'];
                $work_cluster = $_SESSION['work_cluster'];
                $Result_file = "$work_cluster/scripts/Job_12345678.txt";
                $End_file = "$work_cluster/scripts/EndJob_12345678.txt" ;
                $Results = file_get_contents($Result_file);
                //// display $End_file  content ///////////////////
                print " <h4>Test Result</h4><pre>$Results</pre><br />\n";    
                                
                $Status_Job = exec("tail -1 $Result_file");
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
                
                print "status: $status<br /> \n";
                if($next == 1)
                {
                    unset($_SESSION['running_job']);
                    ## Call install_done method to rename "install" directory to "__install"
                    print "<form method='post' action='../welcome/install_done' >\n";
                    print "<input type=\"hidden\" name=\"admin_name\" value=\"$admin_name\" />\n";
                    print "<input type='submit' name='submit' value='Congratulation! test your Web site' />\n";
                    print "</form>\n";
                }
                else
                {
                    print "<form method='post' action='submit.php' >\n";
                    print "<input type='submit' name='submit' value='Check your parameters!!' />\n";
                    print "</form>\n";
                    unset($_SESSION['running_job']);
                }
               
                ?>    
            </div>
        </div>
        </body>
        </html>
    <?php
            
    }
    else
    {
        /**
        * Refresh page every 10 sec while
        */
        
        print "<html>
                <head>
                    <link rel=\"stylesheet\" href=\"bootstrap.css\"/>                
                <script type=\"text/javascript\" src=\"../assets/js/jquery.min.js\"></script>
                    <title>Expression Database App : Check cluster command </title>
                </head>\n";
        print "<META http-EQUIV=\"Refresh\" CONTENT=\"10; url=\"check_cluster.php?pid=12345678\" >";
        
        print "<body><br /><br /><div id=\"right\">Please wait.. Check cluster command... \n";
        print "<img src=\"wait28.gif\" width=\"28\" heigth=\"28\" alt=\"process\" /></div>";
        $web_path = $_SESSION['web_path'];
        $work_cluster = $_SESSION['work_cluster'];
        print "check cluster ... running job : ".$_SESSION['running_job']."<br>";
        print "Wait for $End_file <br />\n";
        $dir = exec("ls -l $work_cluster/scripts/",$r3);
        print "<hr />Content directory: $work_cluster/scripts<pre>".print_r($r3,1)."</pre>";
        
        print "<pre>";
        system("ps -ef |grep launch",$r)."<br />\n";
        system("ps -ef |grep execute",$r2)."<br /></pre>";
        
        if(file_exists("$work_cluster/files/$admin_name/Myco_AnnotTest_Ended")) print "Similarity computing ended<br />\n";
        if(file_exists("$work_cluster/files/$admin_name/EndJob_Myco_AnnotTest0_8.json")) print "Network computing ended<br />\n";
    }
}
  
?>

