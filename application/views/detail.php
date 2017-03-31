<?php
/**
* The Expression Database.
*       view detail.php
*       display informations about selected Dataset (Ctrl Display)
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage view
*/
?>
<!-- //////////////    detail      //////////////  -->
<div class="row">
        <div  id="param" class="col-md-10 center-block form-horizontal"> 
      
   <?php     
        print "         <h3>Detail for $table_name data</h3>\n";
        print "         Table $table_name contain :<br />\n";
        print "         <ul>\n";
        print "           <li>".count($column)." columns</li>\n";
        print "           <li>$size lines</li>\n";
        print "           <li>Comment: ".$comment[0]['comment']."</li>\n";
        print "         </ul>\n";
        
        print "         <table class=\"table   table-condenced\">\n";
        print "           <thead>\n";
        print "              <tr>\n";
        foreach($column as $col)
        {
           $colname=$col['COLUMN_NAME'];
           print "                   <th class=\"rotate\"><div><span>$colname</span></div></th>\n";
        }
        print "             </tr>\n";
        print "           </thead>\n";
        print "           <tbody>\n";
       
       $prev_key = "";
        
        foreach($detail as$key=>$val)
        {
           $i=1;
            print "             <tr>\n               ";
           foreach($val as $key2=>$value)
           { 
               if($i==1 OR $i==2) print "<td>$value</td>";
               else print "<td>".round($value,3)."</td>";
               $i++;
           }
          print "\n             </tr>\n";
        }
        print "           </tbody>\n";
        print "         </table>\n";
   ?>
        
        </div>
</div><!-- End DIV row -->
<!-- //////////////    End detail      //////////////  -->
