<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Expression_lib {
  	private $CI;
  	#define ('_ERROR','');
  	#define ('_WARNING','');
    public function __construct()
    {
       $this->CI =& get_instance();
       $this->auth = new stdClass;
       $this->CI->load->model('generic');
       $this->CI->load->library('session');
       #$this->CI->load->library('flexi_auth');
      # $this->CI->load->library('files');
    }
  	
    public function getPid()
    {
     # $session_id =$this->CI->session->userdata('session_id');
     # $pid = substr($session_id,0,10);     
      $pid=trim(strstr(microtime(),' '));
      return $pid;
    }
 
    public function working_space($pid='',$Prg='')
    {
        $WS = new stdclass;
        $username= $this->CI->session->username;
        $working_project= $this->CI->session->working_project;
        $pid = $this->CI->session->pid;
        $day_date=date("Y_m_d");
        if($pid=='')  
        {
            $session = $this->getPid();
            
        }
        else $session = $pid;
        $WS->pid =$session;
        if(isset($username) AND $username!='')
        {
          # Check user dir exist
          if(is_dir("./assets/users/$username")==false)
          {
            mkdir("./assets/users/$username",0775);
             chmod("./assets/users/$username",0775);
             chgrp("./assets/users/$username","perox");
          }
          if(isset($working_project) AND $working_project!='')
          {
            # Check user dir exist
            if(is_dir("./assets/users/$username/$working_project")==false)
            {
              mkdir("./assets/users/$username/$working_project",0775);
              chmod("./assets/users/$username/$working_project",0775);
              chgrp("./assets/users/$username/$working_project","perox");
            }
             $Path=$username.'/'.$working_project;
          }
          else { $Path=$username; }
          # Check or create dir of the day
         /*
          if(is_dir("./assets/users/$Path/$day_date")==false)
          {
            mkdir("./assets/users/$Path/$day_date",0775);
            chmod ("./assets/users/$Path/$day_date",0775);
            chgrp("./assets/users/$Path/$day_date","perox");
          }
          else chmod ("./assets/users/$Path/$day_date",0775);
          if($Prg!='')
          {
             if(is_dir("./assets/users/$Path/$day_date/$Prg")==false)
            {
              mkdir("./assets/users/$Path/$day_date/$Prg",0775);
              chmod("./assets/users/$Path/$day_date/$Prg",0775);
              chgrp("./assets/users/$Path/$day_date/$Prg","perox");
            }
            $WS->Path = "./assets/users/$Path/$day_date/$Prg/";     
          }
          else
          {
            $WS->Path = "./assets/users/$Path/$day_date/";
          }
          */
          if($Prg!='')
          {
              
              if(is_dir("./assets/users/$Path/$Prg")==false)
            {
              mkdir("./assets/users/$Path/$Prg",0775);
              chmod("./assets/users/$Path/$Prg",0775);
              chgrp("./assets/users/$Path/$Prg","perox");
            }
            $WS->Path = "./assets/users/$Path/$Prg/";     
          }
          else
         {
             $WS->Path = "./assets/users/$Path/";
         }
          $WS->session = $session;
          # $username= $this->CI->session->username;
          #$working_project= $this->CI->session->working_project;
          $this->CI->session->set_userdata('working_path',$WS->Path);
          $this->CI->session->set_userdata('pid',$WS->pid);
         # $this->CI->session->set_userdata(array($this->CI->auth->session_name['name'] => $this->CI->auth->session_data));
    
          return $WS;
        }
        else
        {
          if($Prg!='')
          {
             if(is_dir("./assets/temp/$Prg")==false)
            {
              mkdir("./assets/temp/$Prg",0775);
              chgrp("./assets/temp/$Prg","perox");
            }
            $WS->Path = "./assets/temp/$Prg/";     
          }
          else
          {
            $WS->Path = "./assets/temp/";
          }
          #$this->CI->auth->session_data[$this->CI->auth->session_name['working_path']] =   $WS->Path ;
         #   $this->CI->session->set_userdata(array($this->CI->auth->session_name['name'] => $this->CI->auth->session_data));
            #$WS->Path =  "assets/temp/";	
            $this->CI->session->set_userdata('working_path',$WS->Path);
            $this->CI->session->set_userdata('pid',$WS->pid);
          $WS->session = $session;
          return $WS;
        }
  }
  
 
    public function do_debug($active)
    {
        $debug=0;
        if($active==1)
        {
            #  define("DO_DEBUG",true);
            $active=1;
        }
        else 
        {
            # define("DO_DEBUG",false);
            $active=0;
        }
        
        /*$network=ip2long("194.199.55.0");
        $mask=ip2long("255.255.255.0");
        $remote=ip2long($_SERVER['REMOTE_ADDR']);
        
        if (($remote & $mask)==$network)
        {
            $debug=1;
        }
       */
        if($_SERVER["REMOTE_ADDR"]=="194.199.55.237" or 
        $_SERVER["REMOTE_ADDR"]=="82.241.248.32" 
        or $_SERVER["REMOTE_ADDR"]=="194.199.55.117" 
        or $_SERVER["REMOTE_ADDR"]=="194.199.55.51" 
        or $_SERVER["REMOTE_ADDR"]=="194.199.55.43"
        ) 
        {
            $debug=$active; $AddFields=1; $MasterIP=1;
        }
        return $debug;
    }
    
    public function formatLink($source,$name,$format='Source')
    {
        if($format=='' OR $format=='Source') $ad_format="<b>$source:</b> ";
        if($format=='NoSource') $ad_format="";
        $source=strtoupper($source);
        switch ($source)
        {
          case 'GENE3D':      
            $request_name=preg_replace("/G3DSA:/",'',$name);
            $anchor=anchor(sprintf($this->CI->Link['GENE3D'],$request_name),$ad_format.$name,'target="_blank"');
            break;
          case 'GO':      
            $anchor=anchor(sprintf($this->CI->Link['GO'],$name),$ad_format.$name,'target="_blank"');
            break;
          case 'HAMAP':      
            $anchor=anchor(sprintf($this->CI->Link['HAMAP'],$name),$ad_format.$name,'target="_blank"');
            break;
          case 'INTERPRO':      
            $anchor=anchor(sprintf($this->CI->Link['INTERPRO'],$name),$ad_format.$name,'target="_blank"');
            break;
          case 'KEGG':      
            $anchor=anchor(sprintf($this->CI->Link['KEGG'],$name),$ad_format.$name,'target="_blank"');
            break;
          case 'PANTHER':      
            $anchor=anchor(sprintf($this->CI->Link['PANTHER'],$name),$ad_format.$name,'target="_blank"');
            break;
          case 'PFAM':      
            $anchor=anchor(sprintf($this->CI->Link['PFAM'],$name),$ad_format.$name,'target="_blank"');
            break;
          case 'PFAMB':      
            $anchor=anchor(sprintf($this->CI->Link['PFAMB'],$name),$ad_format.$name,'target="_blank"');
            break;
          case 'PIRSF':      
            $anchor=anchor(sprintf($this->CI->Link['PIRSF'],$name),$ad_format.$name,'target="_blank"');
            break;
          case 'PRINTS':      
            $anchor=anchor(sprintf($this->CI->Link['PRINTS'],$name),$ad_format.$name,'target="_blank"');
            break;
          case 'PRODOM':      
            $anchor=anchor(sprintf($this->CI->Link[''],$name),$ad_format.$name,'target="_blank"');
            break;
          case 'PROSITE':case 'PROSITEPATTERNS':
          case 'PROSITE_PATTERNS':case 'PROSITEPROFILES':case 'PROSITE_PROFILES':
            $anchor=anchor(sprintf($this->CI->Link['PROSITE'],$name),$ad_format.$name,'target="_blank"');
            break;
          case 'SMART':      
            $anchor=anchor(sprintf($this->CI->Link['SMART'],$name),$ad_format.$name,'target="_blank"');
            break;
          case 'SUPERFAMILY':      
            /* 
            http://supfam.cs.bris.ac.uk/SUPERFAMILY/cgi-bin/allcombs.cgi?sf=46689;listtype=sf
            http://supfam.cs.bris.ac.uk/SUPERFAMILY/cgi-bin/allcombs.cgi?genome=Eg;sf=46689;;password=;subdomain=n
            
            */
            $request_ID=preg_replace("/SSF/",'',$name);
           # $anchor=anchor(sprintf($this->CI->Link['SUPERFAMILY'],$name),$ad_format.$name,'target="_blank"') ."<br />  ";
            $anchor =anchor("http://supfam.cs.bris.ac.uk/SUPERFAMILY/cgi-bin/allcombs.cgi?genome=Eg;sf=$request_ID",$ad_format.$name,'target="_blank"');
            break;
          case 'TIGRFAMs':
            $anchor=anchor(sprintf($this->CI->Link['TIGRFAMs'],$name),$ad_format.$name,'target="_blank"');
            break;
         default:
           $anchor=anchor($name,"<b>UKN ($source):</b> ".$name,'target="_blank"');
         
           break;
            
        }
        # print " anchor  $anchor <hr />";
        return $anchor; 
    }
 
    
    public function var_debug($mode)
    {  
      $export=var_export($mode,true);
      print "<div class=\"debug\" align=left>Debug:<hr />  $export </div>\n";
    }
    
    public function valid_error($classe,$contents,$message,$left_div='') 
    {
        #print "classe  $classe, contents $contents, message $message <br />";
        $data = array(
              'title'=>"The peroxidase: $classe",
              'contents' => "$contents/error",
              'left_div' => $left_div,
              'message' =>  $message,
              'back' => $this->CI->back()
              );
        return $data;
    }

    public function back($step = -1) 
     {
            return "    <a href=\"javascript:history.go($step)\"  class=\"ui-button ui-widget ui-state-default ui-corner-all\">&lt;&lt; Back</a>";
     }
     
    public function getGenre($GeneGenre,$format='Genre') 
    {
        $GeneGenre=trim(strtolower($GeneGenre));
        switch ($GeneGenre) 
        {
            case "eucalyptus":
            case "euc":
            case "egr":
                $GeneGenre="Egr";
                $GeneGenreFull="Eucalyptus";
                $GeneSpecie="grandis";
                break;
            case "populus":
            case "pop":
            case "ptr":
                $GeneGenre="Ptr";
                $GeneGenreFull="Populus";
                $GeneSpecie= "trichocarpa";
                
                break;  
            case "arabidospsis":
            case "ara":
            case "ath":
                $GeneGenre="Ath";
                $GeneGenreFull="Arabidopsis";
                $GeneSpecie= "thaliana";
                break;  
            case "oriza":
            case "osa":
                $GeneGenre="Osa";
                $GeneGenreFull="Oriza";
                $GeneSpecie= "sativa";
                break;  
        }
        
        switch ($format)
        {
            case '':
            case 'Genre':
                $GetGenre=$GeneGenre;
                break;
            
            case 'whole':
                $GetGenre = new stdclass;
                $GetGenre->Genre=$GeneGenreFull;
                $GetGenre->Specie=$GeneSpecie;
                break;
        }
        
        #var_dump($GetGenre);
        return $GetGenre;
    }
    
    public function getSpecie($GeneGenre,$GeneSpecie='') 
    {
        $GeneGenre = ucfirst(strtolower($GeneGenre));
        $GeneSpecie = strtolower($GeneSpecie);
        switch ($GeneGenre) 
        {
            case "Egr":
                if($GeneSpecie=='')     $GeneSpecie="grandis";
                break;
            case "Ptr":
                if($GeneSpecie=='')     $GeneSpecie= "trichocarpa";
                break;  
            case "Ath":
                if($GeneSpecie=='')     $GeneSpecie= "thaliana";
                break;  
            case "Osa":
                if($GeneSpecie=='')     $GeneSpecie= "sativa";
                break;  
        }
        return $GeneSpecie;
    }
     
    public function verifyGeneNameStruct($GeneName,$i='',$line='')
    {
        $html_data='';
        $Match = new stdclass;
        # add test previously in Upload fct
        $GGG =$GeneName;
        if(preg_match('/\//',$GeneName))
        {
            $GeneName=strstr($GGG, '/', true);
            $GeneAbrev=substr(strstr($GGG, '/'),1);
        } 
        
        $GeneName=strtoupper($GeneName);
        $len=strlen($GeneName);
        $Match->match=0;
        #Euca Eucgr.A00001.1 	
        if(preg_match('/^EUCGR/',$GeneName))
        {
            $Match->Genre='Egr';$Match->specie='grandis';
            if(preg_match('/(\w+)\.(\w+)\.(\d+)/',$GeneName,$ret ,PREG_OFFSET_CAPTURE)) 
            { 
                $Match->match=1; 
            }
            else if(preg_match('/(\w+)\.(\w{6})/',$GeneName,$ret ,PREG_OFFSET_CAPTURE)) 
            {  
                $Match->match=2;  
            }
            $rules='Eucgr.Wnnnnn(.n) W:[A-L] n:0-9';
        }
        #Ath AT1G01010.1
        if(preg_match('/^AT/',$GeneName))
        {
            $Match->Genre='Ath';$Match->specie='thaliana';
            #cas du transcript
            if(preg_match('/(\w+)\.(\d+)/',$GeneName,$ret ,PREG_OFFSET_CAPTURE)) 
            { 
                $Match->match=1; 
            }
            # cas du gene !
            else if(preg_match('/(\w{9})/',$GeneName,$ret ,PREG_OFFSET_CAPTURE)) 
            { 
                $Match->match=2;
            }
        }
        #Pop
        if(preg_match('/^POPTR/',$GeneName))
        {
            $Match->Genre='Ptr';$Match->specie='trichocarpa';
            if(preg_match('/(\w+)\.(\d+)/',$GeneName,$ret ,PREG_OFFSET_CAPTURE)) 
            { 
                $Match->match=1; 
            }
            else if(preg_match('/(\w{11})/',$GeneName,$ret ,PREG_OFFSET_CAPTURE)) 
            { 
                $Match->match=2; 
            }
        }
        # Osa ChrSy.fgenesh.mRNA.10 / LOC_Os01g01060.1
        if(preg_match('/^CHRSY|^CHRUN/',$GeneName))
        {
            $Match->Genre='Osa';$Match->specie='sativa';
            if(preg_match('/(\w+)\.(\w+)\.(\d+)/',$GeneName,$ret ,PREG_OFFSET_CAPTURE)) 
            {
                $Match->match=1; 
            }
        }
        if(preg_match('/^LOC/',$GeneName))
        {
            $Match->Genre='Osa';$Match->specie='sativa';
            if(preg_match('/(\w+)\.(\d+)/',$GeneName,$ret ,PREG_OFFSET_CAPTURE)) 
            {
                $Match->match=1; 
            }
        }
        # print " Gene $GeneName length $len match: $Match->match <br>";#exit;
        # add return info previously in Upload fct
        if($Match->match==0) 
        {
            #$html_data = "<tr><th colspan=2>Line $i</th></tr>\n";
            $html_data= "<tr><td colspan=2 class=\"warn\">GeneName $GeneName ($GGG) does not follow the validation rules:<br /> $rules!! </td></tr>\n";
            $html_data.= "<tr><td colspan=\"2\" class=\"small\">$line</td></tr>\n";
            $Match->html_data.=$html_data;
            return $Match;
            break;
        }
        return $Match;
    }
    
    public function trimGeneName($GName,$GGenre)
    {
        $GGenre=strtoupper($GGenre);
        if($GGenre=='EGR' OR $GGenre=='OSA' ) 
        {
            if(preg_match('/(\w+)\.(\w+)\.(\d+)/',$GName,$ret ,PREG_OFFSET_CAPTURE)) 
            { 
                $Match=1;
            }
            $GName=preg_replace('/(\w+)\.(\w+)\.(\d+)/','$1.$2',$GName);
            # print "Cleaned: $GName GeneOrtho  $GeneOrtho <br />";
            #exit;
        }
        else 
        {
            //  print "In else";
            if(preg_match('/(\w+)\.(\d+)/',$GName,$ret ,PREG_OFFSET_CAPTURE)) 
            { 
                $Match=1; 
            }
            $GName=preg_replace('/(\w+)\.(\d+)/','$1',$GName);
        }
        
        return $GName;      
    }


}
