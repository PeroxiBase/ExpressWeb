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
           'footer_title' => $this->footer_title
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
        $GetWS=$this->expression_lib->working_space('','Upload');
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
           'footer_title' => $this->footer_title,
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
                       'footer_title' => $this->footer_title,
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
                    ######### get Max_Size for this organism #############
                    $organisms = $this->generic->get_organisms($id_organism); 
                    $max_size = $organisms->result->Max_transcript_size;
                    
                    $data = array(
                       'title'=>"$this->header_name: Upload file $import_file",
                       'contents' => 'upload/process_upload',
                       'footer_title' => $this->footer_title,
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
                       'max_size' => $max_size,
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
            $countFile =count( $UploadedFile);        }
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
                array_shift($UploadedFile);
        }
            
        ## new file size after header remove
        $countFile =count( $UploadedFile);
        $i=0;
        foreach($UploadedFile as $lines)
        {           
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
                #### check type
                $type="unknown";
                $option ="";
                if(preg_match('/^[0-9]+$/',$cols)) $type="INT";
                
                elseif(preg_match('/[-+]?([0-9]*\.[0-9]+|[0-9]+)/',$cols)) $type="DOUBLE";
                elseif(preg_match('/[0-9]{2}-[0-9]{2}-[0-9]{4}/',$cols))  $type="DATE";
                if(preg_match('/[a-zA-Z-_]([0-9]{1,})?(\.[0-9]+)?$/',$cols)) 
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
                  case 'VARCHAR':
                      $option = "";
                      break;
                }
                
                 if($i==0)
                 {
                     $max_value_col[$clnb]['size']= $col_size;
                     $max_value_col[$clnb]['type']= $type;
                     $max_value_col[$clnb]['option']= $option;
                 }
                 else
                 {
                     $prev_size=  $max_value_col[$clnb]['size'];
                     $prev_type= $max_value_col[$clnb]['type'];
                     $prev_option= $max_value_col[$clnb]['option'];
                     if ($col_size > $prev_size) 
                     {
                        $max_value_col[$clnb]['size']= $col_size;
                     }
                     
                     if($type != $prev_type)
                     {
                         $max_value_col[$clnb]['type']= $type;
                         if($prev_option == "SIGNED" && $type!= "VARCHAR")
                             $max_value_col[$clnb]['option']= "SIGNED";
                         else  $max_value_col[$clnb]['option']= $option;
                     }                         
                 }
                 
                 array_push($data_columns,$cols);                     
                 $clnb++;
            }
            array_push($Datas_columns,$data_columns);
           if(isset($limit) && $limit!="" && $i> $limit) 
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
                     }
                }
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
            $sql_data_CT .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8;";
            $sql_data_CT_deb .= "
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;<br />";
            
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
                $child = $insert_in_db->IdTable;
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
                    if($comment_geneName==1) $insertData .=" '$comment_value',";
                    $insertData = trim($insertData,',');
                    $insertData .= "),";
                    $insert_size = strlen($insertData);
                    
                    if($insert_size >1000000)
                    {
                        $insertData = trim($insertData,','); 
                        $insertData .= ";";
                        $lines= $ll - $prev_lines;
                        $DbInfo = array('table_name' => "$table_name",'line' => "$lines");
                        $insert_in_db = $this->generic->insert_Sql_data($insertData,$DbInfo);
                        $debug .= "Ins:".$insert_in_db->info."<br />";
                        $insert_size =0;
                        $prev_lines = $ll;
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
                if($this->db->table_exists($originalAnnot))
                {
                   #$rules = $this->expression_lib->verifyGeneNameStruct($GeneName);
                   $test= $this->generic->extract_annot($originalAnnot,$table_name,$child);
                } 
                else
                {                 
                   #$rules = $this->expression_lib->verifyGeneNameStruct($GeneName);
                   $test= $this->generic->extract_annot($originalAnnot,$table_name,$child);
                }
                ########################################################################################
             
                $data = array(
                   'title'=>"$this->header_name: Table successfully created",
                   'contents' => 'upload/create_table',
                   'footer_title' => $this->footer_title,
                    'POST' =>$_POST,
                   'createtable' => $sql_data_CT_deb,
                   'insertData' => $insertData_deb,
		   'test' => $test,
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
                   'footer_title' => $this->footer_title,
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
               'footer_title' => $this->footer_title,
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
                $organisms = $this->generic->get_organisms($id_organism); 
                $max_size = $organisms->result->Max_transcript_size;
                log_message('debug',"Create_table::create_annot 813 ".print_r( $organisms,1)." max_size $max_size ");
                $this->generic->create_annot_table($annoTable,$id,$max_size,$file_name);
                $tables = array('TableName' =>$annoTable);
            }
	}
	else
	{
	    $organisms = $this->generic->get_organisms();
	    foreach ($organisms->result as $row)
            {
                $id =$row['idOrganisms'];
                $req= $this->db->query("SELECT TableName FROM tables WHERE TableName = 'Annotation_$id'");
                $organism =$row['Organism'];
                if($req->num_rows()>0)
                {
                    $OrgaOpt .= "<option value=\"$id\" >$organism !!</option>";
                }
                else
                {
                    $OrgaOpt .= "<option value=\"$id\" >$organism</option>";
                }
            }
           
            $data = array(
               'title'=>"$this->header_name: Create annotation ",
               'contents' => 'upload/create_annotation',
               'footer_title' => $this->footer_title,
               'organisms' => $OrgaOpt, //json_encode($organisms->result),
               'debug' => $debug,
               'success' => 'none',
              );
            $this->load->view('templates/template', $data);
        }
    }

    /**
    * function 
    * 
    * @staticvar integer $staticvar 
    * @param string $param1 
    * @param string $param2 
    * @return integer 
    */  
    public function update_tables_annot()
    {
        if(isset($_POST['submit']) )
	{
	     $this->load->library('form_validation');
	     
	    if(isset($_POST['update_table'])  )
	    {	        
	        $type_submit = $this->input->post('submit');
	        $update_table = $this->input->post('update_table');
	        foreach($update_table as $key=>$val)
	        {
	            $child=$key;
	            $table_name = $val;	            
	        }
	        $id= $this->input->post('organism');
	        $id= $id[$table_name];
	        #### check if user resubmit same page !!
	        $annot_session = $this->session->userdata('updated_annotation');
	        if ($annot_session == $table_name)
	        {
	            $this->session->unset_userdata('updated_annotation');
                    $_POST =array();
                    redirect('create_table','refresh');
                    
                }
                else
                {
                   $create_annot= "  organism $id table_name $table_name type_submit $type_submit <br />";
                    
                    $annoTable="Annotation_$id";
                    if( $type_submit == "Re-Generate")
                    {
                        $create_annot = $this->generic->extract_annot($annoTable,$table_name,$child,1); 
                        #$tables = array('TableName' =>$annoTable);*/
                        $process =" renew";
                    }
                    else
                    {
                        $create_annot = $this->generic->extract_annot($annoTable,$table_name,$child,0);     
                        $process =" new";
                    }                
                
                    $data = array(
                       'title'=>"$this->header_name: Update annotation ",
                       'contents' => 'upload/process_annotation_update',
                       'footer_title' => $this->footer_title,
                       'info' => $create_annot,
                       'table_name' => $table_name,
                       'process' => $process
                      );
                    $this->load->view('templates/template', $data);
                }
                
            }
        }
        else
        {
            $this->session->unset_userdata('updated_annotation');
            $listeTbls =$this->generic->get_removable_table("TableName  LIKE 'Annotation_%'");
            $tables = $this->generic->get_tables('',1);
            $data = array(
               'title'=>"$this->header_name: Check annotation Tables ",
               'contents' => 'upload/list_tables',
               'footer_title' => $this->footer_title,
               'listeTbls' => $listeTbls->result,
               'tables' => $tables,
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
    public function update_annot_page()
    {
        $organisms = $this->generic->get_organisms();
        foreach ($organisms->result as $row)
        {
            $id =$row['idOrganisms'];
            $req= $this->db->query("SELECT TableName FROM tables WHERE TableName = 'Annotation_$id'");
            $organism =$row['Organism'];
            if($req->num_rows()>0)
            {
                $OrgaOpt .= "<option value=\"$id\" >$organism</option>";
            }
            else
            {
              #  $OrgaOpt .= "<option value=\"$id\" >$organism</option>";
            }
        }
        $data = array(
           'title'=>"$this->header_name: Edit annotation ",
           'contents' => 'upload/update_annotation',
           'footer_title' => $this->footer_title,
           'success' => 'none',
           'organisms' => $OrgaOpt, //json_encode($organisms->result),
          );
        $this->load->view('templates/template', $data);
    }

    
    
    public function load_annot()
    {
	$this->load->dbforge();
	$this->load->library('form_validation');
	
	if(isset($_POST['selectID']) && isset($_FILES['upload_file'] ) )
	{
	    $this->form_validation->set_rules('selectID','Organism name', 'trim|numeric|greater_than[0]|required');
	    $this->form_validation->set_rules('upload_file','upload file', 'trim|required');
            $id = $this->input->post('selectID');
            $annoTable = "Annotation_$id";
            $file_name = $_FILES['upload_file']['name'];
            $annotFile = $_FILES['upload_file']['tmp_name'];
            $Force_Update = $this->input->post('Force_Update');
            $header = $this->input->post('header');
            ############# check if table exist. otherwise create it
            $tables=$this->db->table_exists($annoTable);
           
            if($tables==0)
            {
                $organisms = $this->generic->get_organisms($id_organism); 
                $max_size = $organisms->result->Max_transcript_size;
                $this->generic->create_annot_table($annoTable,$id,$max_size,$file_name);
                $tables = array('TableName' =>$annoTable);
            }
             
            ############## READ ANNOTATION FILE //
            $csv = array();
            $lines = file($annotFile, FILE_IGNORE_NEW_LINES);
            $error=FALSE;
            $upload_error = "";
            #############  find seprator used and check field's number
            $del=$this->expression_lib->readCSV($lines[0]);
            $good_del=$del->delimeter;
            $csv_count = count(str_getcsv($lines[0],"$good_del"));
            if($csv_count >5 OR $this->form_validation->run() == FALSE) 
            {
                $data = array(
                        'title'=>"$this->header_name: Error in file",
                        'contents' => 'upload/error',
                        'footer_title' => $this->footer_title,
                        'success' => 'success',
                        'error' => "File $file_name contains $csv_count fields !!. Upload aborted".print_r($_POST,1),
                         
                     );
                
                $this->load->view('templates/template', $data);                 
            }
            else
            {
		foreach ($lines as $key => $value)
		{
			$csv[$key] = str_getcsv($value,"\t");
		}
		### remove first line if header
		if(isset($header) && $header ==1) array_shift($csv);
		
		##### if force update truncate table
		if($Force_Update ==1) 
                {
                    $Table_already_set="";
                    $truncate = $this->db->query("TRUNCATE $annoTable");   
                    #$upload_error .= "Erase previous table $annoTable. return : <pre>".print_r($truncate,1)."</pre><br />";
                }
               
		// ADD INTO ANNOTATION TABLE //
		$i=1; 
		
		foreach($csv as $line)
		{
		     if(count($line) <4 ) 
		     {
		         $upload_error .= "error on line $i . nbr fields ".count($line)."<br />";
		         $i++;
		         continue;
		     }
		     
                    if(!isset($line[4])){
                            $line[4]="";
                    }
                    if(strlen($line[0])==0) continue;
                    if(strlen($line[2])==0) continue;
                    $data=array(
                            'Gene_Name' => $line[0],
                            'Analyse' => $line[1],
                            'Signature' => $line[2],
                            'Description' => $line[3],
                            'misc' => $line[4]
                    );
                    ### check if exist in table
                    $query = $this->db->select('*')->where($data)->get($annoTable);
                    #   print "D: $i <pre>".print_r($query->result_array(),1)."  ".print_r($data,1)." ".print_r($tables,1)." </pre>  <br />";
                    if(count($query->result_array()) ==0 )
                    {
                        $this->db->insert($annoTable, $data);
                    }
                $i++;
		}
		// LOAD RESULTS VIEW //
		
                $organisms = $this->generic->get_organisms();
                foreach ($organisms->result as $row)
                {
                    $id =$row['idOrganisms'];
                    $req= $this->db->query("SELECT TableName FROM tables WHERE TableName = 'Annotation_$id'");
                    $organism =$row['Organism'];
                    if($req->num_rows()>0)
                    {
                        $OrgaOpt .= "<option value=\"$id\" >$organism</option>";
                    }
                    else
                    {
                      #  $OrgaOpt .= "<option value=\"$id\" >$organism</option>";
                    }
                }
                $data = array(
                   'title'=>"$this->header_name: Edit annotation ",
                   'contents' => 'upload/update_annotation',
                   'footer_title' => $this->footer_title,
                   'success' => 'none',
                   'organisms' => $OrgaOpt, //json_encode($organisms->result),
                        'upload_error' => $upload_error,
                     );
                $this->load->view('templates/template', $data);
	     }
	} // End if(isset($_POST)
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
        $GetWS=$this->expression_lib->working_space('','Upload');
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
            $id =$row['idOrganisms'];
            $req= $this->db->query("SELECT TableName FROM tables WHERE TableName = 'Annotation_$id'");
            $organism =$row['Organism'];
            if($req->num_rows()>0)
            {
                $OrgaOpt .= "<option value=\"$id\" >$organism !!</option>";
            }
            else
            {
                $OrgaOpt .= "<option value=\"$id\" >$organism</option>";
            }
        }
        $data = array(
           'title'=>"$this->header_name: Upload Phytozome annotation",
           'contents' => 'upload/upload_phytozom',
           'footer_title' => $this->footer_title,
           'pid' =>$pid,
           'organism' => $OrgaOpt
          );
        $this->load->view('templates/template', $data);
    }
    
     public function update_phytozom()
    {        
        $this->session->unset_userdata('updated_table_phyto');
        $GetWS=$this->expression_lib->working_space('','Upload');
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
            $id =$row['idOrganisms'];
            $req= $this->db->query("SELECT TableName FROM tables WHERE TableName = 'Annotation_$id'");
            $organism =$row['Organism'];
            if($req->num_rows()>0)
            {
                $OrgaOpt .= "<option value=\"$id\" >$organism</option>";
            }
        }
        $data = array(
           'title'=>"$this->header_name: Update Phytozome annotation",
           'contents' => 'upload/update_phytozom',
           'footer_title' => $this->footer_title,
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
            $pid = $this->input->post('pid');
            $id_organism = $this->input->post('organism');
            $Force_Update = $this->input->post('Force_Update');
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
                       'footer_title' => $this->footer_title,
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
                        $truncate = $this->db->query("TRUNCATE $table_name");   
                        $debug .= "erase previous table $table_name. return : <pre>".print_r($truncate,1)."</pre><br />";
                        
                    }
                    
                    if($Table_already_set !==FALSE && $Table_already_set!= $table_name)
                    {
                        $organisms = $this->generic->get_organisms($id_organism); 
                        $max_size = $organisms->result->Max_transcript_size;
                        #log_message('debug',"Create_table::convert_phytozome_annot 1243 ".print_r( $organisms,1)." max_size $max_size ");
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
                        #$debug .="$sql_data_CT->info";
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
                        * 1 #pacid
                        * 2 locusName         AT1G01050                       Gene_Name
                        * 3 transcriptName    AT1G01050.1
                        * 4 peptideName       AT1G01050.1                     Analyse   Signature       Desc  Misc
                        * 5 Pfam              PF00719                         Pfam      PF00719
                        * 6 Panther           PTHR10286,PTHR10286:SF10        PANTHER   PTHR10286
                        * 7 KOG               KOG1626                         KOG       KOG1626
                        * 8 KEGG/ec	          3.6.1.1	                  KEGG      3.6.1.1
                        * 9 KO                K01507                          KO                K01507
                        * 10 GO                GO:GO:0006796,GO:GO:0005737     GO        GO:0006796
                        * 11 Best-hit-arabi-name    LOC_Os05g36260.1	       description   wall associated kinase 3
                        * 12 arabi-symbol       WAK3
                        * 13 defline            wall associated kinase 3
                        */
                        $separator="\t";
                        $insert_size = 0;
                        $ll=0;
                        $insertData_deb ="";
                        $insertData ="";
                        $wrong_lines = 0;
                        $prev_lines =0;
                        $ColNames = "`Gene_Name`,`Analyse`,`Signature`,`Description`,`misc`";
                        
                        foreach($UploadedFile as $lines)
                        {
                           # $debug .=  "lines $lines <br>";
                           ####  count fields !! . If count <12 exit . not a Phytozome file
                           
                            $lines= trim($lines);
                            $count_fields =count( explode($separator,$lines));
                            if($ll==0 && $count_fields<13)
                            {
                                   $message = "<b> File submitted contains less than 13 fields ($count_fields) . Please submit only annotation file from Phytozom !<br />$lines</b>";
                                    $this->session->set_flashdata('message', $message);
                                    redirect("create_table/upload_phytozom");
                            }
                            if($ll==0) 
                            {
                                $ll++;
                                continue;
                            }
                            ###############################################################
                            ### set list() values to 0 to avoid undeclared index
                            $pacid = $locusName = $transcript = $peptide = $Pfam = $Panther = $KOG = $KEGG = $KO = $GO = $Best = $symbol = $defline = "";
                           # log_message('debug', "convert_phytozome_annot line: $lines"); 
                            
                            #log_message('debug', "convert_phytozome_annot extract line: ".print_r($Content,1).""); 
                            if($count_fields<6)
                            {
                                log_message('debug', "convert_phytozome_annot $count_fields  : $lines");
                                $wrong_lines++;
                                continue;
                            }
                            $Content =explode($separator,$lines);
                            #list($pacid,$locusName,$transcript,$peptide,$Pfam,$Panther,$KOG,$KEGG,$KO,$GO,$Best) = explode($separator,$lines);
                             $pacid = $Content[0];
                             $locusName = $Content[1];
                             $transcript = $Content[2];
                             $peptide = $Content[3];
                             $Pfam = (isset($Content[4]))? $Pfam = $Content[4]:$Pfam ="";
                             $Panther = (isset($Content[5]))? $Panther = $Content[5]:$Panther ="";
                             $KOG = (isset($Content[6]))? $KOG = $Content[6]:$KOG ="";
                             $KEGG = (isset($Content[7]))? $KEGG = $Content[7]:$KEGG ="";
                             $KO = (isset($Content[8]))? $KO = $Content[8]:$KO ="";
                             $GO = (isset($Content[9]))? $GO = $Content[9]:$GO ="";
                            
                            $line=explode($separator,$lines);
                            $data_columns =  array();
                            $clnb=0;
                            $pass=false;
                            
                            $Gene_Name=trim($transcript);
                            $Sql_value =" ";
                            if($insert_size==0)
                            {
                                 $insertData ="REPLACE INTO `$table_name` ($ColNames) ";
                                 $insertData .=" VALUES  ";
                            }
                            
                            ########## PFAM ###############
                            if(strlen($Pfam)>=7)
                            {
                                if(strlen($Pfam)>7)
                                {
                                    $PFAMS=preg_split('/,/',$Pfam);
                                    foreach($PFAMS as $key=>$value)
                                    {
                                        $annot=$this->generic->get_PFAM_Ref($value);
                                        $annot= addslashes($annot);  
                                        $Sql_value .= "('$Gene_Name','Pfam','$value','$annot',''),";   
                                    }
                                }
                                elseif(strlen($Pfam)==7)
                                {
                                    $annot=$this->generic->get_PFAM_Ref($Pfam);
                                    $annot= addslashes($annot);  
                                    $Sql_value .= "('$Gene_Name','Pfam','$Pfam','$annot',''),"; 
                                }
                                $key = $value ="";
                            }
                            ########## PANTHER ###############
                            if(strlen($Panther)>=9)
                            {
                                if(strlen($Panther)>9)
                                {
                                    $Panthers=preg_split('/,/',$Panther);
                                    foreach($Panthers as $key=>$value)
                                    {
                                        $annot=$this->generic->get_PANTHER_Ref($value);
                                        $annot= addslashes($annot);  
                                        $Sql_value .= "('$Gene_Name','PANTHER','$value','$annot',''),";   
                                    }
                                }
                                elseif(strlen($Panther)==9)
                                {
                                    $annot=$this->generic->get_PANTHER_Ref($Panther);
                                    $annot= addslashes($annot);  
                                    $Sql_value .= "('$Gene_Name','PANTHER','$Panther','$annot',''),"; 
                                }
                                $key = $value ="";
                            }
                            ########## KOG ###############
                            if(strlen($KOG)==7)
                            {
                                    $annot=$this->generic->get_KOG_Ref($KOG);
                                    $annot= addslashes($annot);  
                                    $Sql_value .= "('$Gene_Name','KOG','$KOG','$annot',''),"; 
                            }
                            ########## KEGG ###############
                            if(strlen($KEGG)>=7 && strlen($KEGG)<=11)
                            {
                                    $annot=$this->generic->get_KEGG_Ref($KEGG);
                                    $annot= addslashes($annot);  
                                    $Sql_value .= "('$Gene_Name','KEGG','$KEGG','$annot',''),"; 
                            }
                            ########## KO ###############
                            if(strlen($KO)==6 )
                            {       
                                   $annot=$this->generic->get_KO_Ref($KO);
                                   $annot= addslashes($annot);  
                                   $Sql_value .= "('$Gene_Name','KO','$KO','$annot',''),"; 
                            }
                            ########## GO ###############
                            if(strlen($GO)>=10)
                            {
                                if(strlen($GO)>13)
                                {
                                    $GOS=explode(',',$GO);
                                  #  print "GOS : ".print_r($GOS,1)."<br />";
                                    foreach($GOS as $key=>$value)
                                    {
                                        #$value = substr($value,3);
                                        #log_message('debug', "convert_phytozome_annot M GO: $value"); 
                                        $res=$this->generic->get_GO_Ref($value);
                                        $annot= addslashes($res->annot);
                                        $Sql_value .= "('$Gene_Name','GO','$value','$annot','$res->type'),";   
                                    }
                                }
                                elseif(strlen($GO)==10)
                                { 
                                    #log_message('debug', "convert_phytozome_annot GO: $GO"); 
                                    $value = trim($GO);
                                    $res=$this->generic->get_GO_Ref($value);
                                    $annot= addslashes($res->annot);                                
                                    $Sql_value .= "('$Gene_Name','GO','$value','$annot','$res->type'),";
                                }
                            }
                            
                            $ll++;
                            $insertData .= $Sql_value;
                            $insert_size = strlen($insertData);
                    
                            if($insert_size >100000)
                            {
                                #$debug .= "create_table  insert_size: $insert_size <br />";
                                #log_message('debug', "convert_phytozome_annot insert_size >10K insert data");
                                $insertData = trim($insertData,','); 
                                $insertData .= ";";
                                $lines= $ll - $prev_lines;
                                $insert_in_db = $this->db->query($insertData);
                                #$debug .= "Ins:". $insertData." <br />";
                                $insert_size =0;
                                $prev_lines = $ll;
                            }
                            
                        } // end foreach(uploadFile)
                        
                        $insertData = trim($insertData,','); 
                        $DbInfo = array('table_name' => "$table_name",'line' => "$lines");
                        $insert_in_db = $this->db->query($insertData);
                        $debug .=  "lines inserted $ll <br>";                        
                        $debug .= "lines with missings fields $wrong_lines . Look log files<br />";
                        $data = array(
                           'title'=>"$this->header_name: Upload file $import_file",
                           'contents' => 'upload/process_phytozom',
                           'footer_title' => $this->footer_title,
                           'debug' => $debug,
                           'file_name' =>$File2Upload,
                           'Path' => $Path,
                           'file_size' =>$File_Size,
                           'info' => "$ll lines in file",                      
                           'organism' => $id_organism,
                           'table_name' => $table_name
                          );
                        $this->load->view("templates/template", $data);
                    }
                    else
                    {
                        $data = array(
                           'title'=>"$this->header_name: Upload file $import_file",
                           'contents' => 'upload/process_phytozom',
                           'footer_title' => $this->footer_title,
                           'debug' => "Data have already been inserted Table_already_set: |$Table_already_set|",
                           'file_name' =>$File2Upload,
                           'Path' => $Path,
                           'file_size' =>$File_Size,
                           'info' => "error",                      
                           'organism' => $id_organism,
                           'table_name' => $table_name
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
    }// End Fct convert_phytozome
    
  
}
