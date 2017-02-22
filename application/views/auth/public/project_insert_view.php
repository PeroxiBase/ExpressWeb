<script
	
$(document).ready(function() { 
		$('.keyup-numeric').keyup(function() {
			$('span.error-keyup-1').remove();
			var inputVal = $(this).val();
			var numericReg = /^\d*[0-9](|.\d*[0-9]|,\d*[0-9])?$/;
			if(!numericReg.test(inputVal)) {	
				$(this).after('<span class="error error-keyup-1">Numeric characters only</span>');
		}
		});
		$('.keyup-characters').keyup(function() {
			$('span.error-keyup-2').remove();
			var inputVal = $(this).val();
			var characterReg = /^\s*[a-zA-Z0-9,\s]+\s*$/;
			if(!characterReg.test(inputVal)) {	
				$(this).after('<span class="error error-keyup-2">No special characters</span>');
		}
		});
		$('.keyup-limit-8').keyup(function() {
			$('span.error-keyup-3').remove();
			var inputVal = $(this).val();
			var characterReg = /^([a-zA-Z0-9]{0,8})$/;
			if(!characterReg.test(inputVal)) {	
				$(this).after('<span class="error error-keyup-3">Maximum 8 characters</span>');
		}
		});
		$('.keyup-phone').keyup(function() {
			$('span.error-keyup-4').remove();
			var inputVal = $(this).val();
			if(inputVal == ""){
				$('span.error-keyup-4').remove();
			}
			var characterReg = /^[2-9]\d{2}-\d{3}-\d{4}$/;
			if(!characterReg.test(inputVal)) {	
				$(this).after('<span class="error error-keyup-4">Format xxx-xxx-xxxx</span>');
		}
		});
		$('.keyup-date').keyup(function() {
			$('span.error-keyup-5').remove();
			var inputVal = $(this).val();
			var dateReg = /^[0,1]?\d{1}\/(([0-2]?\d{1})|([3][0,1]{1}))\/(([1]{1}[9]{1}[9]{1}\d{1})|([2-9]{1}\d{3}))$/;
			if(!dateReg.test(inputVal)) {	
				$(this).after('<span class="error error-keyup-5">Invalid date format</span>');
		}
		});
		$('.keyup-fake').keyup(function() {
			$('span.error-keyup-6').remove();
			var inputVal = $(this).val();
			var fakeReg = /(.)\1{2,}/;
			if(fakeReg.test(inputVal)) {	
				$(this).after('<span class="error error-keyup-6">Invalid Text</span>');
			}
		});
		$('.keyup-email').keyup(function() {
			$('span.error-keyup-7').remove();
			var inputVal = $(this).val();
			var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
			if(!emailReg.test(inputVal)) {	
				$(this).after('<span class="error error-keyup-7">Invalid Email Format</span>');
			}
		});
		$('.keyup-email-2').keyup(function() {
			$('span.error-keyup-8').remove();
			var inputVal = $(this).val();
			var emailFreeReg = /^([\w-\.]+@(?!gmail.com)(?!yahoo.com)(?!hotmail.com)([\w-]+\.)+[\w-]{2,4})?$/;
			if(!emailFreeReg.test(inputVal)) {	
				$(this).after('<span class="error error-keyup-8">No Free Email Addresses</span>');
			}
		});
		$('.keyup-cc').keyup(function() {
			$('span.error-keyup-9').remove();
			var inputVal = $(this).val();
			var ccReg = /^4[0-9]{12}(?:[0-9]{3})?$/;
			if(!ccReg.test(inputVal)) {	
				$(this).after('<span class="error error-keyup-9">Invalid visa card number</span>');
			}
		});
		$('#submit').click(function() {
			if($('span.error').length > 0){
				alert('Errors!');
				return false;
			} else {
				$("#btn-submit").after('<span class="error">Form Accepted</span>');
				return false;
			}
	});	
});
</script>

<div class="row">
    <div  id="param" class="col-md-8 center-block form-horizontal"> 
    
	<!-- Main Content -->
            <h2>Insert New Project</h2>
            <a href="<?php echo base_url();?>auth_public/manage_project">Manage Project</a>

    <?php if (! empty($message)) { ?>
            <div id="message">
                    <?php echo $message; 
                    echo "<br>".print_r($debug,1)."";
                    ?>
            </div>
    <?php } 
    
     $UserName= set_value('insert_creator');
     if($UserName =="") $UserName= $this->session->username;
    $dateY= set_value('insert_start_Y');
     if($dateY =="") $dateY= date('Y');
    $dateM= set_value('insert_start_M'); 
     if($dateM =="") $dateM= date('m');
    $dateD= set_value('insert_start_D');
     if($dateD =="") $dateD= date('d');
    $comment = set_value('insert_project_comment');
    ?>
            
    <?php echo form_open(current_url(),'id="form-sign-up" ');	?>
                                                    
        <fieldset>
            <legend>Project Details</legend>
            <table>
                <tr>
                    <td>
                        <label for="project_name" class="col-xs-4">Project Name:</label>
                    </td>
                    <td>
                        <input type="text"  id="project_name" name="insert_project_name" value="<?php echo set_value('insert_project_name');?>" size="30" required />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="creator" class="col-xs-4">Creator:</label>
                    </td>
                    <td>
                        <input type="text" id="createdit_useror" name="insert_creator" value="<?php 
                        echo $UserName; ?>" size=30/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="creator" class="col-xs-4">Comment:</label>
                    </td>
                    <td>
                        <textarea  class="col-xs-8" id="comment" name="insert_project_comment" cols=60 rows=5  required ><?php echo $comment;?></textarea>
                    </td>
                </tr>
                
                <tr>
                    <td>
                        <label for="start" class="col-xs-4">Start (Y-M-D):</label>
                    </td>
                    <td>
                        <input type="text" id="startY" name="insert_start_Y" value="<?php echo $dateY;?>" size=4/> -
                        <input type="text" id="startM" name="insert_start_M" value="<?php  echo $dateM; ?>" size=2/> -
                        <input type="text" id="startD" name="insert_start_D" value="<?php  echo $dateD; ?>" size=2/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="end">End (Y-M-D):</label>
                    </td>
                    <td>
                        <input type="text" id="endY" name="insert_end_Y" value="<?php echo set_value('insert_end_Y');?>" size=4/  required > -
                        <input type="text" id="endM" name="insert_end_M" value="<?php echo set_value('insert_end_M');?>" size=2/ required > -
                        <input type="text" id="endD" name="insert_end_D" value="<?php echo set_value('insert_end_D');?>" size=2/ required >
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="visible">Visible:</label>
                    </td>
                    <td>
                        <input type="checkbox" id="visible" name="insert_visible" value="<?php echo set_value('insert_visible');?>"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="shared">Shared:</label>
                    </td>
                    <td>
                        <input type="checkbox" id="shared" name="insert_shared" value="<?php echo set_value('insert_shared');?>"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="creator">Member:</label>
                    </td>
                    <td>
                        <input type="text" id="creator" name="insert_members" value="<?php echo set_value('insert_members');?>"/>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <hr/>
                        <input type="submit" name="insert_project_submit" id="submit" value="Create Project" class="link_button large"/>
                        <input type="reset" name="reset_project" id="reset" value="Reset" class="link_button large"/>
                    </td>
                </tr>
            </table>
        </fieldset>
        <?php echo form_close();?>
        
        <script type="text/javascript">
        $(document).ready(function() {
    $("input:reset").click(function() {       // apply to reset button's click event
        this.form.reset();                    // reset the form

        // call your functions to be executed after the reset      

         return false;                         // prevent reset button from resetting again
    });
});
        </script>
</div>
</div>