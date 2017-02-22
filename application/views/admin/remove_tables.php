<?php
print "<div class=\"row\">\n";
print "    <div  id=\"param\" class=\"col-md-8 center-block\"> \n";
print "         <a href=\"create_table\">back to Admin Express Db</a><br />\n";

print $this->session->flashdata('message')."<br />";
print "<table class=\"table table-hover table-condensed table-bordered\"  >\n";
print " <thead>\n";
print "         <tr><th>TableName</th><th>Dependencies</th><th>Engine</th><th>Nbr lines</th><th>Size</th><th>Created Date</th><th>Submitter</th><th>version</th><th>Action</th></tr>\n";
print " </thead>\n";
print " <tbody>\n";
$same_table = "";
#print "groups: $groups->sql <br>";
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
    if($Root)
    {
        print "    <tr>\n";
        print "            <td>$TableName</td>\n";
       // print "            <td>$GroupName\n";
        foreach($listeTbls as $key =>$row)
        {
           # print "TableName '$TableName' row '".$row['TABLE_NAME']."' '".$row['TableName']."'<br>";
            $Table = $row['TABLE_NAME'];
            $ENGINE = $row['ENGINE'];
            $TABLE_ROWS = $row['TABLE_ROWS'];
            $DATA_LENGTH = $row['DATA_LENGTH'];
            $CREATE_TIME = strstr($row['CREATE_TIME'],' ',1);
            
            $IdTables= $row['IdTables'];
            $Table_Name= $row['TableName'];
            
            /*  $Table= $row->TABLE_NAME;
            $IdTables= $row->IdTables;
            $TableName= $row->TableName;*/
             if(preg_match("/$TableName/",$Table) )
             {
                   # print "<br />$group";
                   if($Table==$Table_Name) 
                   {
                       $Dependencies .="<input type=checkbox name=\"delete_table[]\" value='$Table' /><b>$Table ($IdTables)</b><br />";
                       $Dependencies .="<input type=hidden name=\"master_table[$IdTables]\" value='$Table' />";
                   }
                   if($Table == "Annotation_$Table_Name")
                   {
                       $Dependencies .="<input type=checkbox name=\"delete_table[]\" value='$Table' />$Table [A]<br />";
                   }
                  if($Table == $Table_Name."_0" OR preg_match("/Annotation_$Table_Name/",$Table))
                   {
                       $Dependencies .="<input type=checkbox name=\"delete_table[]\" value='$Table' />$Table($IdTables)<br />";
                   }
                  $Engine .= $ENGINE."<br />";
                  $Nbrlines .= $TABLE_ROWS."<br />";
                  $Size .= $DATA_LENGTH."<br />";
                  $CreatedDate .= $CREATE_TIME."<br />";
             }
        }
       // print "             </td>\n";
        
        print "            <td>$Dependencies</td>\n"; 
        print "            <td>$Engine</td><td>$Nbrlines</td><td>$Size</td><td>$CreatedDate</td>\n";
        print "            <td>$Submitter</td>\n";
        print "            <td>$version</td>\n";
        print "            <td>$anc_action</td>\n";
        print "    </tr>\n";
    }
    print form_close();
    $id_form++;
}
print " </tbody>\n";
print "</table>\n";

print "         </div>\n";
print " </div>\n";
#print "Table  $Table IdTables $IdTables TableName $TableName <br />";
# print "<pre>".print_r($listeTbls[1],1)."</pre>";
