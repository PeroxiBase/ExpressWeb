
	<!-- Intro Content -->
	<div id="site_content">
	
	<!-- Main Content -->
            <h3>Change Email via Email Verification</h3>
            <br />
    <?php if (! empty($message)) { ?>
            <div id="message">
                    <?php echo $message; ?>
            </div>
    <?php } ?>
            
            <?php echo form_open(current_url());	?>  	
                 <label for="email_address">New Email Address:</label>
                 <input type="text" size="50" id="email_address" name="email_address" value="<?php echo set_value('email_address');?>"/>
                 <br />
                 <input type="submit" name="update_email" id="submit" value="Submit" class="link_button large"/>

            <?php echo form_close();?>