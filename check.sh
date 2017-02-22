#!/bin/bash
timestamp() {
 date +%s 
}

echo 'assets/network:'
ls -l assets/network
echo 'assets/similarity'
ls -l assets/similarity
echo '/work/perox_user/clusterII/scripts:'
ls -l /work/perox_user/clusterII/scripts
echo 'process launcher:'
timestamp
ps -ef |grep laun
echo 'process execute:'
timestamp
ps -ef |grep exec
