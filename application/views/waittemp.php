<style>
#wait{
	margin-top:10vh;
        text-align:center;
        font-size:20px;
}
</style>
<div id='wait'>
  <h2> <?php print $title; ?></h2>
 <?php 
 
 echo "  <span id='h'>$h</span>:<span id='m'>$m</span>:<span id='s'>$s</span>"; 
        if(isset($EndSimilarityFile))
        {
            print "<br />computing ...  <br />\n";
            
            /*$dir = exec("ls -l $work_cluster/scripts/",$r3);
            print "<hr />Content directory: $work_cluster/scripts<pre>".print_r($r3,1)."</pre>";
            
            print "<pre>";
            system("ps -ef |grep launch",$r)."<br />\n";
            system("ps -ef |grep execute",$r2)."<br /></pre>";*/
            
            if(file_exists("$EndSimilarityFile")) print "Similarity computing ended<br />\n";
            if(file_exists("$EndNetworkFile")) print "Network computing ended<br />\n";
        }
?>
</div>
<script>
$(function(){
$('#clustate').appendTo('#wait');

var h=$('#h').text()
var m=$('#m').text()
var s=$('#s').text()
var time
function dchiffre(nb)
{
    if(nb < 10) // si le chiffre indiqué est inférieurs à dix ...
    {
        nb = "0"+nb; // .. on ajoute un zéro devant avant affichage
    }
     
    return nb;
}
temps = setInterval(function()
        {
            s++; // On incrémente le nombre de seconde 
            if(s > 59) // Si s est supérieur à 59 ..
            {
                m++; // .. On incrémente le nombre de minute
                s = 0; // et on affecte 0 a notre variable seconde
            }
             
            if(m > 59) // Si m est supérieur à 59 ..
            {
                h++; // .. On incrémente le nombre d'heure
                m = 0; // et on affecte 0 à notre variable minute
            }
             
                        // On affiche le nombre de seconde dans le span s
            $("#s").html(dchiffre(s)); 
            $('input[name=sf]').attr('value',($('#s').text())); 
             
                        // On affiche le nombre de minute dans le span m
	    $("#m").html(m); 
            $('input[name=mf]').attr('value',($('#m').text())); 
 
                        // On affiche le nombre d'heures dans le span h
            $("#h").html(h);
            $('input[name=sf]').attr('value',($('#s').text())); 
             
        },1000); // on exécute ce code toute les secondes
});
</script>
<style>
html,body{
	background-color:white;	
}
h1{
	color:#293E6A;
}
</style>
<script>
 $(function(){
        $('#testForm').submit()
});
</script>
<?php 
$attributes=array('id'=>'testForm');
echo form_open_multipart('visual/show',$attributes);
echo form_hidden('pid', $pid);
echo form_hidden('option', $option);
echo form_hidden('hf',$h);
echo form_hidden('mf',$m);
echo form_hidden('sf',$s);
?>



