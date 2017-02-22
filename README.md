# ExpressWeb
The ExpressWeb is an online Tool that will allow you to easily compute clustering on your expression data and provides usefull visualisation tools as heatmaps, graphs and networks .

## Before installing Your Expression Database App
ExpressWeb was developped under [Codeigniter 3.0.3](https://codeigniter.com/docs) framework a PHP framework.

All source code for running ExpressWeb as a standalone web application is provide in this package.

## Hardware and Software requirements

### Web environment

 You need an Apache webserver (V 2.2 ) , PHP (>V 5.5 ) and Mysql or MariaDB (V 5.1 )
 
### Third party software
        
```        
Python: Python 2.7.2
        Libraries used:
            MySQLdb
            csv
            json
            resource
            math        

R: version 3.1.2
        Libraries used:    
            RMySQL
            Hmisc
            RJSONIO
            dendextend
``` 
### Cluster environment
ExpressWeb is designed to work on a SGE Cluster architecture. 

## Installation

Download or clone repository under local web location.

Follow the installation instructions  by loading on http://your_web_site/expressWeb_location/install/index.php

You may need root privileges for Apache and Mysql configuration.
