<?php
/**
* The Expression Database.
*       view auth/admin/edit_group.php
*       edit group
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage views
*/
?>
<!-- //////////////    auth/admin/edit_group      //////////////  -->
<div class="row">
    <div  id="param" class="page-header col-md-4 center-block"> 
          <a href="../manage_groups">Back to Groups management</a><br /><br />
       <div class="form-group">
           <h1><?php echo lang('edit_group_heading');?></h1>
            <p><?php echo lang('edit_group_subheading');?></p>
            
            <div id="infoMessage"><?php echo $message;?></div>
            
            <?php echo form_open(current_url());?>
            
                  <p>
                        <?php echo lang('edit_group_name_label', 'group_name');?> <br />
                        <?php echo form_input($group_name);?>
                  </p>
            
                  <p>
                        <?php echo lang('edit_group_desc_label', 'description');?> <br />
                        <?php echo form_input($group_description);?>
                  </p>
            
                  <p><?php echo form_submit('submit', lang('edit_group_submit_btn'));?></p>
            
            <?php echo form_close();?>
            </div> <!--  End Div form -->
    </div><!--  End Div param -->
</div><!--  End Div row  -->

<!-- //////////////    End auth/admin/edit_group      //////////////  -->