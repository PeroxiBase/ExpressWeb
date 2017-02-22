#!/bin/bash
source ./ExpressWeb.conf
waitForEndQsub ()
{
    user_id=$1
    jobid=$2
    Qdelay=$3
    jobFile=$4
    step=$5
    debug=$6
    if [ $debug = "1" ] 
    then 
        echo "          user_id: $user_id  job_name $jobid " 
    fi
    #wait 10sec before querying for user's job
    #sleep 10
    #Check running job
    # echo "Check running job"
    $qstat -u $user_id |grep $jobid
    $qstat -u $user_id |grep $jobid >running_jobs
    #count number of lines
    wc=`cat running_jobs |wc -l`
    #
    # loop: test every $Qdelay qsub status
    while [ $wc -gt 0 ]
    do
        $qstat -u $user_id >running_jobs
        wc=`cat running_jobs |wc -l`
         echo "         job runnings: $wc  wait for $Qdelay "
        sleep $Qdelay
    done

    #echo "End job"
    rm running_jobs
    rm -rf $jobFile
}

###############  PATH and Variables ################
user_id=$(whoami)
Qdelay=$qdelay; # delay in secondes
pid=$1
filename=$2
seuil=$3
username=$4
option=$5
launcher_pid=$6
output="$path_cluster/files/$username"
startdate=$(date +"%T")
#################  check input values !! ############################
if [[ $pid == '' ]] || [[ $filename == '' ]] || [[ $seuil == '' ]] || [[ $username == '' ]] || [[ $option == '' ]]
then
    echo "WARNING : you did not submit all the required parameters: pid $pid filename $filename seuil $seuil username $username option $option "
    echo 4
    exit
fi
if [ $debug = '1' ]
then
    echo ""
    echo "------- execute.bash $startdate ---------"    
    echo ""
    echo "pid $pid"
    echo "path_cluster $path_cluster"
    echo "qsub $qsub"
    echo "SGE_Root $SGE_Root"
    echo "qstat $qstat"
    echo "maxError $maxError"
    echo "debug $debug"
    echo "Qdelay $Qdelay"
    current_pid=$$
    echo "PID du processus launcher : $launcher_pid"
    echo "PID du processus courant : $current_pid"
    echo ""
fi
############ launch command 1 . Got pid in jobid .##########
########### jobid file is removed at end of job !!#########
export SGE_ROOT=$SGE_Root
if [ ! -d "$path_cluster/files/$username" ];then mkdir $path_cluster/files/$username; fi
if [ -f "$path_cluster/files/$username/jobid_$pid" ]; then rm -rf "$path_cluster/files/jobid_$pid"; fi

$qsub -l mem=16G -l h_vmem=32G $path_cluster/scripts/DBClustering.R $username $filename $seuil >$path_cluster/jobid_$pid
if [ $debug = '1' ]
then
    echo "//////////  Create Similarity Data $(date +"%T") /////////////////"
    echo "qsub $path_cluster/scripts/DBClustering.R $username $filename $seuil >$path_cluster/jobid_$pid"
    echo ""
fi
#### use Fct to test if quarray stop
read IN <$path_cluster/jobid_$pid
IFS=' ' eval 'array=($IN)'
#store Job_Id 3th field
jobid=${array[2]}
step='DBClustering'
echo "waitForEndQsub $user_id $jobid $Qdelay $path_cluster/jobid_$pid $step $debug"
waitForEndQsub $user_id $jobid $Qdelay $path_cluster/jobid_$pid $step $debug

###############  qsub job finished . wait for R processing end signal ##############
i=0
endFileCluster="$output/"$filename"_Ended"
echo " wait for $endFileCluster  //"
while [ ! -f  $endFileCluster ]
do
  sleep $Qdelay
  ((i++))
  echo "i: $i step: $step Qdelay $Qdelay"
  if [ $i -gt $maxError ]
  then
     $message = "Problem while computing $step step !!."
     $message .= "Check database parameter or increase maxError try($maxError) or qdelay ($Qdelay) in scripts/ExpressWeb.conf"
     $message .= "launch execute_bash script on command line for debugging purpose"
     cat $message >>EndJob_$pid.txt
     echo 1
     kill -9 $current_pid
     exit
  fi
done

waiting=$(($i*$Qdelay))
echo ""
echo "Job done $(date +"%T") ! waiting time $waiting " # or create dummy file for php
echo ""

############ launch command 2 . ##########
$qsub -l mem=16G -l h_vmem=32G $path_cluster/scripts/DBCreateNetwork.py $username $filename $seuil >$path_cluster/jobid_$pid
echo "//////////  Create network...  $(date +"%T") //////////// "
echo "$qsub -l mem=16G -l h_vmem=32G $path_cluster/scripts/DBCreateNetwork.py $username $filename $seuil >$path_cluster/jobid_$pid"
echo ""
#### use Fct to test if quarray stop
read IN <$path_cluster/jobid_$pid
IFS=' ' eval 'array=($IN)'
jobid=${array[2]}
step='DBCreateNetwork'
waitForEndQsub $user_id $jobid $Qdelay $path_cluster/jobid_$pid $step $debug
###############  qsub job finished. Wait for Python end signal ##############
seuil="${seuil/./_}"
i=0
######## files produced by DBCreateNetwork.py  ####################
NodesFiles=$output"/Nodes"$filename"_"$seuil".json"
EdgesFiles=$output"/Edges"$filename"_"$seuil".json"
endFileNetwork="$output/EndJob_"$filename"_"$seuil".json"
while [ ! -f  $endFileNetwork ]
do
  sleep $Qdelay
  ((i++))
  echo "i $i $step"
  if [ $i -gt $maxError ]
  then
     $message = "Problem while computing $step step !!."
     $message .= "Check database parameter or increase maxError try($maxError) or qdelay ($Qdelay) in scripts/ExpressWeb.conf "
     $message .= "launch execute_bash script on command line for debugging purpose"
     cat $message >>EndJob_$pid.txt
     echo 2
     kill -9 $current_pid
     exit
  fi
done
waiting=$(($i*$Qdelay))

echo ""
echo "Json file created: $endFileNetwork"
echo "Job 2 done $(date +"%T") ! waiting time $waiting "
echo ""
## copy content of  in Job_$pid.txt
echo "--------------  DBCreateNetwork.py.o$jobid  output -----------------"
OutputPython=$path_cluster"/scripts/DBCreateNetwork.py.o$jobid"
cat $OutputPython 
echo ""
echo "----------  Move files to Cluster results folder ------------"
mv $output/$filename"_Similarity" $out_similarity
echo "  Move files .. $output/$filename"_Similarity" $out_similarity"
mv $NodesFiles $out_network 
echo "  Move files .. $NodesFiles $out_network "
mv $EdgesFiles $out_network 
echo "   Move files .. $EdgesFiles $out_network "
echo ""
echo "----------  clean script directory and temp directory ----------  "
rm $output/*
sh ./clean.sh
enddate=$(date +"%T")
echo ""
echo "----------  write endjob file : EndJob_$pid.txt  ----------  "
echo "Ok $enddate" >>EndJob_$pid.txt
echo 0
kill -9 $current_pid
exit
