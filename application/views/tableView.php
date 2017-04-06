<?php
/**
* The Expression Database.
*       view tableViews.php
*       display view to create subDataset
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage view
*/
?>
<!-- //////////////    tableView  //////////////  -->
<script> var column=[]</script>
<button type="button" id='closeView' class="btn btn-danger">Close</button>
<button type="button" id='saveView' class="btn btn-success">Save</button>
<button id="alert" class="btn-danger" style="display:none" disabled>Demo user can't create or modify dataset</button>
<h3> Comments </h3>
<?php
	if($comment[0]['comment'] == ""){
		echo '<p> This table do not have any comment !</p>';
	}
	else{
		$com=$comment[0]['comment'];
		echo "<p>".$com."</p>";
	}
echo "<h3> Conditions </h3>";
echo "<p> You can select conditions for clustering : </p>";
echo "<table class='table table-condensed center-table' id='conditionsTable'>";
echo "<thead>";
echo 	"<tr>";
echo        "<th>Column Name</th>";
echo        "<th style='text-align:right'>Select All :&nbsp&nbsp<input type=checkbox class=condCheck id=all value=all checked></th>";
echo     "</tr>";
echo    "</thead>";
	foreach($column as $col){
		$colname=$col['COLUMN_NAME'];
		if($colname != 'Gene_ID' && $colname != 'Gene_Name'){ 	
			echo '<tr><td>';
			echo($colname);
?><script>column.push('<?php echo $colname; ?>')</script><?php
			echo '<td><input type=checkbox class=condCheck id=check'.$colname.' value='.$colname.' checked></td>';
			echo '</td></tr>';
		}
	}	
echo "</table>";
	
?>
<script>
$(function(){
        var userGroup = "<?php print $userDemo; ?>";
        if(userGroup == "Demo")
        {
            $("#saveView").prop('disabled', true);
            $("#alert").show();            
        }
// select all checkboxes //
	$('#all').change(function(){
		if($(this).is(':checked')){
			$('.condCheck').prop('checked',true);	
		}
		else{
			$('.condCheck').prop('checked',false);	
		}
	});
// unselect //

	$('.condCheck').change(function(){
		if($(this).is(':not(:checked)')){
			$('#all').prop('checked',false);
		}
	});
// Close the Table View //
	$('#closeView').click(function(){ 
		$('#block2').fadeOut('slow') 
                $('html,body').animate({
	                scrollTop:$('body').offset().top
                },'slow');

		})

// Save the modified Table //
	$('#saveView').click(function(){
		$(this).prop('disabled', true);
		var idCol='Gene_ID'
		var nameCol='Gene_Name'
		var newColumns=[idCol,nameCol] // add gene name and id to new conditions
		var filename=$('select[name="file"]').val()
		var checked_col =0;
		for(i=0;i<column.length;i++){
			$('.condCheck:checked').each(function(){
				var check=$(this).val()
				if(check == column[i]){ 
				    checked_col++;
					newColumns.push(column[i])	// Get the checked conditions into array			
				}
			})
		}
		if(checked_col >=2)
		{
                    $.ajax({
                            url: '<?php echo base_url('display/saveTable'); ?>',
                            type: 'POST',
                            data:{ conditions: newColumns, filename:filename },
                            success: function(data) {
                                    if(data){
                                            console.log('display/saveTable:: ok')
                                            console.log(data)
                                            alert('The table '+data+' has been created !')
                                            location.reload()
                                    }
                                    else
                                    { 
                                        console.log('display/saveTable::not ok') 
                                        alert('The table '+data+' has not been created !')
                                        location.reload()
                                    }
                                    
                            }               
                    });
                }
                else
                {
                    alert ("Please select at least two columns !!");
                    $(this).prop('disabled', false);
                }
	})
});
</script>
<!-- //////////////    End tableView  //////////////  -->
