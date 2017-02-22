<?php
/**
* The Expression Database.
*
*  Create_table Class 
*
* This class upload, parse expression data files submitted by users
* Uploaded data are analysed to create MYSQl table and insert into it parsed data
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package        ExpressWeb
*@subpackage     Controller
*/
defined('BASEPATH') OR exit('No direct script access allowed');

class Create_table extends MY_Controller 
{
    
     public $required_tag;
     
     
     /**
     * Class constructor
     *
     * Initialize Create_table class
     *
     * @return        void
     */
    public function __construct()
    {
        //  Obligatoire
        parent::__construct();
        $this->output->enable_profiler(false);
        $this->required_tag = "<a title=\"required\" class=\"glyphicon glyphicon-asterisk\"></a>";
        $this->load->helper(array('language'));
        $this->lang->load('auth');
        if (!$this->ion_auth->logged_in())
        {
                // redirect them to the login page
                redirect(base_url()."auth/login", 'refresh');
        }
        if(!$this->ion_auth->is_admin())
        {
            redirect(base_url()."welcome/restricted", 'refresh');
        }
        
        $this->load->library("expression_lib"); 
        
    }
    
    /**
    * function  index
    * 
    *  display menu operation
    */  
    public function index()
    {
        $data = array(
           'title'=>"$this->header_name: Menu operation",
           'contents' => 'upload/index',
          );
        $this->load->view('templates/template', $data);
    }
    
    
    /**
    * function upload_csv
    *
    * Upload users datas 
    * Prepare upload form
    *
    * Create Upload directory
    *@var object $organisms SQL result of organism in Db
    *@var object $GetWS working_path variables
    * @return void 
    */  
    public function upload_csv()
    {
        $GetWS=$this->expression_lib->working_space('Upload');
        if(isset($GetWS->Path))
        {
            $Path= $GetWS->Path;
            $pid = $GetWS->pid;
        }
        # Get list of Organism
        $organisms = $this->generic->get_organisms();
        $OrgaOpt="<select name=\"organism\">";
        $OrgaOpt .= "<option value=\"\" selected>&nbsp;</option>";
        foreach ($organisms->result as $row)
        {
            $id =$row['idOrganisms'] 	 ;
            $organism =$row['Organism'];
            $OrgaOpt .= "<option value=\"$id\" >$organism</option>";
        }
        $OrgaOpt .= "</select>";
        # Get list of groups
        $master_group = $this->generic->get_groups();
        
        $data = array(
           'title'=>"$this->header_name: Convert CSV to MySQL",
           'contents' => 'upload/upload_form',
           'pid' =>$pid,
           'error' =>'',
           'master_group' => $master_group,
           'organism' => $OrgaOpt
          );
        $this->load->view("templates/template", $data);
    }
    
    
    /**
    * function load_csv
    * Validation of uploaded files .
    * 
    * @return integer 
    */  
    public function load_csv()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('master_group','Master Group', 'required');
        $this->form_validation->set_rules('organism','Organism', 'is_natural_no_zero');
        if ($this->form_validation->run() == TRUE )
        {
            ############## get transmitted POST values
            $pid = set_value('pid');
            $separator = set_value('separator');
            $separator_char =  set_value('separator_char');
            $header = set_value('header');
            $has_header = set_value('header');
            $master_group = set_value('master_group');
            $type_data = set_value('type_data');
            $post_process =  set_value('post_process');
            $limit = set_value('limit');
            $id_organism = set_value('organism');
            ######################################################
            $GetWS=$this->expression_lib->working_space($pid,'Upload');
            $debug ='';
            if(isset($GetWS->Path))
            {
                $Path= $GetWS->Path;
            }
           
            $File2Upload='';
            $error='';
            $import_file="";
            ######################  Check if file submited ###########################
            if(isset($_FILES) && ($_FILES["import_file"] ["name"]!='' ) )
            {
                ############# prep upload ##############
                $config['upload_path'] = $Path;
                $config["overwrite"] = TRUE;
               # $config['allowed_types'] = 'text|txt|tab|csv';
                $config['allowed_types'] = '*';
                $this->load->library('upload', $config);
                ############## ERROR #######################
                if ( ! $this->upload->do_upload('import_file'))
                {
                    $error = array('error' => $this->upload->display_errors(),'filename' =>$File2Upload,'path' =>$Path );
                    
                    $organisms = $this->generic->get_organisms();
                    $OrgaOpt="";
                    foreach ($organisms->result as $row)
                    {
                        $id =$row['idOrganisms'] 	 ;
                        $organism =$row['Organism'];
                        $OrgaOpt .= "<option value=\"$id\" >$organism</option>";
                    }
                    $master_group = $this->generic->get_groups();
                    
                    $data = array(
                       'title'=>"$this->header_name: Convert CSV to MySQL",
                       'contents' => 'upload/upload_form',
                       'pid' =>$pid,
                       'error' =>'',
                       'master_group' => $master_group,
                       'organism' => $OrgaOpt,
                       'error' =>$error
                    );
                    $this->load->view("templates/template", $data);
                }
                else
                {
                    $File2Upload= $_FILES["import_file"]["name"];
                    $File_Size = $_FILES["import_file"]["size"];
                   # $debug .=  "76 File2Upload: $File2Upload  Path $Path header $header<br />";
                   # $debug .=  "Path: $Path <br />";
                    $mem_use= memory_get_usage() ;
                    $debug .=  "Memory usage: $mem_use<br />";
                    $existing_tables = $this->generic->get_tables_organism($id_organism);
                    ################# detect column separator #######################
                    switch($separator)
                    {
                        case 'csv_dv':
                            $file_info = $this->check_csv($File2Upload,$pid,$Path,$header,";",$limit);
                            $separator = ";";
                            break;
                        case 'csv_v':
                            $file_info = $this->check_csv($File2Upload,$pid,$Path,$header,",",$limit);
                            $separator = ",";
                            break;
                        case 'tab':
                            $file_info = $this->check_csv($File2Upload,$pid,$Path,$header,"\t",$limit);
                            $separator = "\t";
                            break;
                        case 'other':                        
                            $file_info = $this->check_csv($File2Upload,$pid,$Path,$header,$separator_char,$limit);
                            $separator = $separator_char;
                            break;
                    }
                    if($file_info->info =="")
                    {                        
                        $message = "<div class=\"alert alert-danger\" > $file_info->debug</div>";
                        $this->session->set_flashdata('message', $message);
                        redirect("create_table/upload_csv"); 
                    }
                    
                    $data = array(
                       'title'=>"$this->header_name: Upload file $import_file",
                       'contents' => 'upload/process_upload',
                       'debug' => $debug,
                       'file_name' =>$File2Upload,
                       'Path' => $Path,
                       'file_size' =>$File_Size,
                       'info' => $file_info->info,
                       'has_header' => $has_header,
                       'header' => $file_info->header,
                       'data_columns' => $file_info->data_columns,
                       'max_value_col' => $file_info->max_value_col,
                       'separator' => $separator,
                       'debug1' => $debug,
                       'debug' =>  $file_info->debug,
                       'master_group' => $master_group,
                       'type_data' => $type_data,
                       'post_process' => $post_process,
                       'existing_tables' => $existing_tables,
                       'limit' => $limit,
                       'organism' => $id_organism,
                       'required_tag' => $this->required_tag
                      );
                    $this->load->view("templates/template", $data);
                    }
             }
        }
         else
         {
            $message =validation_errors();
            $this->session->set_flashdata('message', $message);
          redirect("create_table/upload_csv");
         }
    }
    
    
    #####################################
    /**
    * function check_csv
    *  Parse file and calculate optimal SQL type of field (string, INT, TINYINT...)
    * call by load_csv()
    * @param string $Filename 
    * @param string $pid  
    * @param string $Path 
    * @param string $header
    * @param string $separator 
    * @return object $Data  data_columns && max_value_col 
    */  
    public function check_csv($Filename,$pid,$Path,$header,$separator,$limit='')
    {
        $this->load->helper('file');
        /**
        * Initialise variables
        */
        $Data = new stdclass;
        $debug="";
        $nbr_col = $nbr_header_col = $max_col = 0;
        $type_col=array();
        $max_value_col=array();
        $header_columns = array();
        $Datas_columns = array(); 
        $header_col="";
        $Filename = "$Path$Filename";
        ############# Check $Filename  #####################
        if(file_exists($Filename))
        {
            $UploadedFile= file($Filename);
            $LenFile = get_file_info($Filename);
            $countFile =count( $UploadedFile);
            #$debug .=  " L113 check_csvfile $Filename exist  with length :$countFile  and FileSize ".$LenFile['size']."!<br />";
        }
        else
        {
            $Data->debug =  "file $Filename doesn't exist !";
            $Data->info = "";
            $Data->header = "";
            return $Data;
        }
        ######### Use Header as columns name ##################
        if($header >0)
        {
                $header_cols = explode($separator,$UploadedFile[0]);                    
                $nbr_header_col =count($header_cols);
                if($nbr_header_col==1)
                {   
                    $check_data = $UploadedFile[0];
                    $good_separator = $this->expression_lib->readCSV($check_data);
                    $Data->debug =  "Wrong separator!. You select <b>$separator</b> <br />Try <b>$good_separator->info</b>";
                    $Data->info = "";
                    return $Data;
                }
                foreach($header_cols as $key=>$cols)
                {
                    $cols= trim($cols);
                    $cols = str_replace(CHR(13),"",$cols);  #retrait retour chariot 
                    $cols = str_replace(CHR(10),"",$cols);  #retrait saut de ligne
                    $cols= preg_replace("/^[\"']/","",$cols);
                    $cols= preg_replace("/[\"']$/","",$cols);
                    $cols= preg_replace("/\s/","_",$cols);
                    
                    array_push($header_columns,$cols);
                }
               # $debug .= "nbr_header_col  $nbr_header_col<br />";
                array_shift($UploadedFile);
        }
            
        ## new file size after header remove
        $countFile =count( $UploadedFile);
      #  $debug .= "New count size : $countFile <br />";
         $i=0;
        foreach($UploadedFile as $lines)
        {
           # $debug .=  "lines $lines <br>";
           
            $line=explode($separator,$lines);
            $nbr_col = count($line);
            if($nbr_col >$nbr_header_col) 
            {
              $debug .= "Line $i nbre col $nbr_col exceed header capacity $nbr_header_col ! <br />";   
            }
            $data_columns =  array();
            $clnb=0;
            $pass=false;
            foreach($line as $key=>$cols)
            {
                $cols= trim($cols);
                $cols = str_replace(CHR(13),"",$cols);  #retrait retour chariot 
                $cols = str_replace(CHR(10),"",$cols);  #retrait saut de ligne
                $col_size=strlen($cols);
                #check type
                $type="unknown";
                $option ="";
               if(preg_match('/^[0-9]*$/',$cols)) $type="INT";
               # elseif(preg_match('/^-?[\d*]/',$cols)) $type="INTS";
              #if(is_int($cols)) $type="INT";
                elseif(preg_match('/-?[\d*][\.,][0-9]*$/',$cols)) $type="DOUBLE";
                elseif(preg_match('/[0-9]{2}-[0-9]{2}-[0-9]{4}/',$cols))  $type="DATE";
                elseif(preg_match('/[a-zA-Z\s-]/',$cols)) 
                {
                    $type="VARCHAR";
                    $cols=addslashes(trim($cols,'"'));
                }
                
                switch ($type)
                {
                 case 'INT': 
                     if($cols >0 && $cols < 255) $type= "TINYINT";
                     if($col_size >3 && $cols < 65535) $type= "SMALLINT";
                     if($col_size >5 && $cols < 16777215) $type= "MEDIUMINT";
                     if($col_size >8 && $cols <4294967295) $type= "INT";
                     $option = "UNSIGNED";
                     break;
                 case 'INTS': 
                     if($cols >-128 || $cols < 127) $type= "TINYINT";
                     if($cols >3 && ($cols >-32768 || $cols < 32767)) $type= "SMALLINT";
                     if($col_size >5 && ($cols >-8388608  || $cols < 8388607)) $type= "MEDIUMINT";
                     if($col_size >8 && ($cols >-2147483648 || $cols <  2147483647)) $type= "INT";
                     $option = "SIGNED";
                     break;
                  case 'DOUBLE': 
                      $option = "SIGNED";
                     break;
                }
                # $debug.=" i $i k $key size $col_size <br />";
                
                 if($i==0)
                 {
                     $max_value_col[$clnb]['size']= $col_size;
                     $max_value_col[$clnb]['type']= $type;
                     $max_value_col[$clnb]['option']= $option;
                 }
                 else
                 {
                     $prev_size= $max_value_col[$clnb]['size'];
                     $prev_type= $max_value_col[$clnb]['type'];
                     $prev_option= $max_value_col[$clnb]['option'];
                     if ($col_size > $prev_size) 
                     {
                       #  $debug.=" clnb $clnb  size $col_size  > prev $prev_size<br />";
                        $max_value_col[$clnb]['size']= $col_size;
                        
                       #  array_push($max_value_col,array($i=>array($key=>$col_size)));
                     }
                     
                     if($type != $prev_type)
                     {
                         $max_value_col[$clnb]['type']= $type;
                         if($prev_option == "SIGNED")
                             $max_value_col[$clnb]['option']= "SIGNED";
                         else  $max_value_col[$clnb]['option']= $option;
                         # $debug.=" i $i clnb $clnb type $type  > prev_type $prev_type<br />";
                     }                         
                 }
                 
                 array_push($data_columns,$cols);                     
                 $clnb++;
            }
           #  if($i==0) $debug .=  "max_value_col : ".print_r($max_value_col,1)."<br />";
            array_push($Datas_columns,$data_columns);
          # if($i==2) break;
          if(isset($limit) && $i> $limit) 
           {
               $debug .=  "limit $limit i $i <br>";
                break;
           }
            $i++;
        }
        $Data->debug =  $debug;
        $Data->info ="$countFile";
        $Data->header =$header_columns;
        $Working_array=array();
       /* for($i=0;$i<$clnb;$i++)
        {
            array_push($Working_array,$Datas_columns[$i]);
        }*/
        $Data->data_columns =$Datas_columns[0];
        $Data->max_value_col =$max_value_col;
        return $Data;
    }
    
    
    /**
    * function  create_table
    * from load_csv validation. Create SQL table and import data
    * @param string $_POST 
    * @return void 
    */  
    public function create_table()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('tableSql','Table name', 'trim|required');
        $this->form_validation->set_rules('version','version number', 'trim|required');
       
        $MaxGeneNameSize= $this->config->item('MaxGeneNameSize');
        
        if($this->form_validation->run() == TRUE)
        {
            $Do_Sql = set_value('Do_Sql');
            $Do_Sql = 1;
            $tableSql = set_value('tableSql');
            $version = set_value('version');
            $comment = set_value('comment');
            $null_field = set_value('null_field');
            $is_index = set_value('is_index');
            $require = set_value('require');
            $include = set_value('include');
            $include_size = count($include);
            $SqlOption = set_value('SqlOption');
            $SqlType = set_value('SqlType');
            $columns = set_value('col');
            $decimal = set_value('decimal');
            $IdOrganism = set_value('organism');
          #  $max_value_col  = set_value('max_value_col');
            $SqlSize = set_value('SqlSize');
            $file_name = set_value('file_name');
            $Path = set_value('Path');
            $separator = set_value('separator');
            
            $master_group = set_value('master_group');
            $type_data = set_value('type_data');
            $post_process =  set_value('post_process');
            $comment_geneName = set_value('comment_geneName');
            $replicate = set_value('replicate');
            $force_dump = set_value('force_dump');
            ####################  replicate   #########################
            if(is_array($replicate))
            {
                $replicate_cols  = array();
            }
            $condition = set_value('condition');
            if(is_array($condition))
            {
                $condition_cols = array();
            }
            
            if($_POST['limit']) $limit = set_value('limit');
            else $limit= -1;
            $table_name = $master_group."_".$tableSql;
            #####################  parse file ##########################
            $this->load->helper('file');
            $debug="";
            $Filename = "$Path$file_name";
            
            if(file_exists($Filename))
            {
                $UploadedFile= file($Filename);
                $LenFile = get_file_info($Filename);
                $countFile =count( $UploadedFile);
                #$debug .=  " L113 check_csvfile $Filename exist  with length :$countFile  and FileSize ".$LenFile['size']."!<br />";
            }
            
            $Datas_columns = array(); 
            $i=0;
            $nbr_header_col =count($columns);
            $debug .= "Create_table::Filename: $Filename<br />Nbr lines: $countFile<br />";
            $debug .= "Process SqL: $Do_Sql <br />Header size : $nbr_header_col<br />";
             array_shift($UploadedFile);
            ####################  Create Array to store File Data ##################################
            foreach($UploadedFile as $lines)
            {
               # if($i==0) $debug .=  "lines $lines <br>";
                $line=explode($separator,$lines);
                $nbr_col = count($line);
                if($nbr_col >$nbr_header_col) 
                {
                  $debug .= "Line $i nbre col $nbr_col  exceed header capacity $nbr_header_col! <br />";   
                }
                $data_columns =  array();
                $clnb=0;
                $pass=false;
                foreach($line as $key=>$cols)
                {
                     if(array_key_exists($key,$include))
                     {
                        $cols= trim($cols);
                        $cols = str_replace(CHR(13),"",$cols);  #retrait retour chariot 
                        $cols = str_replace(CHR(10),"",$cols);  #retrait saut de ligne    
                        $cols=addslashes(trim($cols,'"'));
                        array_push($data_columns,$cols);
                        #  $debug .= "Loop file : $key  / $cols  <br />";
                         
                     }
                }
               #  if($i==0) $debug .=  "max_value_col : ".print_r($max_value_col,1)."<br />";
                array_push($Datas_columns,$data_columns);
               #################  LIMIT nbr lines to import ############################
               if($limit >0 && $i> $limit) 
               {
                   break;
               }
                $i++;
            }            
            
            
             $debug .= " End parse:<br /> limit $limit<br />  lines: $i <br /> Size Datas_columns ".count($Datas_columns)." <br />";
            ########################  CREATE TABLE   ########################################
            #$sql_data_CT1  = "DROP TABLE IF EXISTS `$table_name` ;";
            $sql_data_CT = "CREATE TABLE `$table_name` (" ;
            $sql_data_CT .= " `Gene_ID` INT(11) unsigned NOT NULL AUTO_INCREMENT, ";            
                                ####pour debug #############            
                                $sql_data_CT_deb  = "DROP TABLE IF EXISTS `$table_name` ;<br />";
                                $sql_data_CT_deb .= "CREATE TABLE IF NOT EXISTS `$table_name` (<br />" ;
                                $sql_data_CT_deb .= " `Gene_ID` INT(11) unsigned NOT NULL AUTO_INCREMENT, <br />";
            
            ############# Define columns data and size ##################
            $ColNames="";
            foreach($columns as $key=>$col)
            {
                if(array_key_exists($key,$include))
                {
                    $require_tag= "";
                    if(isset($require[$key]))
                    {
                        $require_tag = $require[$key];
                        if($require_tag) $require_tag= "NOT NULL";
                    }
                    $SqlOption_tag = strtolower($SqlOption[$key]);
                    $SqlType_tag = strtolower($SqlType[$key]);
                    $size = $SqlSize[$key]; 
                    if($col=="Gene_Name") $Gene_Name_Size=$size;
                    if($SqlType_tag =='double') 
                    {
                        $sql_data_CT .= " `$col` $SqlType_tag NOT NULL, ";
                        $sql_data_CT_deb .= " `$col` $SqlType_tag NOT NULL, <br />";
                    }
                    else
                    {
                        $sql_data_CT .= " `$col` $SqlType_tag ($size) $require_tag, ";
                        $sql_data_CT_deb .= " `$col` $SqlType_tag ($size) $require_tag, <br />";
                    }
                    $ColNames .="`$col`, ";
                    ###################  replicate ########################
                    if(is_array($replicate))
                    {
                     /**
                     [col] => Array                             [replicate] => Array
                        (                                        (
                            [0] => Gene_Name                            
                            [1] => RPKM_GR24_7d-1                       [1] => GR24
                            [2] => RPKM_GR24_7d-2                       [2] => GR24
                            [3] => RPKM_GR24_7d-3                       [3] => GR24
                            [4] => RPKM_Ct_7d-1                         [4] => Ct_7d                                                                       
                     */
                     if (array_key_exists($key,$replicate))
                     {
                         $rep_value = $replicate[$key];
                         $replicate_cols[$rep_value][]=$col;                        
                     }
                    }
                }
            }
            #########################  replicate  #########################################
            if(is_array($replicate))
            {
                $max =max(array_map('strlen', $replicate));
                $max_length_col= strlen($replicate[$max]);
                $RefTable = $table_name."_replicate";
                $sql_data_Alias = "CREATE TABLE IF NOT EXISTS `$RefTable` (" ;
                $sql_data_Alias .= " `IdReplicate` INT(11) unsigned NOT NULL AUTO_INCREMENT, ";
                
            }
            ##################################################################
            ###############  GeneName Comment field ##############################
            if($comment_geneName == 1)  
            {
                $sql_data_CT .= " `GeneNameAlias` VARCHAR (100), ";
                $sql_data_CT_deb .= " `GeneNameAlias` VARCHAR (100), <br />";
                $ColNames .="`GeneNameAlias`, ";
            }
            $sql_data_CT .= "  PRIMARY KEY (`Gene_ID`),";
            $sql_data_CT_deb .= "  PRIMARY KEY (`Gene_ID`),<br />";
            if(is_array($is_index))
            {
                foreach($is_index as $key=>$val)
                {
                    $sql_data_CT .= " KEY `$val` (`$val`), ";
                    $sql_data_CT_deb .= " KEY `$val` (`$val`), <br />";
                }
            }
            $sql_data_CT = trim($sql_data_CT,', ');
            $sql_data_CT_deb = trim($sql_data_CT_deb,', <br />');
            $sql_data_CT .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
            $sql_data_CT_deb .= "
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;<br />";
            
            $ColNames = trim($ColNames,', ');
            
            #########################  END of TABLE definition  #########################################
            $Table_already_set = $this->session->userdata('updated_table');
            if($Table_already_set != $table_name AND $Do_Sql==1)
            {
                $debug .= " Call create_Sql_table($sql_data_CT) <hr />";
                
                $DbInfo = array('master_group' => "$master_group",'table_name' => "$table_name",'IdOrganism' => "$IdOrganism",
                               'version' => $version,'comment' => addslashes($comment),'file_name' => $file_name,
                               'force_dump' => "$force_dump");
                 $insert_in_db = $this->generic->create_Sql_table($DbInfo,"$sql_data_CT");
                $debug .= "<hr />BAck create_Sql_table:<br />".$insert_in_db->info;
            }
            elseif($Table_already_set == $table_name)
            {
                $debug .= " Table  $table_name already created . <br />Resubmit a new file or go back to update table<br />";
            }
          
            ################################################################################################
            #$insertData_deb = "INSERT INTO `$table_name` (`Id$table_name`,$ColNames) <br /> VALUES ";
            $idx=0;
            $Datas_columns_size=count($Datas_columns);
            $debug .= " Datas_columns_size $Datas_columns_size <br />"; #Datas_columns : <pre>".print_r($Datas_columns[1],1)."</pre><br />";
            ########################### PARSE $Data_columns ARRAY to create INSERT Definition   #########################
            $insert_size = 0;
            $ll=0;
            $insertData_deb ="";
            $insertData ="";
            $prev_lines =0;
            if($Table_already_set != $table_name AND $Do_Sql==1)
            {
                foreach($Datas_columns  as $line=>$values)
                {
                    if($insert_size==0)
                    {
                         $insertData ="INSERT INTO `$table_name` (`Gene_ID`,$ColNames) ";
                         $insertData .=" VALUES  ";
                    }
                    
                    $insertData .=" (NULL, ";
                    $idx=0;
                    foreach ($values as $key=>$value)
                    {
                        # $debug .= "Datas_columns :key $key line $line  value ".$value." <br />";
                        if($idx==0)
                        {
                            $GeneName = $value;
                            $trunc = false;
                            if(strlen($value) >$Gene_Name_Size)
                            {
                                 $trunc = true;
                                 $trunc_value=$Gene_Name_Size;
                            }
                            elseif(strlen($value)>$MaxGeneNameSize)
                            {
                                 $trunc = true;
                                 $trunc_value=$MaxGeneNameSize;
                            }
                            if($trunc==true)
                            {
                                if($comment_geneName==1) $comment_value=$value;
                                $value= substr($value,0,$trunc_value);
                            }
                            $idx++;
                        }
                         $insertData .=" '$value',"; 
                         $i++;
                    }
                    
                    $non_exist_column= (($include_size -$key) -1);
                    if($non_exist_column>0)
                    {
                        for($x=0;$x<$non_exist_column;$x++)
                        {
                             $insertData .=" '',"; 
                        }
                    } 
                   # $debug .= "Datas_columns :key $key line $line i $i non_exist_column $non_exist_column include_size $include_size<br>";
                    if($comment_geneName==1) $insertData .=" '$comment_value',";
                    $insertData = trim($insertData,',');
                    $insertData .= "),";
                    $insert_size = strlen($insertData);
                    
                    if($insert_size >1000000)
                    {
                        #$debug .= "create_table  insert_size: $insert_size <br />";
                        $insertData = trim($insertData,','); 
                        $insertData .= ";";
                        $lines= $ll - $prev_lines;
                        $DbInfo = array('table_name' => "$table_name",'line' => "$lines");
                        $insert_in_db = $this->generic->insert_Sql_data($insertData,$DbInfo);
                        $debug .= "Ins:".$insert_in_db->info."<br />";
                        $insert_size =0;
                        $prev_lines = $ll;
                        #$ll=0;
                        
                    }
                     if($line ==3) $insertData_deb .= preg_replace("/\),/","),<br />",$insertData);
                     $ll++;
                }
                 
                $insertData = trim($insertData,','); 
                $DbInfo = array('table_name' => "$table_name",'line' => "$lines");
                $insert_in_db = $this->generic->insert_Sql_data($insertData,$DbInfo);
                $debug .= "Ins:".$insert_in_db->info."<br />";
             
                ###########################  extract annotation for current data #######################
		$originalAnnot="Annotation_$IdOrganism";
                if($this->db->table_exists($originalAnnot)){
                   $rules = $this->expression_lib->verifyGeneNameStruct($GeneName);
                   $test= $this->generic->extract_annot($originalAnnot,$table_name,$rules);
                } 
                
                ########################################################################################
             
                $data = array(
                   'title'=>"$this->header_name: Table successfully created",
                   'contents' => 'upload/create_table',
                    'POST' =>$_POST,
                   'createtable' => $sql_data_CT_deb,
                   'insertData' => $insertData_deb,
		   'test' => $test,
                 /*  'include' => $include,
                    'is_index' => $is_index,
                   'SqlOption' => $SqlOption,
                   'SqlType' => $SqlType,
                   'SqlSize' => $SqlSize,
                   'require' => $require,
                   'columns' => $columns,*/
                   
                  // 'replicate_SQL' => $replicate_SQL,
                   'tableSql' => $table_name,
                   'debug' => $debug
                  );
               if(isset($replicate_cols)) 
               {
                   $data['replicate_cols'] = $replicate_cols;
                   $data['replicate'] = $replicate;
               }
                $this->load->view("templates/template", $data);
            }
            elseif($Table_already_set == $table_name)
            {
                $debug .= " Table  $table_name already created . <br />Resubmit a new file or go back to update table<br />";
                 $data = array(
                   'title'=>"$this->header_name: Table already created",
                   'contents' => 'upload/create_table',
                    'POST' =>$_POST,
                   'createtable' => "table exist",
                   'insertData' => "No data inserted",
                   'tableSql' => $table_name,
                   'debug' => $debug
                  );
            }
        }
        else
        {
            $data = array(
               'title'=>"$this->header_name: Error while processing",
               'contents' => 'upload/error',
               'error' =>  " Missing values ! ".print_r($this->form_validation->run(),1)."<br />"             
               );
            $this->load->view("templates/template", $data);
        }
    }
    
    /**
    * function  create_annot
    * 
    * Create annotation table for an organism
    * @param string $param2 
    * @return integer 
    */  
    public function create_annot()
    {
	$debug ="";
	$DoSQL=0;
	$GetWS=$this->expression_lib->working_space('','Upload');
        if(isset($GetWS->Path))
        {
            $Path= $GetWS->Path;
            $pid = $GetWS->pid;
        }   
	if(isset($_POST['selectID']) && isset($_FILES['upload_file'] ) )
	{
            $id=$_POST['selectID'];
            $annoTable="Annotation_$id";
            $file_name=$_FILES['upload_file']['name'];
            $annotFile=$_FILES['upload_file']['tmp_name'];
            
            $tables=$this->generic->get_Subtables($id);
            if($tables->nbr==0)
            {
                $this->generic->create_annot_table($annoTable,$id,15,$file_name);
                $tables = array('TableName' =>$annoTable);
            }
	}
	else
	{
	    $organisms = $this->generic->get_organisms();
            $data = array(
               'title'=>"$this->header_name: Create annotation ",
               'contents' => 'upload/create_annotation',
               'organisms' => json_encode($organisms->result),
               'debug' => $debug,
               'success' => 'none',
              );
            $this->load->view('templates/template', $data);
        }
    }

    /**
    * function  edit_annot_page
    * 
    * 
    * @param string $param2 
    * @return integer 
    */  
    public function edit_annot_page()
    {
        $organisms = $this->generic->get_organisms();
        $data = array(
           'title'=>"$this->header_name: Edit annotation ",
           'contents' => 'upload/edit_annotation',
           'success' => 'none',
           'organisms' => json_encode($organisms->result),
          );
        $this->load->view('templates/template', $data);
    }

    public function load_annot()
    {
	$this->load->dbforge();
	if(isset($_POST['selectID']) && isset($_FILES['upload_file'] ) )
	{
		$id=$_POST['selectID'];
		$annoTable="Annotation_$id";
		$file_name=$_FILES['upload_file']['name'];
		$annotFile=$_FILES['upload_file']['tmp_name'];
		
		$tables=$this->generic->get_Subtables($id);
		if($tables->nbr==0)
		{
		    $this->generic->create_annot_table($annoTable,$id,15,$file_name);
		    $tables = array('TableName' =>$annoTable);
		}
		// READ ANNOTATION FILE //
		$csv = array();
		$lines = file($annotFile, FILE_IGNORE_NEW_LINES);
		$error=FALSE;
		foreach ($lines as $key => $value)
		{
			$del=$this->expression_lib->readCSV($value);
			$good_del=$del->delimeter;
    			$csv[$key] = str_getcsv($value,$good_del);
		}

		if(isset($_POST['header']) && $_POST['header'] ==1) array_shift($csv);

		// ADD INTO ANNOTATION TABLE //
		foreach($csv as $line)
		{
		    #print "D: line ".print_r($line,1)." <br />";
			if(!isset($line[4])){
				$line[4]="";
			}
			//if( strlen($line[0])<=15 && strlen($line[1])<=21 && strlen($line[2])<=18 && strlen($line[3])<=255 && strlen($line[4])<=15 ){
			if(strlen($line[0])==0) continue;
			if(strlen($line[2])==0) continue;
				$data=array(
					'Ref_Gene' => $line[0],
					'Analyse' => $line[1],
					'Signature' => $line[2],
					'Description' => $line[3],
					'misc' => $line[4]
				);
				
				$query = $this->db->select('*')->where($data)->get($annoTable);
				#print "D: $query <br />";
				if(count($query->result_array()) ==0 )
				{
					$this->db->insert($annoTable, $data);

					// UPDATE SUB ANNOTATION TABLES //
					foreach($tables->result as $table){
						$annotName=$table['TableName'];//sub annot
						$tableName=explode("_",$annotName,2);
						$tableName=$tableName[1];//file name
						$gene=explode('.',$line[0],2);
						$gene=$gene[0];
						$query2 = $this->db->query("SELECT Gene_Name FROM $tableName WHERE Gene_Name='$gene'");
						if(count($query2->result_array()) !=0 ){ //if gene in file
							$data2=array(
								'Gene_Name' => $gene,
								'Analyse' => $line[1],
								'Signature' => $line[2],
							);
							$this->db->insert($annotName, $data2);
						}
					}
				}
				else{
					$match=$query->result_array();
				}
			//}
		}
		


		// LOAD RESULTS VIEW //
		if($error == FALSE){ 
			$organisms = $this->generic->get_organisms();
			$data = array(
				'title'=>"$this->header_name: Create annotation ",
				'contents' => 'upload/edit_annotation',
				'success' => 'success',
				'organisms' => json_encode($organisms->result),
			     );
			$this->load->view('templates/template', $data);
		}
	}
    }
    
    
    
    
    /**
    * function upload_phytozom
    * import annotation from Phytozom Db
    * @staticvar integer $staticvar 
    * @param string $param1 
    * @param string $param2 
    * @return integer 
    */  
    public function upload_phytozom()
    {        
        $this->session->unset_userdata('updated_table_phyto');
        $GetWS=$this->expression_lib->working_space('Upload');
        if(isset($GetWS->Path))
        {
            $Path= $GetWS->Path;
            $pid = $GetWS->pid;
        }
         # Get list of Organism
        $organisms = $this->generic->get_organisms();
        $OrgaOpt="";
        foreach ($organisms->result as $row)
        {
            $id =$row['idOrganisms'] 	 ;
            $organism =$row['Organism'];
            $OrgaOpt .= "<option value=\"$id\" >$organism</option>";
        }
        $data = array(
           'title'=>"$this->header_name: Upload Phytozome annotation",
           'contents' => 'upload/upload_phytozom',
           'pid' =>$pid,
           'organism' => $OrgaOpt
          );
        $this->load->view('templates/template', $data);
    }
    
    public function convert_phytozome_annot()
    {
        $this->load->library('form_validation');
        $this->load->helper('file');
       # $this->form_validation->set_rules('import_file','File to import', 'required');
        $this->form_validation->set_rules('organism','Organism', 'is_natural_no_zero');
        if ($this->form_validation->run() == TRUE )
        {
            ############## get transmitted POST values
            $pid = set_value('pid');
            $id_organism = set_value('organism');
            $Force_Update = set_value('Force_Update');
            ######################################################
            $GetWS=$this->expression_lib->working_space($pid,'Upload');
            $debug ='';
            if(isset($GetWS->Path))
            {
                $Path= $GetWS->Path;
            }           
            $File2Upload='';
            $error='';
            $import_file="";
            $debug = "pid $pid id_organism $id_organism Force_Update $Force_Update <br />";
            ######################  Check if file submited ###########################
            if(isset($_FILES) && ($_FILES["import_file"] ["name"]!='' ) )
            {
                ############# prep upload ##############
                $config['upload_path'] = $Path;
                $config["overwrite"] = TRUE;
               # $config['allowed_types'] = 'text|txt|tab|csv';
                $config['allowed_types'] = '*';
                $this->load->library('upload', $config);
                ############## ERROR #######################
                if ( ! $this->upload->do_upload('import_file'))
                {
                    $error = array('error' => $this->upload->display_errors(),'filename' =>$File2Upload,'path' =>$Path );
                    
                    $organisms = $this->generic->get_organisms();
                    $OrgaOpt="";
                    foreach ($organisms->result as $row)
                    {
                        $id =$row['idOrganisms'] 	 ;
                        $organism =$row['Organism'];
                        $OrgaOpt .= "<option value=\"$id\" >$organism</option>";
                    }
                    
                    $data = array(
                       'title'=>"$this->header_name: Convert Phytozome Annotation",
                       'contents' => 'upload/upload_phytozom',
                       'pid' =>$pid,
                       'error' =>'',
                       'organism' => $OrgaOpt,
                       'error' =>$error
                    );
                    $this->load->view("templates/template", $data);
                }
                else
                {
                    $File2Upload= $_FILES["import_file"]["name"];
                    $File_Size = $_FILES["import_file"]["size"];
                   # $debug .=  "76 File2Upload: $File2Upload  Path $Path header $header<br />";
                   # $debug .=  "Path: $Path <br />";
                    $mem_use= memory_get_usage() ;
                    $debug .=  "Memory usage: $mem_use<br />";
                     $table_name = "Annotation_$id_organism";
                    $Table_already_set = $this->session->userdata('updated_table_phyto');
                    if($Force_Update ==1) 
                    {
                        $Table_already_set="";
                        $this->db->query("TRUNCATE $table_name");   
                    }
                    if($Table_already_set !==FALSE && $Table_already_set!= $table_name)
                    {
                        $organisms = $this->generic->get_organisms($id_organism); 
                        $max_size = $organisms->result->Max_transcript_size;
                        
                        ####################  CREATE TABLE ####################################
                       
                       #  print "create_annot_table($table_name,$id_organism,$max_size,$File2Upload)"; exit;
                        $sql_data_CT = $this->generic->create_annot_table($table_name,$id_organism,$max_size,$File2Upload);
                        if($sql_data_CT->error ==1)
                        {                        
                            $message = "<div class=\"alert alert-danger\" > Annotation $table_name exist.<br />$sql_data_CT->info</div>";
                            $this->session->set_flashdata('message', $message);
                            redirect("create_table/upload_phytozom"); 
                        }
                        
                        ######################  INSERT DATA ###############################
                        /**
                        * Initialise variables
                        */
                        $Data = new stdclass;
                        $debug .="$sql_data_CT->info";
                        $nbr_col = $nbr_header_col = $max_col = 0;
                        $type_col=array();
                        $max_value_col=array();
                        $header_columns = array();
                        $Datas_columns = array(); 
                        $header_col="";
                        $Filename = "$Path$File2Upload";
                        ############# Check $Filename  #####################
                        if(file_exists($Filename))
                        {
                            $UploadedFile= file($Filename);
                            $LenFile = get_file_info($Filename);
                            $countFile =count( $UploadedFile);
                            #$debug .=  " L113 check_csvfile $Filename exist  with length :$countFile  and FileSize ".$LenFile['size']."!<br />";
                        }
                        else
                        {
                            $Data->debug =  "file $Filename doesn't exist !";
                            $Data->info = "";
                            $Data->header = "";
                            return $Data;
                        }
                        ############ remove header #################
                        array_shift($UploadedFile);
                        
                        ############ Store file in Array ######################
                         ################# convert file #######################
                        #Osativa:
                        ##pacId	locusName	transcriptName	peptideName	Pfam	Panther	KOG	KEGG/ec	KO	GO	Best-hit-arabi-name	arabi-symbol	arabi-defline
                        #33149960	ChrSy.fgenesh.gene.14	ChrSy.fgenesh.mRNA.14	ChrSy.fgenesh.mRNA.14	PF13947,PF07714	PTHR27005,PTHR27005:SF14	KOG1187	2.7.11.1		GO:GO:0030247,GO:GO:0006468,GO:GO:0004672	AT1G21240.1	WAK3	wall associated kinase 3
                        #Ath
                        #pacId	locusName	transcriptName	peptideName	Pfam	Panther	KOG	KEGG/ec	KO	GO	Best-hit-rice-name	rice-symbol	rice-defline
                        #19652974	AT1G01050	AT1G01050.1	AT1G01050.1	PF00719	PTHR10286,PTHR10286:SF10	KOG1626	3.6.1.1	K01507	GO:GO:0006796,GO:GO:0005737,GO:GO:0004427,GO:GO:0000287	LOC_Os05g36260.1		soluble inorganic pyrophosphatase, putative, expressed
    
                        /**
                        * Id PhytozomField     Data                            AnnotationField             
                        * 1 locusName         AT1G01050                       Ref_Gene
                        * 2 transcriptName    AT1G01050.1
                        * 3 peptideName       AT1G01050.1                     Analyse   Signature       Desc  Misc
                        * 4 Pfam              PF00719                         Pfam      PF00719
                        * 5 Panther           PTHR10286,PTHR10286:SF10        PANTHER   PTHR10286
                        * 6 KOG               KOG1626                         KOG       KOG1626
                        * 7 KEGG/ec	          3.6.1.1	                  KEGG      3.6.1.1
                        * 8 KO                K01507                          KO                K01507
                        * 9 GO                GO:GO:0006796,GO:GO:0005737     GO        GO:0006796
                        * 11 symbol    WAK3	                                                           wall associated kinase 3
                        * 12 defline   wall associated kinase 3                                                WAK3
                        */
                        $separator="\t";
                        $insert_size = 0;
                        $ll=0;
                        $insertData_deb ="";
                        $insertData ="";
                        
                        $prev_lines =0;
                        $ColNames = "`Ref_Gene`,`Analyse`,`Signature`,`Description`,`misc`";
                        foreach($UploadedFile as $lines)
                        {
                           # $debug .=  "lines $lines <br>";
                            list($pacid,$locusName,$transcript,$peptide,$Pfam,$Panther,$KOG,$KEGG,$KO,$GO,$Best,$symbol,$defline) = explode($separator,$lines);
                            $line=explode($separator,$lines);
                            $data_columns =  array();
                            $clnb=0;
                            $pass=false;
                            
                            $Ref_Gene=trim($transcript);
                            $Sql_value =" ";
                            if($insert_size==0)
                            {
                                 $insertData ="REPLACE INTO `$table_name` ($ColNames) ";
                                 $insertData .=" VALUES  ";
                            }
                            
                            ########## PFAM ###############
                            if(strlen($Pfam)>0)
                            {
                                if(strlen($Pfam)>7)
                                {
                                    $PFAMS=preg_split('/,/',$Pfam);
                                    foreach($PFAMS as $key=>$value)
                                    {
                                        $annot=$this->generic->get_PFAM_Ref($value);
                                        $annot= addslashes($annot);  
                                        $Sql_value .= "('$Ref_Gene','Pfam','$value','$annot',''),";   
                                    }
                                }
                                else
                                {
                                    $annot=$this->generic->get_PFAM_Ref($Pfam);
                                    $annot= addslashes($annot);  
                                    $Sql_value .= "('$Ref_Gene','Pfam','$Pfam','$annot',''),"; 
                                }
                            }
                            ########## PANTHER ###############
                            if(strlen($Panther)>0)
                            {
                                if(strlen($Panther)>9)
                                {
                                    $Panthers=preg_split('/,/',$Panther);
                                    foreach($Panthers as $key=>$value)
                                    {
                                        $Sql_value .= "('$Ref_Gene','PANTHER','$value','',''),";   
                                    }
                                }
                                else
                                {
                                    $Sql_value .= "('$Ref_Gene','PANTHER','$Panther','',''),"; 
                                }
                            }
                            ########## KOG ###############
                            if(strlen($KOG)>0)
                            {
                                    $annot=$this->generic->get_KOG_Ref($KOG);
                                    $annot= addslashes($annot);  
                                    $Sql_value .= "('$Ref_Gene','KOG','$KOG','$annot',''),"; 
                            }
                            ########## KEGG ###############
                            if(strlen($KEGG)>0)
                            {
                                    $annot=$this->generic->get_KEGG_Ref($KEGG);
                                    $annot= addslashes($annot);  
                                    $Sql_value .= "('$Ref_Gene','KEGG','$KEGG','$annot',''),"; 
                            }
                            ########## KO ###############
                            if(strlen($KO)>0)
                            {       
                                   
                                    $Sql_value .= "('$Ref_Gene','KO','$KO','',''),"; 
                            }
                            ########## GO ###############
                            if(strlen($GO)>0)
                            {
                                if(strlen($GO)>13)
                                {
                                    $GOS=explode(',',$GO);
                                  #  print "GOS : ".print_r($GOS,1)."<br />";
                                    foreach($GOS as $key=>$value)
                                    {
                                        $value = substr($value,3);
                                        $res=$this->generic->get_GO_Ref($value);
                                        $annot= addslashes($res->annot);
                                        $Sql_value .= "('$Ref_Gene','GO','$value','$annot','$res->type'),";   
                                    }
                                }
                                else
                                {
                                    $value = substr($GO,3);
                                    $res=$this->generic->get_GO_Ref($value);
                                    $annot= addslashes($res->annot);                                
                                    $Sql_value .= "('$Ref_Gene','GO','$value','$annot','$res->type'),";
                                }
                            }
                            
                            $ll++;
                            $insertData .= $Sql_value;
                            $insert_size = strlen($insertData);
                    
                            if($insert_size >100000)
                            {
                                #$debug .= "create_table  insert_size: $insert_size <br />";
                                $insertData = trim($insertData,','); 
                                $insertData .= ";";
                                $lines= $ll - $prev_lines;
                                $insert_in_db = $this->db->query($insertData);
                                #$debug .= "Ins:". $insertData." <br />";
                                $insert_size =0;
                                $prev_lines = $ll;
                                #$ll=0;
                                
                            }
                            
                           # if($ll==10) break;
                        }
                        
                         $insertData = trim($insertData,','); 
                        $DbInfo = array('table_name' => "$table_name",'line' => "$lines");
                        #$insert_in_db = $this->generic->insert_Sql_data($insertData,$DbInfo);
                        $insert_in_db = $this->db->query($insertData);
                        
                        $data = array(
                           'title'=>"$this->header_name: Upload file $import_file",
                           'contents' => 'upload/process_phytozom',
                           'debug' => $debug,
                           'file_name' =>$File2Upload,
                           'Path' => $Path,
                           'file_size' =>$File_Size,
                           'info' => "$ll lines in file",                      
                           'organism' => $id_organism,
                           'table_name' => $table_name
                          // 'required_tag' => $this->required_tag
                          );
                        $this->load->view("templates/template", $data);
                    }
                    else
                    {
                        $data = array(
                           'title'=>"$this->header_name: Upload file $import_file",
                           'contents' => 'upload/process_phytozom',
                           'debug' => "Data have already been inserted Table_already_set: |$Table_already_set|",
                           'file_name' =>$File2Upload,
                           'Path' => $Path,
                           'file_size' =>$File_Size,
                           'info' => "error",                      
                           'organism' => $id_organism,
                           'table_name' => $table_name
                          // 'required_tag' => $this->required_tag
                          );
                        $this->load->view("templates/template", $data);
                    }
             }
        }
         else
         {
            $message =validation_errors();
            $this->session->set_flashdata('message', $message);
            redirect("create_table/upload_phytozom");
         }
    }
    }
    
    /**
    * function  upload_tair
    * 
    * @param string $param2 
    * @return integer 
    */  
    public function upload_tair()
    {
        $GetWS=$this->expression_lib->working_space('Upload');
        if(isset($GetWS->Path))
        {
            $Path= $GetWS->Path;
            $pid = $GetWS->pid;
        }
         # Get list of Organism
        $organisms = $this->generic->get_annotation_list();
        
        $data = array(
           'title'=>"$this->header_name: Upload TAIR annotation",
           'contents' => 'upload/upload_tair',
           'pid' =>$pid,
           'organism' => $organisms->Data,
           'error' =>''
          );
        $this->load->view('templates/template', $data);
    }
    
    /**
    * Upload in existing annotation file TAIR po_anatomy_gene_orga.assoc file 
    * 
    * @param string $param2 
    * @return integer 
    */  
    public function process_tair()
    {
        $this->load->library('form_validation');
        $this->load->helper('file');
       # $this->form_validation->set_rules('import_file','File to import', 'required');
        $this->form_validation->set_rules('organism','Organism', 'is_natural_no_zero');
        $this->form_validation->set_rules('type_data','Type of Data', 'required');
        if ($this->form_validation->run() == TRUE )
        {
            ############## get transmitted POST values
            $pid = set_value('pid');
            $id_organism = set_value('organism');
            $type_data = set_value('type_data');
            ######################################################
            $GetWS=$this->expression_lib->working_space($pid,'Upload');
            $debug ='';
            if(isset($GetWS->Path))
            {
                $Path= $GetWS->Path;
            }           
            $File2Upload='';
            $error='';
            $import_file="";
            ######################  Check if file submited ###########################
            if(isset($_FILES) && ($_FILES["import_file"] ["name"]!='' ) )
            {
                ############# prep upload ##############
                $config['upload_path'] = $Path;
                $config["overwrite"] = TRUE;
               # $config['allowed_types'] = 'text|txt|tab|csv';
                $config['allowed_types'] = '*';
                $this->load->library('upload', $config);
                ############## ERROR #######################
                if ( ! $this->upload->do_upload('import_file'))
                {
                    $error = array('error' => $this->upload->display_errors(),'filename' =>$File2Upload,'path' =>$Path );
                    
                    $organisms = $this->generic->get_annotation_list();
                    
                    $data = array(
                       'title'=>"$this->header_name: Convert TAIR PO Annotation",
                       'contents' => 'upload/upload_tair',
                       'pid' =>$pid,
                       'organism' => $organisms->Data,
                       'error' => "error upload file :".print_r($error,1)."".print_r($config,1)
                    );
                    $this->load->view("templates/template", $data);
                }
                else
                {
                    $File2Upload= $_FILES["import_file"]["name"];
                    $File_Size = $_FILES["import_file"]["size"];
                   # $debug .=  "76 File2Upload: $File2Upload  Path $Path header $header<br />";
                   # $debug .=  "Path: $Path <br />";
                    $mem_use= memory_get_usage() ;
                    $debug .=  "Memory usage: $mem_use<br />";
                    $existing_tables = $this->generic->get_tables_organism($id_organism);
                   
                    ####################  DEFINE TABLE ####################################
                     $table_name = "Annotation_$id_organism";
                     
                     
                    ######################  INSERT DATA ###############################
                    /**
                    * Initialise variables
                    */
                    $Data = new stdclass;
                    $debug="";
                    $nbr_col = $nbr_header_col = $max_col = 0;
                    $type_col=array();
                    $max_value_col=array();
                    $header_columns = array();
                    $Datas_columns = array(); 
                    $header_col="";
                    $Filename = "$Path$File2Upload";
                    ############# Check $Filename  #####################
                    if(file_exists($Filename))
                    {
                        $UploadedFile= file($Filename);
                        $LenFile = get_file_info($Filename);
                        $countFile =count( $UploadedFile);
                        #$debug .=  " L113 check_csvfile $Filename exist  with length :$countFile  and FileSize ".$LenFile['size']."!<br />";
                    }
                    else
                    {
                        $Data->debug =  "file $Filename doesn't exist !";
                        $Data->info = "";
                        $Data->header = "";
                        return $Data;
                    }
                    ############ remove header #################
                    array_shift($UploadedFile);
                    
                    ############ Store file in Array ######################
                    switch ($type_data)
                    {
                    case '':
                        
                        break;
                    }
                     ################# convert file #######################
                    #!gaf-version: 2.0
                    #TAIR	locus:2194060	ATARCA		PO:0000003	TAIR:Publication:501710912	IEP		S	AT1G18080	AT1G18080|ATARCA|RACK1A_AT|RACK1A|RECEPTOR FOR ACTIVATED C KINASE 1 A|T10F20.9|WD-40 REPEAT PROTEIN ATARCA	protein	taxon:3702	20050706	TAIR		TAIR:locus:2194060
                    # Ref: http://www.arabidopsis.org/download_files/GO_and_PO_Annotations/Plant_Ontology_Annotations/README-format.txt
                    /**
                        1. DB Name:  "TAIR"
                        2. Object Id:       "locus:56789"
                        3. Object Symbol:       ACT1
                        4. Not For:     NULL
                        5. PO Id:       PO:0020132
                        6. Reference:   PMID:16510871    
                        7. Evidence:    IDA
                        8. Evidence With:     NULL
                        9. Aspect:      S for plant structure, G for plant growth and dev. stage
                        10. Object Name:        AT1G01050
                        11. Synonyms: 
                        12. Object Type:        gene or locus
                        13. Taxon:      taxon:3702
                        14. Date:       20060510
                        15. Annotation Origin:  TAIR

                    * Id TAIRField        Data                  AnnotationField             
                    * 9 Object Name       AT1G18080             Ref_Gene
                    *                                           Analyse   Signature       Desc  Misc
                    * 4 PO Id:            PO:0020132             PO   PO:0020132
                    * 2 Object Symbol:    ACT1                                                   ACT1
                    */
                    $separator="\t";
                    $insert_size = 0;
                    $ll=0;
                    $insertData_deb ="";
                    $insertData ="";
                    
                    $prev_lines =0;
                    $ColNames = "`Annot_${id_organism}_ID`,`Ref_Gene`,`Analyse`,`Signature`,`Description`,`misc`";
                    foreach($UploadedFile as $lines)
                    {
                       # $debug .=  "lines $lines <br>";
                        #list($pacid,$locusName,$transcript,$peptide,$Pfam,$Panther,$KOG,$KEGG,$KO,$GO,$Best,$symbol,$defline) = explode($separator,$lines);
                        $line=explode($separator,$lines);
                        $data_columns =  array();
                        $clnb=0;
                        $pass=false;
                        switch ($id_organism)
                        {
                            case '2':
                                //Arabidopsis convert Gene to transcript
                                if(!preg_match("/^AT\d/",$line[9])) continue;
                                $Ref_Gene = trim($line[9]).".1";
                                break;
                            default:
                                $Ref_Gene = trim($line[9]);
                                break;
                        }
                        $Signature = addslashes($line[4]);
                        $Description = addslashes(trim($line[2]));
                        $Sql_value =" ";
                        if($insert_size==0)
                        {
                             $insertData ="REPLACE INTO `$table_name` ($ColNames) ";
                             $insertData .=" VALUES  ";
                        }
                        
                        ########## PFAM ###############
                            $Sql_value .= "('$Ref_Gene','PO','$Signature','$Description',''),";   
                        
                        
                        $ll++;
                        $insertData .= $Sql_value;
                        $insert_size = strlen($insertData);
                
                        if($insert_size >100000)
                        {
                            #$debug .= "create_table  insert_size: $insert_size <br />";
                            $insertData = trim($insertData,','); 
                            $insertData .= ";";
                            $lines= $ll - $prev_lines;
                            $insert_in_db = $this->db->query($insertData);
                            #$debug .= "Ins:". $insertData." <br />";
                            $insert_size =0;
                            $prev_lines = $ll;
                            #$ll=0;
                            
                        }
                        
                        # if($ll==50) break;
                    }
                    
                     $insertData = trim($insertData,','); 
                    $DbInfo = array('table_name' => "$table_name",'line' => "$lines");
                    $insert_in_db = $this->db->query($insertData);
                   # $debug .= "Ins:". $insertData." <br />";
                    $data = array(
                       'title'=>"$this->header_name: Upload file $import_file",
                       'contents' => 'upload/process_tair',
                       'debug' => $debug,
                       'file_name' =>$File2Upload,
                       'Path' => $Path,
                       'file_size' =>$File_Size,
                       'info' => "$ll lines in file",                      
                       'organism' => $id_organism,
                      // 'required_tag' => $this->required_tag
                      );
                    $this->load->view("templates/template", $data);
                    }
             }
        }
         else
         {
            $message =validation_errors();
            $this->session->set_flashdata('message', $message);
            redirect("create_table/upload_tair");
         }
    }
    
}
