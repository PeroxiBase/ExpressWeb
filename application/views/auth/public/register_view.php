	<!-- Intro Content -->
	<div id="site_content">
		<!-- Main Content -->
				<h2>Register Account</h2>

			<?php if (! empty($message)) { ?>
				<div id="message">
					<?php echo $message; ?>
				</div>
			<?php } ?>
				
				<?php echo form_open(current_url()); ?>  	
				
					<fieldset>
						<legend>Personal Details</legend>
						<table class="login">
              <tr>
                <td class="info_req"><label for="first_name">First Name:</label></td>
                <td><input type="text" id="first_name" name="register_first_name" value="<?php echo set_value('register_first_name');?>"/></td>
              
                <td class="info_req"><label for="last_name">Last Name:</label></td>
                <td><input type="text" id="last_name" name="register_last_name" value="<?php echo set_value('register_last_name');?>"/></td>
              </tr>
						</table>
					</fieldset>
					
		<fieldset>
                        <legend>Contact Details</legend>
                        <tr>
                            <td class="info_req">
                                    <label for="phone_number">Phone Number:</label>
                                    <input type="text" id="phone_number" name="register_phone_number" value="<?php echo set_value('register_phone_number');?>"/>
                            </td>
                            <td>
                                    <label for="company"> Laboratory</label>
                                    <input type="text" id="pro_company" name="register_company" value="<?php echo set_value('register_company');?>" size="40"/>
                            </td>
                        </tr>
                    </fieldset>
					
					<fieldset>
						<legend>Login Details</legend>
						<table class="login">
						<tr>
							<td class="info_req">
								<label for="email_address">Email Address:</label></td>
							<td>
								<input type="text" id="email_address" name="register_email_address" value="<?php echo set_value('register_email_address');?>" class="tooltip_trigger"
									title="Upon registration, you will need to activate your account via clicking a link that is sent to your email address." size="30"/>
							</td>
							<td class="info_req">
								<label for="username">Username:</label></td>
							<td>
								<input type="text" id="username" name="register_username" value="<?php echo set_value('register_username');?>" class="tooltip_trigger"
									title="Set a username that can be used to login with."/>
							</td>
							</tr>
					  <tr>
							<td colspan="4">							
								<small>
									Password length must be more than <?php echo $this->flexi_auth->min_password_length(); ?> characters in length.<br/>
									Only alpha-numeric, dashes, underscores, periods and comma characters are allowed.
								</small>
							</td>
							</tr>
					  <tr>
							<td class="info_req">
								<label for="password">Password:</label></td>
							<td>
								<input type="password" id="password" name="register_password" value="<?php echo set_value('register_password');?>"/>
							</td>
							<td class="info_req">
								<label for="confirm_password">Confirm Password:</label></td>
							<td>
								<input type="password" id="confirm_password" name="register_confirm_password" value="<?php echo set_value('register_confirm_password');?>"/>
							</td>
						</tr>
						</table>
					</fieldset>
					
					<fieldset>
						<legend>Register</legend>
						<table>
						<tr>
							<td>
								<input type="submit" name="register_user" id="submit" value="Submit" class="link_button large"/>
							</td>
						</tr>
						</table>
					</fieldset>
				<?php echo form_close();?>