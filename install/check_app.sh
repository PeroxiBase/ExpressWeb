#!/bin/bash
echo '************* Bash version *************'
    
/bin/bash --version |head -1
echo '************* python version *************'
/usr/bin/env python -V

echo '************* Rscript --version *************'
/usr/local/bioinfo/src/R/current/bin/Rscript --version
export SGE_ROOT=/SGE/ogs
echo '************* qsub version *************'
/SGE/ogs/bin/linux-x64/qsub -help  |head -1

echo '************* qstat *************'
/SGE/ogs/bin/linux-x64/qstat |wc -l


echo '************* end script *************'
