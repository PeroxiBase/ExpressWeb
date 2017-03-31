<?php
/**
* The Expression Database.
*       view waittemp.php
*       counter page for clustering
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage view
*/
?>
<style>
#wait{
	margin-top:10vh;
        text-align:center;
        font-size:20px;
}
</style>
<!-- //////////////    waittemp  //////////////  -->
<div id='wait'>
  <h2> <?php print $title; ?></h2>
  <?php 
 
        echo "  <span id='h'>$h</span>:<span id='m'>$m</span>:<span id='s'>$s</span>"; 
        
        print "<br />$message<br />\n";
        print "<span id='loop_time' style='display:none'>0</span>\n"; #
        if(isset($EndSimilarityFile))
        {
            if(file_exists("$EndSimilarityFile")) print "Similarity computing ended<br />\n";
            if(file_exists("$EndNetworkFile")) print "Network computing ended<br />\n";
        }
?>
</div>
<script type="text/javascript">
$(function(){
        
   // console.log("appendTo('#wait')");
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
        // console.log("L 38 fcy dbchiffre nb"+nb);
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
            // console.log("L68: setInterval inc s "+s);
        },1000); // on exécute ce code toute les secondes
    
    /// if EndFile detected exit. Test every 10sec
    checkEndFile=setInterval(function()
    {
        var endFile="<?php if(file_exists($EndFile)){ print 1;} else { print 0; } ?>"; 
        var qdelay="<?php print $loop_time; ?>";
        $("#loop_time").html( parseInt($("#loop_time").html())+10000);
        var loop_time=$("#loop_time").html();
        console.log("L80: checkEndFile endFile %s qdelay %s loop_time %s ",endFile,qdelay,loop_time);
        if(endFile == 1 || loop_time==qdelay)
        {
            $("#file").html("file   exist"); 
            $('#testForm').submit()
        }
        else{
            $("#file").html("file   not exist"); 
            console.log("L88: checkEndFile  qdelay %s loop_time %s ",qdelay,loop_time);
        }        
    },10000); 
  
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
<?php 
#exit;
$attributes=array('id'=>'testForm');
$this->session->set_flashdata('message', $message);
echo form_open_multipart(base_url().'visual/show',$attributes);
echo form_hidden('pid', $pid);
echo form_hidden('option', $option);
echo form_hidden('hf',$h);
echo form_hidden('mf',$m);
echo form_hidden('sf',$s);
?>

<!-- //////////////    End waittemp  //////////////  -->

