<?php
$base_url= base_url();
?>

<!-- Intro Content -->
<div class="row">
        <div  id="param" class="col-md-10 center-block"> 
                <h2>Admin: Dashboard</h2>
	
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
                <li role="presentation"><a href="#tabs-4" role="tab" data-toggle="tab" aria-controls="tabs-4" >User Privileges</a></li>
            </ul>
					
					
            <div  class="tab-content ">
            
                <div class="tab-pane active" id="tabs-1">
                    <?php print "<pre>".print_r($whoOnline->Data,1)."  ".print_r($ActiveProcess->apache,1)." </pre>"; ?>
                </div>
                
	        <div class="tab-pane  " id="tabs-2">
                    <p>Manage the account details of all site users.</p>                     
                    <ul>
                      <li>
                        <a href="<?php echo $base_url."auth/index"; ?>">Manage User Accounts</a>			
                      </li>	
                    </ul>       
                </div>
					
                <div class="tab-pane" id="tabs-3">
                    <p>Manage the user groups that users can be assigned to.</p>
                    <p>User groups are intended to be used to categorise the primary access rights of a user, if required, more specific privileges can then be assigned to a user using the 'User Privileges' below. User groups are completely customised.</p>
                    <ul>
                      <li>
                        <a href="<?php echo $base_url."auth/manage_groups"; ?>">Manage User Groups</a>			
                      </li>	
                    </ul>
                </div>
					
                <div class="tab-pane" id="tabs-4">
                    <p>Manage the specific user privileges that can be assigned to users.</p>
                    <p>User privileges are intended to verify whether a user has privileges to perfrom specific actions within the site. The specific action of each privilege is completely customised.</p>
                    <ul>
                      <li>
                        <a href="<?php echo $base_url."/auth/manage_privileges"; ?>">Manage User Privileges</a>			
                      </li>	
                    </ul>
                </div>
					
              
      </div>
   </div>
</div>
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
                { // success est toujours en place, bien sûr !
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
                { // success est toujours en place, bien sûr !
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
</script>