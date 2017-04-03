#!/bin/bash
SCRIPT_PATH=$(dirname `which $0`)
source $SCRIPT_PATH/ExpressWeb.conf
######### $1:pid $2:filename $3:seuil $4: username $5:optionclus
### check if we got all arguments
if [ -z "$1" ] ||  [ -z "$2" ] ||  [ -z "$3" ] ||  [ -z "$4" ]||  [ -z "$5" ] 
then
    echo "Job ended with code 9"
    exit 9;
fi

if [ ! -d "$path_cluster/files/$4" ]
then 
    mkdir -m 777 $path_cluster/files/$4
    echo " mkdir -m 777 $path_cluster/files/$4"
fi
echo "Launch cluster job : sh $path_cluster/scripts/execute_bash.sh $1 $2 $3 $4 $5 "

echo "PID du processus courant : $$"
echo "launch execute.sh "
( cd $path_cluster/scripts; sh execute_bash.sh $1 $2 $3 $4 $5 $$)
