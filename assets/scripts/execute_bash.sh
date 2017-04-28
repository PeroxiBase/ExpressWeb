#!/bin/bash
source ./ExpressWeb.conf

###################################################
##      waitForEndQsub fct . Check running job  ###
###################################################
waitForEndQsub ()
{
    user_id=$1
    jobid=$2
    Qdelay=$3
    jobFile=$4
    step=$5
    debug=$6
    if [ $debug -eq '1' ] 
    then 
        echo "          user_id: $user_id  job_name: $jobid " 
    fi
    #Check running job
    $qstat -u $user_id |grep $jobid >running_jobs_$jobid
    #count number of lines
    wc=`cat running_jobs_$jobid |wc -l`
    #
    # loop: test every $Qdelay qsub status
    while [ $wc -gt '0' ]
    do
        $qstat -u $user_id >running_jobs_$jobid
        wc=`cat running_jobs_$jobid |wc -l`
         
        if [ $wc -eq '0' ] 
        then
            echo "         no more running jobs . break"
            break
        fi
        echo "         job runnings: $wc  wait for $Qdelay sec"
        sleep $Qdelay
    done

    #echo "End job"
    rm running_jobs_$jobid
    rm -rf $jobFile
}

#################################################################################
##      check_qsub_state fct . Check qsub state                                ##
##  exit if qsub state is Eqw or retry to running state exceed $maxError tries ##
#################################################################################
check_qsub_state ()
{
    user_id=$1
    jobid=$2
    Qdelay=5
    MaxError=$4
    current_pid=$5
    debug=$6
    CQSmaxError=0
    
    ###### recover qstat state in running_jobs. Keep last line
    $qstat -u $user_id |grep $jobid >running_jobs_$jobid
    qsub_state=`tail -1 running_jobs_$jobid`
    #7871197 0.00000 DBClusteri apache_user qw    03/03/2017 16:04:55 
    ## check qsub state !! qw or Eqw1
    IFS=' ' eval 'array=($qsub_state)'
    job_state=${array[4]}
    
    if [ "$debug" == '1' ] 
    then 
        echo "      check_qsub_state:: jobid $jobid job_state $job_state"
    fi
    
    ##  job in qw or t  state ##
    if [ "$job_state" == "qw" ] || [ "$job_state" == "t" ] 
    then
        # loop: test every $Qdelay qsub status while job_state == 'r' OR $CQSmaxError == $MaxError
        while [ "$job_state" == "qw" -o "$job_state" == "t" ]
        do
            ((CQSmaxError++))
            $qstat -u $user_id |grep $jobid  >running_jobs_$jobid
            
            qsub_state=`tail -1 running_jobs_$jobid`
            IFS=' ' eval 'array=($qsub_state)'
            job_state=${array[4]}
            
            if [ "$debug" == '1' ]
            then 
                echo "      check_qsub_state::  $CQSmaxError wait ($Qdelay sec) for job start on cluster : job_state($job_state) user_id $user_id jobid $jobid"
            fi
            #########  r ? exit fct   #######
            if [ "$job_state" == "r" ] 
            then
                startdate=$(date +"%T")
                echo "        check_qsub_state:: jobs launched on cluster at $startdate"
                break
            fi
            #########  cluster full ? job not launched after Qdelay*$MaxError secondes #######
            if [ "$CQSmaxError" -eq "$MaxError" ]
            then
                err_mesg="      check_qsub_state:: Qsub job not launched on cluster after $MaxError retry "
                cat "$err_mesg" >>EndJob_$pid.txt
                echo "Job ended with code 10" 
                rm running_jobs_$jobid
                $qdel $jobid
                kill -9 $current_pid
                exit 10
            fi
            
            #########   job in Eqw state  . error in command ####
            if [ "$job_state" == "Eqw" ]
            then
                err_mesg="      check_qsub_state:: Qsub job $jobid in error state"
                cat "$err_mesg" >>EndJob_$pid.txt
                echo "Job ended with code 10"
                rm running_jobs_$jobid
                $qdel $jobid
                kill -9 $current_pid
                exit 10
            fi
            
            ## First loop wait 5 sec  .next use user Qdelay 
            sleep $Qdelay           
            Qdelay=$3
        done
    fi
    ########## super fast cluster or sunday morning ? job already running #########
    if [ "$job_state" == "r" ]
    then
        startdate=$(date +"%T")
        echo "          check_qsub_state::jobs launched on cluster at $startdate"
        echo ""
        break
    fi
}

####################################################
###       PATH and Variables                      ##
####################################################
user_id=$(whoami)
Qdelay=$qdelay; # delay in secondes
pid=$1
filename=$2
seuil=$3
username=$4
option=$5
launcher_pid=$6
output="$path_cluster/files/$username"
export SGE_ROOT=$SGE_Root
startdate=$(date +"%T")
#################  check input values !! ############################
if [[ $pid == '' ]] || [[ $filename == '' ]] || [[ $seuil == '' ]] || [[ $username == '' ]] || [[ $option == '' ]]
then
    echo "WARNING : you did not submit all the required parameters: pid $pid filename $filename seuil $seuil username $username option $option "
    echo "Job ended with code 4" 
    exit 4
fi

if [ $debug = '1' ]
then
    echo ""
    echo "------- execute.bash $startdate ---------"    
    echo "user_id $user_id"
    echo "pid $pid"
    echo "path_cluster $path_cluster"
    echo "qsub $qsub"
    echo "SGE_Root $SGE_Root"
    echo "qstat $qstat"
    echo "maxError $maxError"
    echo "debug $debug"
    echo "Qdelay $Qdelay"
    current_pid=$$
    echo "PID_launcher : $launcher_pid"
    echo "PID_current : $current_pid"
    echo "-----------------------------------------"
fi

##########  create working directories ############
if [ ! -d "$path_cluster/files/$username" ];then mkdir $path_cluster/files/$username; fi
if [ -f "$path_cluster/files/$username/jobid_$pid" ]; then rm -rf "$path_cluster/files/jobid_$pid"; fi

####################################################
###             START CLUSTERING                ####
###      launch command 1 . Got pid in jobid .  ####
###      jobid file is removed at end of job !! ####
####################################################
step='DBClustering'
date_start_qsub1=$(date +"%s")
##########       command qsub                   ##########
$qsub -l mem=16G -l h_vmem=32G $path_cluster/scripts/DBClustering.R $username $filename $seuil >$path_cluster/jobid_$pid  2>$path_cluster/scripts/err_qsub

echo "//////////////////////////////////////////////"
echo "//   Create Similarity Data $(date +"%T")        //"
echo "//////////////////////////////////////////////"
echo "$qsub $path_cluster/scripts/DBClustering.R $username $filename $seuil >$path_cluster/jobid_$pid 2>$path_cluster/scripts/err_qsub"
echo ""

##########        use Fct to get qsub job_id      ##########
read IN <$path_cluster/jobid_$pid
IFS=' ' eval 'array=($IN)'
#store Job_Id 3th field
jobid=${array[2]}

##########        qsub not launched !! .          ##########
if [ "$jobid" == "" ]
then
    err_mesg="Unable to launch programme $step. Check path and log files"
    cat "$err_mesg" >>EndJob_$pid.txt
    cat "$path_cluster/scripts/err_qsub" >>EndJob_$pid.txt
    echo "Job ended with code 10"
    kill -9 $current_pid
    exit 10
fi

##########      check qsub state in running_jobs      ##########
echo "  //check_qsub_state [$step] :  $user_id $jobid $Qdelay $maxError $current_pid $debug  //"
echo ""

check_qsub_state $user_id $jobid $Qdelay $maxError $current_pid $debug

##########      job launched                    ##########
echo ""
echo "  //waitForEndQsub $user_id $jobid $Qdelay $path_cluster/jobid_$pid $step $debug //"

waitForEndQsub $user_id $jobid $Qdelay $path_cluster/jobid_$pid $step $debug

##########      qsub job ended. wait for R processing end signal ##########
i=0
endFileCluster="$output/"$filename"_Ended"
date_end_qsub1=$(date +"%s")

echo ""
echo "  //Step DBClustering ended. //"

while [ ! -f  $endFileCluster ]
do  
  ((i++))
  if [ $debug = '1' ]
  then
      echo "        wait for $endFileCluster i: $i step: $step Qdelay $Qdelay"
  fi
  
  if [ "$i" -gt "$maxError" ]
  then
     $message = "Problem while computing $step step . File $endFileCluster not created.!!."
     $message .= "Check database parameter, disk quota, qsub memory limit or increase maxError try($maxError) or qdelay ($Qdelay) in scripts/ExpressWeb.conf"
     $message .= "launch execute_bash script on command line for debugging purpose"
     cat $message >>EndJob_$pid.txt
     echo "Job ended with code 1"
     sh ./clean.sh $jobid
     kill -9 $current_pid
     exit 1
  fi
  sleep 5
done

process_diff=$((date_end_qsub1 - date_start_qsub1 ))
process_1=$(date -d @$process_diff +'%M:%S')
echo ""
echo "  Job DBClustering done $(date +"%T") ! processing time $process_1" # or create dummy file for php
echo ""

##########      Clean temp directory DBClustering.R.*$pid ##########
sh ./clean.sh $jobid
##############################################
###              launch command 2 .        ###
##############################################
step='DBCreateNetwork'
date_start_qsub2=$(date +"%s")
##########       command qsub                   ##########
$qsub -l mem=16G -l h_vmem=32G $path_cluster/scripts/DBCreateNetwork.py $username $filename $seuil >$path_cluster/jobid_$pid 2>$path_cluster/scripts/err_qsub

echo "//////////////////////////////////////////////"
echo "//        Create network...  $(date +"%T")       // "
echo "//////////////////////////////////////////////"
echo "$qsub -l mem=16G -l h_vmem=32G $path_cluster/scripts/DBCreateNetwork.py $username $filename $seuil >$path_cluster/jobid_$pid 2>$path_cluster/scripts/err_qsub"
echo ""
 
##########       use Fct to test if quarray stop        ##########
read IN <$path_cluster/jobid_$pid
IFS=' ' eval 'array=($IN)'
jobid=${array[2]}

##########              qsub not launched !!            ##########
if [ "$jobid" == "" ]
then
    err_mesg="Unable to launch programme $step. Check path and log files. Increase sleep delay "
    cat "$err_mesg" >>EndJob_$pid.txt
    touch $endFileCluster
    echo "Job ended with code 10"
    kill -9 $current_pid
    exit 10
fi

##########       check qsub state in running_jobs       ##########
echo "  //check_qsub_state [$step] :  $user_id $jobid $Qdelay $maxError $current_pid $debug //"
echo ""

check_qsub_state $user_id $jobid $Qdelay $maxError $current_pid $debug

##########               job launched                   ##########
echo ""
echo "  //waitForEndQsub $user_id $jobid $Qdelay $path_cluster/jobid_$pid $step $debug //"

waitForEndQsub $user_id $jobid $Qdelay $path_cluster/jobid_$pid $step $debug

##########        qsub job finished. Wait for Python end signal  ##########
seuil="${seuil/./_}"
i=0
##########        files produced by DBCreateNetwork.py  ##########
NodesFiles=$output"/Nodes"$filename"_"$seuil".json"
EdgesFiles=$output"/Edges"$filename"_"$seuil".json"
endFileNetwork="$output/EndJob_"$filename"_"$seuil".json"

date_end_qsub2=$(date +"%s")

echo ""
echo "  Step DBCreateNetwork ended. //"

while [ ! -f  $endFileNetwork ]
do
  
  ((i++))
  echo "        wait for $endFileNetwork . i $i $step"
  if [ "$i" -gt "$maxError" ]
  then
     $message = "Problem while computing $step step !!. File $endFileNetwork not created"
     $message .= "Check database parameter, disk quota, qsub memory limit or increase maxError try($maxError) or qdelay ($Qdelay) in scripts/ExpressWeb.conf "
     $message .= "launch execute_bash script on command line for debugging purpose"
     cat $message >>EndJob_$pid.txt
     echo "Job ended with code 2"
     sh ./clean.sh $jobid
     kill -9 $current_pid
     exit 2
  fi
  sleep 5
done

process_diff=$((date_end_qsub2 - date_start_qsub1 ))
process_2=$(date -d @$process_diff +'%M:%S')

echo ""
echo "  Json file created: $endFileNetwork //"
echo "  Job DBCreateNetwork done $(date +"%T") ! processing time $process_2 //" # or create dummy file for php
echo ""

#########################################################################
###             TRANSFERT RESULTS IN WEB USERS DIRECTORIES            ###
#########################################################################
##########      copy content of  in Job_$pid.txt        ##########
echo "--------------  DBCreateNetwork.py.o$jobid  output -----------------"

OutputPython=$path_cluster"/scripts/DBCreateNetwork.py.o$jobid"
cat $OutputPython 

echo "--------------------------------------------------------------------"
echo ""
echo "-------------   Move files to Cluster results folder ---------------"

##########       check remote directories exist         ##########
if [ ! -d $out_similarity ]
then
    mkdir $out_similarity
    echo "create $out_similarity "
fi

if [ ! -d $out_network ]
then
    mkdir $out_network
    echo "create $out_network "
fi
##########         move files to user directories       ##########
mv $output/$filename"_Similarity" $out_similarity

echo "  Move files .. $output/$filename"_Similarity" $out_similarity"

mv $NodesFiles $out_network 

echo "  Move files .. $NodesFiles $out_network "

mv $EdgesFiles $out_network 

echo "  Move files .. $EdgesFiles $out_network "

##########      Clean script directory and temp directory ##########
echo ""
echo "--------------  Clean script directory and temp directory ----------"

rm $output/*
sh ./clean.sh $jobid
enddate=$(date +"%T")

##########              Write endjob file and exit      ##########
echo ""
echo "--------------  Write endjob file : EndJob_$pid.txt  ---------------"

echo "Ok $enddate" >>EndJob_$pid.txt

echo "Job ended with code 0"
kill -9 $current_pid
exit 0
