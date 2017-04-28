<?php
/**
* The Expression Database.
*       view main.php
* allow user to select and run experimental data.
* If threshold values exist, results are displayed. Otherwise, cluster job is launched
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package expressionWeb
*/
?>
<!-- //////////////    main      //////////////  --> 
<script type="text/javascript">
// Slider //
function outputUpdate1(vol) 
{
    document.querySelector('#showRange').value = vol;
}
function outputUpdate2(vol)
{
    document.querySelector('#showCor').value = vol;
}
$(function()
{
    $(document).scrollTop( $("#param").offset().top );
    $("input[name='geneSelect']").removeAttr('disabled')

// Fonction pour ajouter dynamiquement un input
    $("input[name='geneSelect']:checkbox").change(function()
    {
        if($(this).is(":checked"))
        {
            $('#geneS').fadeIn('slow');
            $('#toggle1').css('background-color','lightgray');
        }
        else
        {
            $('#geneS').fadeOut('slow');
            $('#toggle1').css('background-color','white');
        }
    });
// 
    $(".filelink").on('click',function()
    {
        $(".filelink").css({'color':'black'});
        $(this).css({'color':'green'});
        var f=$(this).text()
        $("input[name='filprintoser']").attr('value',f);
    });
/////////////////////////////////////////////////////////////
    // AJAX LOAD TABLE VIEW //

    $('#showT').click(function()
    {
        var seuil=$('#cluS').val()
        var file=$('select[name="file"]').val()
        $('#block2').fadeIn('slow');
        $.ajax(
        {
            url: '<?php print base_url('display/showTable'); ?>',
            type: 'POST',
            data:{ seuil:seuil,file:file },
            success: function(data)
                  {
                    if(data)
                    $("#block2").html(data);
                    $('html,body').animate({
                            scrollTop:$('#block2').offset().top -30
                    },'slow');
                  }		
        });
    });
    $('#run').click(function()
    {
        $(this).prop('disabled', true);
        var file=$('select[name="file"]').val()
        var clusterSeuil=$('#cluS').val()
                
       /* */ $.ajax(
        {
            url:'<?php print base_url('visual/load'); ?>',
            data:{
                    file:file,
                    clusterSeuil:clusterSeuil	
            },
            type:'POST',
            success:function(data){
                    if(data)
                    $('#block1').html(data);
                    $('#block2').remove();
            }
        });
       
        console.log("run with threshold"+ clusterSeuil);
    });
});
//// on change data_set show previous calculated results
// Add check for users in Demo groups to avoid launching jobs
// and create sub_tables from existing Datasets
// On Dataset selection change Threshold value and show or hide Run button
$(document).ready(function()
{
    $("#SelectBase").change(function()
    {
        $(this).find("option:selected").each(function()
        {
            var optionValue = $(this).prop("value");
            /////  check if user belong to Demo group
            var user_group = "<?php print $userDemo; ?>";
            console.log('user_group "'+ user_group+'"')
            if(optionValue)
            {
                $(".box").not("." + optionValue).hide();
                $("." + optionValue).show();
                 var thresh = $("." + optionValue).text();
                 /// check in hidden div generated by CTRL Visual/init_user
                 /// if one of our available Dataset to Demo users isFinite not yet computed
                 if(thresh != "dataset have not been computed")
                 {
                     // test available threshold for selected Dataset
                     // keep the last threshold....
                     thresh = thresh.replace('pre-calculated threshold: ','');
                     thresh = thresh.trim();
                     var threshLen = thresh.length;
                     /// should not occurs ... with test thresh != "dataset have not been computed"
                     if( threshLen == 0 && user_group == "Demo")
                     {
                         /// dataset not yet compute!. set Range to 0.9 and disabled Run button
                         $('#cluS').val("0.9");
                         $('#showRange').val("0.9");
                         $('#run').hide();
                     }
                     if( (threshLen>0 && threshLen<5 )&& user_group == "Demo")
                     {
                         $('#cluS').val(thresh);
                         $('#showRange').val(thresh);
                         $('#run').show();
                         console.log('input.clusterSeuil "'+ thresh+'"')
                     }
                     ///  multiple values. Keep the last one
                     if(threshLen >5 && user_group == "Demo")
                     {
                         thresh = thresh.split("  ");
                        console.log('value array "'+ thresh[0]+'"')
                         var new_thresh = thresh.pop(); 
                         $('#cluS').val(new_thresh);
                         $('#showRange').val(new_thresh);
                         $('#run').show();
                         console.log('input.clusterSeuil "'+ new_thresh+'"')
                     }
                 }
                 else
                 {
                     if(user_group == "Demo")
                     {
                         $("." + optionValue).text("Has Demo user, you can't launch cluster job\n on raw Dataset");
                         $('#cluS').val("0.9");
                         $('#showRange').val("0.9");
                         $('#run').hide();
                     }
                 }
                  console.log('value "'+ thresh+'"  len '+threshLen)
            }
            else
            {
                $(".box").hide();
            }
        });

    }).change();                   
});
</script>
<?php
print $this->session->flashdata('message')."<br />\n";

$attributes=array('id'=>'formParam','target'=>"_blank",'class'=>"form-horizontal");
print form_open_multipart('visual/load',$attributes);

print "<div id=\"param\" class=\"row\">\n";
print "     <div class=\"col-md-10\" id=\"formTitle\">\n";
print "          <h2>Clustering Parameters..  </h2>\n";
print "          <p> Here you can choose the table you want to work with and set the parameters for clustering.</p>\n";
print "     </div>\n";

print "     <div class=\"launch form-group col-md-8 \">\n";

        $attLabel=array('title'=>'Choose the table you want to work with');
        print form_label('Choose a table : ','',$attLabel)."\n";
        $options=array();
        foreach ($tables->result as $row)
        {
                $options[$row['TableName']]=$row['TableName'];
        } 
        $attCtrl=array('class'=>'form-control','id' =>'SelectBase');
                print form_dropdown('file',$options,'large',$attCtrl)."\n";
            
           
        $options=array('class'=>'btn btn-info btn-md','id'=>'showT');
        print form_button('showT','Show table',$options)."\n";
        ######### display previous calculated threshold #############
        print $option_div;
print "         </div>\n"; # End Div launch

#print "     </div>\n";
print "         <div class=\"row\">\n";
print "                 <div class=\"launch form-group col-md-8   \">\n";
                $options=array('id'=>'clusLabel','title'=>'Set a Threshold in order to build clusters');
                print form_label('Clustering threshold : ','cluS',$options)."\n";
?>
                            <input type="range" name="clusterSeuil" id="cluS" value="0.9" min="0" max="1" step="0.05" oninput="outputUpdate1(value)" style="width:80px">
                            <output for="clusterSeuil" id="showRange" >0.9</output>
                        </div>
</div>
         <div class="row">
<?php
#print "             <div class=\"launch form-group col-md-4 \">\n";
print "                 <button type=\"button\" class=\"btn btn-success\" id=\"run\">Run</button>\n";
print "                         ".form_hidden('pid', $pid)."\n";
print "             </div>\n";
print "    </div>  <!-- END DIV row -->\n";   
print form_close(); 
print "</div> <!-- END DIV id=param -->\n";   
?>	
      

<?php
$path=base_url();

?>
<script>
$(function(){
var userGroup='<?php print $userDemo; ?>';
if(userGroup == 'Demo'){
   var valCl= $('#cluS').val();
	console.log($('#cluS'+valCl))
	
	$('#cluS').attr('disabled', true);
	$('#cluS').css('cursor', 'not-allowed');
}
});
// change threshold selector value by click on previous calculated threshold
$('.seuil_val').on('click', function() 
    {
    var value=$(this).text();
     
    $('#cluS').val(value);
     $('#showRange').val(value);
});
</script>

<div class="container-fluid" id='block2'></div>

<!-- //////////////    End main      //////////////  --> 

