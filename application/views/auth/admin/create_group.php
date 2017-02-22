 <div class="row">
    <div  id="param" class="col-md-4 center-block form-horizontal"> 
        <a href="manage_groups">Back to Groups management</a><br /><br />
       <div class="form-group">
                <h1><?php echo lang('create_group_heading');?></h1>
                <p><?php echo lang('create_group_subheading');?></p>
                
                <div id="infoMessage"><?php echo $message;?></div>
                
                <?php echo form_open("auth/create_group");?>
                
                      <p>
                            <?php echo lang('create_group_name_label', 'group_name');?> <br />
                            <?php echo form_input($group_name);?>
                      </p>
                
                      <p>
                            <?php echo lang('create_group_desc_label', 'description');?> <br />
                            <?php echo form_input($description);?>
                      </p>
                
                      <p><?php echo form_submit('submit', lang('create_group_submit_btn'));?></p>
                
                <?php echo form_close();?>
            </div>
    </div>
</div>