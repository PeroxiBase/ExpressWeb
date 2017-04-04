# ExpressWeb
The ExpressWeb is an online Tool that will allow you to easily compute clustering on your expression data and provides usefull visualisation tools as heatmaps, graphs and networks .
You can import genes annotation from Phytozom or from your own sources and create Toolbox .
Using annotation tables (Phytozom, PFAM, GO ... or personal data), you can enrich the results of the clustering.

## Before installing Your Expression Database App
ExpressWeb was developped under [Codeigniter 3.0.3](https://codeigniter.com/docs) framework a PHP framework.

All source code for running ExpressWeb as a standalone web application is provide in this package.

Codeigniter structure is show in fig1. 


 .   
├── application/  
├── **assets/**  
├── composer.json  
├── contributing.md  
├── index.php  
├── __install/__  
├── license.txt  
├── output/  
├── readme.rst  
├── system/  
└── user_guide/  
**fig1**


application/  
├── cache/  
├── config/  
├── controllers/  
├── core/  
├── helpers/  
├── hooks/  
├── index.html  
├── language/  
├── libraries/  
├── logs/  
├── models/  
├── third_party/  
└── views/  
**fig2**

assets/  
├── css/  
├── img/  
├── js/  
├── __network/__  
├── __scripts/__  
├── __similarity/__  
├── __uploads/__  
└── __users/__  
**fig3**
 
install/   
├── annotTester.csv  
├── __apache_conf/__  
├── bootstrap.css  
├── check_app.sh  
├── check_cluster.php   
├── __config/__   
├── form.php  
├── index.php    
├── __scripts/__  
├── __sql/__  
└── submit.php  
**fig4**


## Hardware and Software requirements

### Cluster environment
ExpressWeb is designed to work on a SGE Cluster architecture. 

### Web environment

 You need an Apache webserver (V 2.2 ) , PHP (>V 5.5 ) and Mysql or MariaDB (V 5.1 )
 
### Third party software
        
```        
Python: Python 2.7.2                R: version 3.1.2
        Libraries used:                   Libraries used:
            MySQLdb                           RMySQL
            csv                               Hmisc
            json                              RJSONIO
            resource                          dendextend
            math
``` 

### Third party Data
```    
Data    Description             Source
PFAM    Protein families        ftp://ftp.ebi.ac.uk/pub/databases/Pfam/releases/Pfam31.0/database_files/pfamA.txt.gz
        
GO      Gene Ontology           http://geneontology.org/page/downloads

KEGG    Kyoto Encyclopedia of Genes and Genomes    http://www.kegg.jp/

Enzymes KEGG Enzymes            http://rest.kegg.jp/list/enzyme
KO      KEGG Orthology          http://rest.kegg.jp/list/ko
KOG     Eukaryotes Ortholog     ftp://ftp.ncbi.nih.gov/pub/COG/KOG/kog (old !!)

PANTHER Protein ANalysis THrough Evolutionary Relationships     ftp://ftp.pantherdb.org/hmm_classifications/

```    

## Installation

All source code (for web interface) are located under **application** directory (fig2)

**assets** directory (fig3) contains directories required by our application (network,scripts,similarity,uploads,users)

**scripts** directory contains scripts used for cluster submission and post-calculations. 
These scripts will be copied to the cluster in a directory with cluster write access.

**install** directory contain scripts for installing Expression Database App.


- In apache_conf you will find an example of httpd.conf file
- In sql you will find sql scripts for creating Expression database
- **This directory will be renamed after configuration**        

### Download/clone code
1. Create a directory located in your web space (under <code>DocumentRoot</code> ) (ex: /var/www/htm/ExpressWeb)  
2. Copy the archive in this directory and Unzip it

OR
 
1. Clone gitHub repository:  
1.1. cd to your <code>ex: cd /var/www/html/</code>  
1.2. if you want to rename default directory (ExpressWeb) add 'my_directory_name' as last argument   

    ```  
    $ git clone https://github.com/PeroxiBase/ExpressWeb.git  [my_directory_name]
         
    Initialized empty Git repository in /var/www/html/ExpressWeb/.git/
    [ Initialized empty Git repository in /var/www/html/my_directory_name/.git ]
    remote: Counting objects: 2247, done.
    remote: Compressing objects: 100% (1703/1703), done.
    remote: Total 2247 (delta 504), reused 2238 (delta 500), pack-reused 0
    Receiving objects: 100% (2247/2247), 15.31 MiB | 690 KiB/s, done.
    Resolving deltas: 100% (504/504), done.

    ```
### Apache configuration    
1. Edit your Apache conf file (/etc/httpd/conf/httpd.conf | /etc/apache2/apache2.conf & sites-enabled/)         
1.1. look at <code>install/apache_conf/httpd.conf</code> for example  
1.2. define a new virtual host (website.domain.org) or  create an alias (ExpressWeb)  
1.3. reload apache config and open page <code>http://website.domain.org/install/</code> 
or <code>http://website.domain.org/ExpressWeb/install</code>  
1.4. you should display the first page of the installer "Before installing Your Expression Database App" 

### Database settings
        
Before proceeding to configuration of your local copy of ExpressWeb , you need to create the database on your mysql server.  
See **README.md** in install/sql directory.

### Web, directories, third party software and cluster settings

<code>apache_user</code>  
     By default, apache is run under user *apache* and group *apache*.  
    You may have to run apache under an account allowed to write on the cluster...   
    **This account is used for job submission**.  
    **You need to check if <code>apache_user</code> is able to launch a job on your cluster from Apache !!** 
  
<code>Site URL</code>  
    Name of your web site(ex :website.domain.org).   
    If you defined an Alias in apache config (ex ExpressWeb) give full name:  
   > website.domain.org/ExpressWeb  

<code>Web path</code>  
    Full path of web Site directory as defined in apache conf  
           <code>&lt;Directory "/var/www/html/ExpressWeb/"></code>
    
<code>Cluster results Folder</code>  
The full path of directory used for storing computed networks and similarity files.  
   Default under Web Path in <code>/assets/network</code> and <code>/assets/similarity</code>  

<code>Admin email</code>  
User must request account to access database. Request is send to <code>Admin email</code> for validation purpose.
 
### Cluster Settings
You need to define application and environment variables to submit job on cluster.
#### Cluster architecture  

<code>Cluster Env Path</code>  
 Root path of cluster manager: <code>/SGE/ogs/</code>  
    
 <code>Cluster App Path</code>  
  Full path of binary command for cluster operation (qsub,qstat,...): <code>/SGE/ogs/bin/linux-x64</code> 
    
<code>Cluster working Folder</code>  
   The full path to writing directory on cluster. Your cluster job should be able to write in this directory !!  
    Two directories will be created under this directory:  
 
- **files**: intermediates results files will be write temporarily in this directory. 
On success , files are moved to <code>Cluster results Folder</code>
- **scripts**: scripts in <code>assets/scripts/</code> will be copied in this directory
job submission are done from this directory and all job files and control commands are write/execute from this directory
temporary files are deleted on success.
           
## Configuration process  
  With all these informations , we are able to configure Codeigniter (config,database), configuration scripts for cluster scripts  
  Path and Directories will be checked. If need we'll try to create missing directories...  
  A cluster job submission will be launched.  
  On success, install directory will be renamed and your web site launch!.  
