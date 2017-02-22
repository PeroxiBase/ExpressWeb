<div class="row">
        <div  id="param" class="col-md-10 center-block form-horizontal"> 
        <style>
        th.rotate {
  /* Something you can count on */
  height: 200px;
  white-space: nowrap;
}

th.rotate > div {
  transform: 
    /* Magic Numbers 
    translate(5px, 1px)*/
    /* 45 is really 360 - 45 */
    rotate(315deg);
  width: 50px;
}
th.rotate > div > span {
  border-bottom: 1px solid #ccc;
  padding: 5px 10px;
}
</style>
   <?php     
       print " <h3>Detail for $table_name data</h3>\n";
       print "Table $table_name contain :<br />\n";
       print "<ul>\n";
       print "  <li>".count($column)." columns</li>\n";
       print "  <li>$size lines</li>\n";
       print "  <li>Comment: ".$comment[0]['comment']."</li>\n";
       print "</ul>\n";
       
       print "<table class=\"table table-bordered table-condenced\">\n";
       print "  <thead>\n";
       print "     <tr>\n";
       foreach($column as $col)
       {
           $colname=$col['COLUMN_NAME'];
           print "<th class=\"rotate\"><div><span>$colname</span></div></th>";
	}
	print "</tr>\n";
       print "<thead>\n";
       print "<tbody>\n";
       
       $prev_key = "";
       print "     <tr>\n";
       foreach($detail as$key=>$val)
       {
           $i=1;
            print "     <tr>\n";
           foreach($val as $key2=>$value)
           { 
               if($i==1 OR $i==2) print "<td>$value</td>";
               else print "<td>".round($value,3)."</td>";
               $i++;
           }
           print "</tr>\n";
       }
        print "</tbody>\n";
        print "</table>\n";
   ?>
        
        </div>
</div>      
