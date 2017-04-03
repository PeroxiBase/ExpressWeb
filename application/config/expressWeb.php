<?php
   defined('BASEPATH') OR exit('No direct script access allowed');
/**
* -------------------------------------------------------------------
* ExpressDB working directories  SETTINGS
* -------------------------------------------------------------------
* Version: 1.0
*
* Authors: 
*       Bruno SAVELLI savelli@lrsv.ups-tlse.fr
*       Sylvain Picard sylvain.picard@lrsv.ups-tlse.fr
* Created: 15.06.2016
*
* This file will contain the settings needed to perform calculation on cluster.
*
* For complete instructions please consult the 'Cluster configuration'
* page of the User Guide.
*
* -------------------------------------------------------------------
* EXPLANATION OF VARIABLES
* -------------------------------------------------------------------
*
*       ['header_name']         Name of your website. Will be displayed in navigator tab
*
*       ['web_path']            Full path of web Site directory. Applications (controller, model, view) and storage folders will be created under it.
*
*       ['admin_name']          Name of administrator in Db. Default administrator
*
*       ['apache_user']         the user who run Apache . usually 'apache'
*
*	['network']             The full path of directory used for storing computed networks
*
*	['similarity']          The full path of directory used for storing computed similarity
*
*	['launch_cluster']      The full path to the script who start clustering job.
*                               Need to allow apache to access SGE cluster. Used in ctrl/Visual.php
*
*	['work_cluster']        The full path to writing directory on cluster.
*                               Your cluster should be able to write in this directory !!
*
*	['MaxGeneNameSize']     The maximum length of gene name. 
*                               Limit size for a better display in heatmap and network display
*
*	['maxError']            While submitting job to the cluster, script test connectivity
*                               On overloaded cluster script will exit after maxError try
*       
*       ['cluster_env']         Root path of cluster manager
*
*       ['cluster_app']        Full path of binary command for cluster operation (qsub,qstat,...)
*
*       ['check_cluster']       command used to check job launched by 'apache_user' on the cluster
*                               default command for cluster using SGE instructions.
*/
$config['header_name'] = ' ';
$config['web_path'] = ' ';
$config['admin_name'] = 'administrator';
$config['apache_user'] = 'apache';
$web_path = $config['web_path'];
$config['network'] = $web_path.'/assets/network/';
$config['similarity'] = $web_path.'/assets/similarity/';

$config['cluster_env'] = '/SGE/ogs';
$config['cluster_app'] = '/SGE/ogs/bin/linux-x64';
$cluster_env = $config['cluster_env'];
$cluster_app = $config['cluster_app'];
$apache_user = $config['apache_user'];
$config['check_cluster'] = "export SGE_ROOT=$cluster_env && ${cluster_app}/qstat -u $apache_user ";

$config['launch_cluster'] = $web_path.'/assets/scripts/launch_cluster.sh';
$config['work_cluster'] = ' ';
$config['work_files'] = $config['work_cluster'].'/files/';
$config['work_scripts'] = $config['work_cluster'].'/scripts/';

$config['MaxGeneNameSize'] = '15';
$config['maxError'] = '50';
$config['qdelay'] = '30';

?>