<?php

    echo '<html><head>';
    echo "<META http-EQUIV=\"Refresh\" CONTENT=$loop_time; url=\"visual/show/$pid\">";
    echo "</head><body>Please wait.. computing Cluster $pid reload page in $loop_time ...";
  
    if(isset($command))
    {
        exec("$command >>$EndFile &");
        
    }
        if(file_exists("$EndSimilarityFile")) print "Similiraty computing ended<br />\n";
        if(file_exists("$EndNetworkFile")) print "Network computing ended<br />\n";
 

?>
