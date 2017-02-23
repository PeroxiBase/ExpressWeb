<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $title; ?></title>
<link type="text/css" rel='stylesheet' href="<?php echo base_url('assets/css/bootstrap-3.3.6/dist/css/bootstrap.css'); ?>"/>
<link type="text/css" rel="stylesheet" href="<?php echo base_url().'assets/js/jquery/ui/jquery-ui.css' ; ?>" /> 
<link type="text/css" rel='stylesheet' href="<?php echo base_url('/assets/css/mainStyle.css'); ?>" />  

<script type="text/javascript" src="<?php echo base_url().'assets/js/jquery-2.1.4.min.js'; ?>"></script>
<script type="text/javascript" src="<?php print base_url('');?>assets/js/jquery/ui/jquery-ui.min.js"></script> 
<script type="text/javascript" src="<?php echo base_url()?>assets/js/fancytree/lib/jquery-ui.custom.js" type="text/javascript"></script>

<link type="text/css" rel="stylesheet" href="<?php echo base_url()?>assets/js/fancytree/dist/skin-bootstrap/ui.fancytree.css"  class="skinswitcher">
<script type="text/javascript" src="<?php echo base_url()?>assets/js/fancytree/dist/jquery.fancytree-all.js" type="text/javascript"></script>

<script type="text/javascript">
$(document).ready(function() {
        // Tabs
    $('#tabs').tabs();
    // $('a[href="' + this.location.pathname + '"]').parents('li,ul').addClass('active');
    // http://stackoverflow.com/users/2702806/mirko
    // set active menu 
    $(function()
    {
        var current_page_URL = location.href;
        
        $( "a" ).each(function() 
        {
            if ($(this).attr("href") !== "#") 
            {
                var target_URL = $(this).prop("href");
        
                if (target_URL == current_page_URL) {
                    $('nav a').parents('li, ul').removeClass('active');
                    $(this).parent('li').addClass('active');
        
                    return false;
                }
            }
        });
   });
});
/* */
$('#myTabs a').click(function (e) {
  e.preventDefault()
  $(this).tab('show')
});

</script> 
</head>
<body>