<!DOCTYPE html>
<html>
<head>
<title><?php echo $title; ?></title>
 
<link type="text/css" rel='stylesheet' href="<?php echo base_url('assets/css/bootstrap-3.3.6/dist/css/bootstrap.css'); ?>"/>
<link type="text/css" rel='stylesheet' href="<?php echo base_url('/assets/css/mainStyle.css'); ?>" />  

<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Arvo" />
<script src="<?php echo base_url().'assets/js/jquery-2.1.4.min.js'; ?>"></script>
<script type="text/javascript" src="<?php print base_url('');?>assets/js/jquery/ui/jquery-ui.min.js"></script> 
<script type="text/javascript" src="<?php echo base_url().'assets/js/jquery.stellar.min.js'; ?>"></script>
<script type="text/javascript">
$(window).load(function() {
		$(".loader").fadeOut("slow");
		});
/*$.stellar({
    responsive: true,
    horizontalScrolling: false,
    scrollProperty: 'scroll',
});*/
function updateTextInput(val) {
      document.getElementById('textInput').value=val; 
    }
$(function(){
	$(".filelink").on('click',function(){
		$(".filelink").css({'color':'black'});
		$(this).css({'color':'green'});
		var f=$(this).text()
		$("input[name='filechooser']").attr('value',f);
	});
	$('input[name="geneSelect"]').attr("disabled", true);
	$('input[name="doubleclus"]').attr("disabled", true);
	$('#textInput').attr("disabled", true);
});
    $(document).ready(function() {
            // Tabs
        $('#tabs').tabs();
        // $('a[href="' + this.location.pathname + '"]').parents('li,ul').addClass('active');
        // http://stackoverflow.com/users/2702806/mirko
        // set active menu 
         $(function(){
        var current_page_URL = location.href;

        $( "a" ).each(function() {

            if ($(this).attr("href") !== "#") {

                var target_URL = $(this).prop("href");

                    if (target_URL == current_page_URL) {
                        $('.nav a').parents('li, ul').removeClass('active');
                        $(this).parent('li').addClass('active');

                        return false;
                    }
            }
        }); });
    /* */
    $('#myTabs a').click(function (e) {
      e.preventDefault()
      $(this).tab('show')
    
    });
    
    });
    </script>
</head>
<body>
