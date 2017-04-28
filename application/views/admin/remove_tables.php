<?php
/**
* The Expression Database.
* view admin/remove_tables
* list master tables and dependencies
*
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage views
*/
print "<!-- //////////////    admin/remove_tables  //////////////  -->\n";
print "<div class=\"row\">\n";
print "    <div  id=\"param\" class=\"col-md-12 center-block\"> \n";
print "         <a href=\"../create_table\">back to Admin Express Db</a><br />\n";

print $this->session->flashdata('message')."<br />\n";
print "         <table class=\"table table-hover table-condensed table-bordered\">\n";
print "             <thead>\n";
print "                 <tr><th>TableName</th><th>Dependencies</th><th>Engine</th><th>Nbr lines</th><th>Size</th><th>Created Date</th><th>Submitter</th><th>version</th><th>Action</th></tr>\n";
print "            </thead>\n";
print "            <tbody>\n";
$same_table = "";
$id_form=1;
$root_table =array();
foreach($tables->result as $row)
{
    
    $IdTable = $row['IdTables'];
    $TableName = $row['TableName'];
    $MasterGroup = $row['MasterGroup'];
    $GroupName = $row['name'];
    $Organism = $row['Organism'];
    $Submitter = $row['Submitter'];
    $version = $row['version'];
    $Root= $row['Root']; 
    $anc_action =   form_submit('submit', "Delete table",'target="_blank"'); 
    $Dependencies = "";
    $Engine = $Nbrlines = $Size = $CreatedDate = "";
    print form_open(uri_string(),"id='form_$id_form'",'target="_blank"');
    print form_hidden('TableName',$TableName);
    if($Root )
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
            if(preg_match("/$TableName/",$Table) )
            {
                   if($Table==$TableName) 
                   {
                       $Dependencies .="<input type=checkbox name=\"delete_table[]\" value='$Table' /> <b>$Table</b><br />";
                       $Dependencies .="<input type=hidden name=\"master_table[$IdTables]\" value='$Table' />";
                   }
                   elseif($Table == "Annotation_$TableName")
                   {
                       $Dependencies .="<input type=checkbox name=\"delete_table[]\" value='$Table' /> $Table [A]<br />";
                   }
                   elseif(preg_match("/${TableName}_0/",$Table))
                   {
                       $Dependencies .="<input type=checkbox name=\"delete_table[]\" value='$Table' /> $Table<br />";
                   }
                   
                  $Engine .= $ENGINE."<br />";
                  $Nbrlines .= $TABLE_ROWS."<br />";
                  $Size .= $DATA_LENGTH."<br />";
                  $CreatedDate .= $CREATE_TIME."<br />";
             }
        }        
        print "                 <td>$Dependencies</td>\n"; 
        print "                 <td>$Engine</td><td>$Nbrlines</td><td>$Size</td><td>$CreatedDate</td>\n";
        print "                 <td>$Submitter</td>\n";
        print "                 <td>$version</td>\n";
        print "                 <td>$anc_action</td>\n";
        print "              </tr>\n";
    }
    print form_close();
    $id_form++;
}
print "             </tbody>\n";
print "         </table>\n";
print "<br />\n";

print "<div class=\"legend\">[A]: annotation file <br />  <b>Table_Name</b>: DataSet <br /> *_Cluster *_Order: Cluster analysis</div>\n";
print "    </div><!-- End DIV param -->\n";
print " </div><!-- End DIV rows -->\n";
print "<!-- //////////////    End admin/remove_tables  //////////////  -->\n";
