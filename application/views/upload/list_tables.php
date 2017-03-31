<?php
/**
* The Expression Database.
* view upload/list_tables
* list master tables and annotation
*
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage views
*/
print "<!-- //////////////    upload/list_tables  //////////////  -->\n";
print "<div class=\"row\">\n";
print "    <div  id=\"param\" class=\"col-md-14 center-block\"> \n";
print "         <a href=\"../create_table\">back to Admin Express Db</a><br />\n";
$id_form=1;
print $this->session->flashdata('message')."<br />\n";
 print form_open(uri_string(),"id='form_$id_form'",'target="_blank"');
print "         <table class=\"table table-hover table-condensed table-bordered\">\n";
print "             <thead>\n";
print "                 <tr><th>TableName</th><th>Annotation</th><th>Organism</th><th>Nbr lines</th><th>Size</th><th>Created Date</th><th>Action</th></tr>\n";
print "            </thead>\n";
print "            <tbody>\n";
$same_table = "";

$root_table =array();
$tbl_checked = array();

foreach($tables->result as $row)
{
    
    $IdTable = $row['IdTables'];
    $TableName = $row['TableName'];
    $MasterGroup = $row['MasterGroup'];
    $GroupName = $row['name'];
    $Organism = $row['Organism'];
    $idOrganisms = $row['idOrganisms'];
    $Submitter = $row['Submitter'];
    $version = $row['version'];
    $Root= $row['Root']; 
    $Dependencies = "";
    $Engine = $Nbrlines = $Size = $CreatedDate = "";
   
    $table_annot = FALSE;
    if( preg_match("/Annotation_/",$TableName))
    {
            $table_annotRes = preg_match("/Annotation_\d$/",$TableName);
            $table_annot = $table_annotRes;
    }
    else $table_annot = TRUE;
    if($Root && $table_annot)
    {
        
        print "                 <tr>\n";
        print "                     <td>$TableName</td>\n";
        foreach($listeTbls as $key =>$row)
        {
            $Table = $row['TABLE_NAME'];
            $ENGINE = $row['ENGINE'];
            $TABLE_ROWS = $row['TABLE_ROWS'];
            $DATA_LENGTH = $row['DATA_LENGTH'];
            $CREATE_TIME = strstr($row['CREATE_TIME'],' ',1);
            
            $IdTables= $row['IdTables'];
            $Table_Name= $row['TableName']; 
            $void =true;
            if(preg_match("/$TableName/",$Table) )
            {
                if($Table == "Annotation_$TableName")
                {
                   $Dependencies .="<input type=radio name=\"update_table\" value='$TableName' /> $Table<br />";
                   $Dependencies .="<input type=hidden name=\"organism[$TableName]\" value='$idOrganisms' /><br />";
                   $anc_action =   form_submit('submit', "Re-Generate",'target="_blank"'); 
                   #$Nbrlines = $this->db->query("select count(*) as Nblines from $Table")->row('Nblines');
                   $void =false;
                   array_push($tbl_checked,$TableName);
                }
                else if($Table == $TableName)
                {
                   $Dependencies .="";
                   $anc_action =  anchor("create_table/update_annot_page","Update","title='Update by resubmiting original annotation file!'"); 
                   $void =false;
                   array_push($tbl_checked,$TableName);
                }
                $Engine .= $ENGINE."<br />";
                $Nbrlines .= $TABLE_ROWS."<br />";
                $Size .= $DATA_LENGTH."<br />";
                $CreatedDate .= $CREATE_TIME."<br />";
            }
        }//End foreach(ListTbl)
        if(!in_array($TableName,$tbl_checked))
        {
           $Dependencies .="<input type=radio name=\"update_table\" value='$TableName' /> process";           
           $Dependencies .="<input type=hidden name=\"organism[$TableName]\" value='$idOrganisms' /><br />";
           $anc_action =   form_submit('submit', "Generate",'target="_blank"'); 
        }
        print "                 <td>$Dependencies</td>\n"; 
        print "                 <td>$Organism</td><td>$Nbrlines</td><td>$Size</td><td>$CreatedDate</td>\n";
        print "                 <td>$anc_action</td>\n";
        print "              </tr>\n";
    } // End if($Root)   
   
    $id_form++;
}

print form_close();

print "             </tbody>\n";
print "         </table>\n";

print "    </div><!-- End DIV param -->\n";
print " </div><!-- End DIV rows -->\n";
print "<!-- //////////////    End upload/list_tables  //////////////  -->\n";
