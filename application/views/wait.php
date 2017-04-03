<?php
/**
* The Expression Database.
*       view wait.php
*       use by Visual/load pre-launch job on cluster
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage view
*/
?>
<!-- //////////////    wait  //////////////  -->
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
        
        print "<br />$message<br />\n";
?>
</div>
<script type="text/javascript">
$(function(){
        
    //console.log("appendTo('#wait')");
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
    
    var qdelay="<?php print $loop_time; ?>";
    console.log("L68: checkEndFile   qdelay %s  ",qdelay);
    checkEndFile=setInterval(function()
    {
        $('#testForm').submit()
    },qdelay); 
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
<!-- //////////////    End wait  //////////////  -->
