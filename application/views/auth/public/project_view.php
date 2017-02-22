<div class="row">
    <!-- Intro Content -->
    <div id="param" class="col-md-8 center-block ">
        <div class="col100">
            <h3>Manage Project</h3>
            <p>Create project , define pipeline , process and recover data</p>
        </div>		
     
    
    <!-- Main Content -->
                            
    <a href="<?php echo base_url();?>auth_public/insert_project">Insert New Project</a><br /><br />
    
    <?php if (! empty($message)) { ?>
    <div id="message">
        <?php echo $message; ?>
    </div>
    <?php } ?>
    
    <?php echo form_open(current_url());
                if (!empty($projects)) {
                 // var_dump($projects);
                        foreach ($projects as $project) {
                    ?>
            <table class="table">
                <thead>
                    <tr>
                        <th class="spacer_150 tooltip_trigger"
                                title="An alias to reference the address by.">
                                Id
                        </th>
                        <th>Name</th>
                        <th>Comment</th>
                        <th>Creator</th>
                        <th>Created</th>
                        <th>Use</th>
                        <th class="spacer_100 align_ctr tooltip_trigger" 
                                title="If checked, the row will be deleted upon the form being updated.">
                                Delete
                        </th>
                    </tr>
                </thead>
                    
                    <tbody>
                        <tr>
                            <td>
                                    <a href="<?php echo base_url();?>auth_public/update_project/<?php echo $project['uprj_id'];?>/">
                                    <?php echo $project['uprj_id'];?></a>
                            </td>
                            <?php
                             if( $project['uprj_working']==1) 
                             {
                               ?>
                            <th><?php echo anchor(base_url()."auth_public/view_project/".$project['uprj_id']."",$project['uprj_project_name']);?></th>
                            <?php
                             }
                             else 
                             {
                               ?>
                            <td><?php echo $project['uprj_project_name'];?></td>
                            <?php
                             }
                             ?>
                             <td><?php echo $project['uprj_project_comment'];?></td>
                             <td><?php echo $project['username'];?></td>
                            <td><?php echo $project['uprj_project_date_start'];?></td>
                            <td class="align_ctr">
                            <?php 
                            $RefProject=$project['uprj_id'].'|'.$project['uprj_project_name'];
                            $use_project=array('name' => "working_project",'id' => 'use_project','value' => $RefProject, 'checked' =>$project['uprj_working'],
                              'class'=>"tooltip_trigger",'title' => 'Working Project');
                            echo  form_radio($use_project);
                            ?>
                            <!--	<input type="radio" name="use_project" value="<?php echo $project['uprj_id'];?>"/>-->
                            </td>
                            <td class="align_ctr">
                                    <input type="checkbox" name="delete_project[<?php echo $project['uprj_id'];?>]" value="1"/>
                            </td>
                        </tr>
                    </tbody>
                    <?php } ?>
                    <tfoot>
                        <tr>
                            <td colspan="7">
                                <input type="submit" name="delete" value="Delete Checked Project" class="link_button large"/>&nbsp;&nbsp;
                                <input type="submit" name="working" value="Define as Working Project" class="link_button large"/>
                                <input type="hidden" name="update_username" value="<?php echo $project['username'];?>"/>
    
                            </td>
                        </tr>
                    </tfoot>
                    <?php } else { ?>
                    <tbody>
                        <tr>
                            <td colspan="7">
                                <p>There are no project in your address book</p>
                            </td>
                        </tr>
                    </tbody>
                    <?php } ?>
            </table>
    <?php echo form_close();?>
    </div>
</div>