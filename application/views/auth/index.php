<div class="row">
        <div  id="param" class="col-md-6 center-block form-horizontal"> 
        <a href="../dashboard">Back to Users management</a><br />
            <h1><?php echo lang('index_heading');?></h1>
            <p><?php echo lang('index_subheading');?></p>
            
            <div id="infoMessage"><?php echo $message;?></div>
            
            <table class="table table-condensed table-hover  table-bordered">
                    <tr>
                            <th><?php echo lang('index_fname_th');?></th>
                            <th><?php echo lang('index_lname_th');?></th>
                            <th><?php echo lang('index_login_th');?></th>
                            <th><?php echo lang('index_email_th');?></th>
                            <th><?php echo lang('index_groups_th');?></th>
                            <th><?php echo lang('index_status_th');?></th>
                            <th><?php echo lang('index_action_th');?></th>
                    </tr>
                    <?php foreach ($users as $user):?>
                            <tr>
                        <td><?php echo htmlspecialchars($user->first_name,ENT_QUOTES,'UTF-8');?></td>
                        <td><?php echo htmlspecialchars($user->last_name,ENT_QUOTES,'UTF-8');?></td>
                        <td><?php echo htmlspecialchars($user->username,ENT_QUOTES,'UTF-8');?></td>
                        <td><?php echo htmlspecialchars($user->email,ENT_QUOTES,'UTF-8');?></td>
                                    <td>
                                            <?php foreach ($user->groups as $group):?>
                                                    <?php echo anchor(base_url()."auth/edit_group/".$group->id, htmlspecialchars($group->name,ENT_QUOTES,'UTF-8')) ;?><br />
                            <?php endforeach ;?>
                                    </td>
                                    <td><?php echo ($user->active) ? anchor(base_url()."auth/deactivate/".$user->id, lang('index_active_link')) : anchor(base_url()."auth/activate/". $user->id, lang('index_inactive_link'));?></td>
                                    <td><?php echo anchor(base_url()."auth/edit_user/".$user->id, 'Edit') ;?></td>
                            </tr>
                    <?php endforeach;?>
            </table>
            
            <p><?php echo anchor(base_url()."auth/create_user", lang('index_create_user_link'))?> | <?php echo anchor(base_url()."auth/create_group", lang('index_create_group_link'))?></p>
           </div>
       </div>