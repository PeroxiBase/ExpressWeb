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
print "<div class=\"row\"><br />\n";
print "    <div  id=\"param\" class=\"col-md-12 center-block\"> \n";
print "         <a href=\"../create_table\">back to Admin Db</a><br />\n";

print " <h1>Reset Db</h1>\n";
print " <div class=\"label-danger col-md-8\"><strong>WARNING</strong><br /> Validating this form will:<br />\n";
print "      <ul>\n";
print "         <li>Erase all dataset, annotation, toolbox.</li>";
print "         <li>truncate Organisms table </li>";
print "         <li>remove all datas in assets/networks, assets/similarity, assets/users/* directories</li>";
print "      </ul>\n";
print "</div><br />\n";
print $this->session->flashdata('message')."<br />\n";

if($tables->nbr >0)
{
    print "<div class=\"form-group col-md-8\">\n";
    print form_open(uri_string(),"id='form_1'");
    $anc_action =   form_submit('submit', "Reset Db");
    $reset_action =   form_submit('reset', "Back to Admin Db");
    print "<div class=\"form-group\"><br />\n";
    print "         <table class=\"table-hover table-condensed table-bordered\">\n";
    print "             <thead>\n";
    print "                 <tr><th>TableName</th><th>Dependencies</th></tr>\n";
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
         
        $Dependencies = "";
        $Engine = $Nbrlines = $Size = $CreatedDate = "";
        
       # print form_hidden('TableName',$TableName);
        if($Root)
        {
            print "                 <tr>\n";
            print "                     <td>$TableName</td>\n";
            foreach($listeTbls as $key =>$row)
            {
                $Table = $row['TABLE_NAME'];
                $Table_Name= $row['TableName']; 
                if(preg_match("/$TableName/",$Table) )
                {
                           $Dependencies .="<input type=hidden name=\"delete_table[]\" value='$Table' checked />$Table &nbsp; ";
                 }
            }        
            print "                 <td>$Dependencies</td>\n"; 
            print "              </tr>\n";
        }
        
    }
    
    $id_form++;
    
    print "             </tbody>\n";
    print "         </table>\n";
    print "    </div><!-- End DIV table -->\n";
    
    print "<br />\n";
    
    print "$anc_action &nbsp;$reset_action\n";
    print form_close();
   print "    </div><!-- End DIV table -->\n";
    print "<hr />\n";
 }
else
{
        print "There is no table or DataSet in your Database<br />";   
        print form_open(uri_string(),"id='form_1'",'target="_blank"');
        print form_hidden('delete_table[]','Clean');
        print form_submit('submit', "Clean directories",'target="_blank"'); 
        print form_submit('reset', "back to Admin",'target="_blank"'); 
        print form_close();
        
}
print "    </div><!-- End DIV param -->\n";
print " </div><!-- End DIV rows -->\n";
print "<!-- //////////////    End admin/remove_tables  //////////////  -->\n";
