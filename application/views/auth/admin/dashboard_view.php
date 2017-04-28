<?php
/**
* The Expression Database.
*       view auth/admin/dashboard_view.php
*       Admin dashboard
*
* Parts From codeigniter-auth
* [Ben Edmunds](http://benedmunds.com)
*
* This version drops any backwards compatibility and makes things even more
* awesome then you could expect.
*
* Documentation is located at http://benedmunds.com/ion_auth/
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage views
*/
$base_url= base_url();
?>
<!-- //////////////    auth/admin/dashboard_view      //////////////  -->
<div class="row">
        <div  id="param" class="page-header col-md-12 center-block"> 
                <h2>Users management</h2>
	
<!-- Main Content -->
<?php if (! empty($message)) { ?>
            <div id="message">
                    <?php echo $message; ?>
            </div>
<?php } ?>
				
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#tabs-1" role="tab" data-toggle="tab" aria-controls="tabs-1" >User Activity</a></li>
                <li role="presentation"><a href="#tabs-2" role="tab" data-toggle="tab" aria-controls="tabs-2" >User Accounts</a></li>
                <li role="presentation"><a href="#tabs-3" role="tab" data-toggle="tab" aria-controls="tabs-3" >User Groups</a></li>
               <!-- <li role="presentation"><a href="#tabs-4" role="tab" data-toggle="tab" aria-controls="tabs-4" >User Privileges</a></li>-->
            </ul>
					
					
            <div  class="tab-content ">
            
                <div class="tab-pane active" id="tabs-1">                        
                    <?php 
                        if(isset($whoOnline->Data)) print "<pre> ".$whoOnline->Data."</pre>";
                        if( $ActiveProcess->apache!="") print "<pre>".print_r($ActiveProcess->apache,1)." </pre>"; ?>
                        <div id=test_process></div>
                </div>
                
	        <div class="tab-pane" id="tabs-2">
                    <p>Manage the account details of all site users.</p>                     
                    <ul>
                      <li>
                        <a href="<?php echo $base_url."auth/index"; ?>">Manage User Accounts</a>			
                      </li>	
                    </ul>       
                </div>
					
                <div class="tab-pane" id="tabs-3">
                    <p>Manage the user groups that users and Dataset can be assigned to.</p>
                    <p>User groups are intended to be used to categorise the primary access rights of a user.</p>
                    <ul>
                      <li>
                        <a href="<?php echo $base_url."auth/manage_groups"; ?>">Manage User Groups</a>			
                      </li>	
                    </ul>
                </div>

                <!--
                <div class="tab-pane" id="tabs-4">
                    <p>Manage the specific user privileges that can be assigned to users.</p>
                    <p>User privileges are intended to verify whether a user has privileges to perfrom specific actions within the site. The specific action of each privilege is completely customised.</p>
                    <ul>
                      <li>
                        <a href="<?php // echo $base_url."/auth/manage_privileges"; 
                        ?>">Manage User Privileges</a>			
                      </li>	
                    </ul>
                </div>
                -->
              
     </div> <!--  End Div tab-content -->
    </div><!--  End Div param -->
</div><!--  End Div row  -->

<script type="text/javascript">
//function kill_process()
$(".Kill").click(function()
   {
       var ProcessId = $(this).attr("value");
       jQuery.ajax({ 
                type:'post',
                url:'dashboard/kill_process',
                data: 'ProcessId='+ProcessId,
                dataType:'html',
                success: function(code_html, statut)
                { 
                    // alert('Process '+ProcessId+' killed '+status+ ' mesg:'+code_html);
                    location.reload();
                },
                error: function(resultat, statut, erreur)
                { 
                   alert('Process '+ProcessId+' not killed '+status);
                    location.reload();
                }
        });
    });

$(".Qdel").click(function()
   {
       var ProcessId = $(this).attr("value");
       jQuery.ajax({ 
                type:'post',
                url:'dashboard/qdel_process',
                data: 'ProcessId='+ProcessId,
                dataType:'html',
                success: function(code_html, statut)
                { 
                      alert('Process '+ProcessId+' killed '+status+ ' mesg:'+code_html);
                    location.reload();
                },
                error: function(resultat, statut, erreur)
                { 
                   alert('Process '+ProcessId+' not killed '+status);
                    location.reload();
                }
        });
    });
$(".qstat_shrt").click(function()
   {
       var ProcessId = $(this).attr("value");
       jQuery.ajax({ 
                type:'post',
                url:'dashboard/qstat_shrt',
                data: 'ProcessId='+ProcessId,
                dataType:'html',
                success: function(code_html, statut)
                { 
                    // alert('Test Process "'+ProcessId+'" status "'+status+ '" mesg:'+code_html);
                    $('#test_process').html('<pre>'+code_html+'</pre>');
                    console.log('ProcessId='+ProcessId);
                   
                },
                error: function(resultat, statut, erreur)
                { 
                   $('#test_process').html('ERROR: Process '+ProcessId+' not killed '+status);
                    location.reload();
                }
        });
    });

$(".qstat_long").click(function()
   {
       var ProcessId = $(this).attr("value");
       jQuery.ajax({ 
                type:'post',
                url:'dashboard/qstat',
                data: 'ProcessId='+ProcessId,
                dataType:'html',
                success: function(code_html, statut)
                { 
                    // alert('Test Process "'+ProcessId+'" status "'+status+ '" mesg:'+code_html);
                    $('#test_process').html('<pre>'+code_html+'</pre>');
                    console.log('ProcessId='+ProcessId);
                   // location.reload();
                },
                error: function(resultat, statut, erreur)
                { 
                   alert('ERROR: Process '+ProcessId+' not killed '+status);
                    location.reload();
                }
        });
    });
</script>

<!-- //////////////    End auth/admin/dashboard_view      //////////////  -->