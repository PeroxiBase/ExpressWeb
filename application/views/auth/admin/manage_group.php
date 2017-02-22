<div class="row">
    <div  id="param" class="col-md-4 center-block form-horizontal"> 
       <a href="../dashboard">Back to Users Account management</a><br /><br />
       <div class="form-group">
                <h1><?php echo lang('list_group_heading');?></h1>
                <p><?php echo lang('list_group_subheading');?></p>
                
                <div id="infoMessage"><?php echo $message;?></div>
                
                <table class="table table-bordered table-condensed">
                        <tr>
                                <th><?php echo lang('index_groups_th');?></th>
                                <th><?php echo lang('edit_group_desc_label');?></th>
                                <th><?php echo lang('index_action_th');?></th>
                        </tr>
                        <?php foreach ($groups as $group):?>
                                <tr>
                            <td><?php echo htmlspecialchars($group->name,ENT_QUOTES,'UTF-8');?></td>
                            <td><?php echo htmlspecialchars($group->description,ENT_QUOTES,'UTF-8');?></td>
                            <td><?php echo anchor(base_url()."auth/edit_group/".$group->id, 'Edit') ;?></td>
                                </tr>
                        <?php endforeach;?>
                </table>
                
                <p><?php echo anchor(base_url()."auth/create_group", lang('index_create_group_link'))?></p>
            </div>
    </div>
</div>