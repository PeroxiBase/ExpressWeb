
	<!-- Intro Content -->
	<div id="site_content">
			
	<!-- Main Content -->
				<h2>Update Project</h2>
				<a href="<?php echo base_url();?>auth_public/manage_project">Manage Project</a>

			<?php if (! empty($message)) { ?>
				<div id="message">
					<?php echo $message; ?>
				</div>
			<?php } 
			
			//var_dump($project);
			?>
				
				<?php echo form_open(current_url());	?>  	
				
					<fieldset>
									
						<legend>Project Details</legend>
						<ul>
							<li class="info_req">
								<label for="project_name">Project Name:</label>
								<input type="text" id="project_name" name="update_project_name" value="<?php echo set_value('update_project_name',$project['uprj_project_name']);?>" size="30"
								class="tooltip_trigger"	title="." />
							</li>
							<li class="info_req">
								<label for="creator">Creator:</label>
								<input type="text" id="creator" name="update_creator_name" value="<?php echo set_value('update_creator_name',$project['uacc_username']);?>" size="30" class="tooltip_trigger"	title="." />
								<input type="hidden" id="creator" name="update_creator" value="<?php echo set_value('update_creator',$project['uprj_uacc_fk']);?>" size="30" class="tooltip_trigger"	title="." />
							</li>
							<li class="info_req">
								<label for="creator">Comment:</label>
								<textarea id="comment" name="update_project_comment" cols=60 rows=5><?php echo set_value('update_project_comment',$project['uprj_project_comment']);?></textarea>
							</li>
						<li class="info_req">
								<label for="start">Start (Y-M-D):</label>
								<input type="text" id="start" name="update_date_start" value="<?php  echo set_value('update_date_start',$project['uprj_project_date_start']);?>" size="20" class="tooltip_trigger"	title="." />
							</li>
							<li class="info_req">
								<label for="end">End (Y-M-D):</label>
								<input type="text" id="end" name="update_date_end" value="<?php echo set_value('update_date_end',$project['uprj_project_date_end']);?>" size="20" class="tooltip_trigger"	title="." /> 
							</li>
							<li class="info_req">
								<label for="visible">Visible:</label>
								<?php 
								$visible=array('name' => 'update_visible','id' => 'visible','value' =>'1', 'checked' =>$project['uprj_project_visibility'],'class'=>"tooltip_trigger",'title' => 'Project public');
								echo  form_checkbox($visible);
								?>
							</li>
							<li class="info_req">
								<label for="shared">Shared:</label>
								<?php 
								$shared=array('name' => "update_shared",'id' => "shared",'value' =>'1', 'checked' =>$project['uprj_project_shared'],'class'=>"tooltip_trigger",'title' => 'Project shared with other members');
								echo  form_checkbox($shared);
								?>
								</li>
						<!--	<li class="info_req">
								<label for="creator">Member:</label>
								<input type="text" id="creator" name="update_members" value="<?php echo set_value('update_members',$project['']);?>" class="tooltip_trigger"	title="." />
							</li> -->
						
						<ul>
							
							<li>
								<hr/>
								<label for="submit">Update Address:</label>
								<input type="submit" name="update_project" id="submit" value="Submit" class="link_button large"/>
								<input type="hidden" name="update_project_id" value="<?php echo $project['uprj_id'];?>"/>
							</li>
						</ul>
					</fieldset>
				<?php echo form_close();?>