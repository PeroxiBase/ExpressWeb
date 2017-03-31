<?php
/**
* The Expression Database.
*       view auth/admin/deactivate_user.php
*       deactivate user
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage views
*/
?>
<!-- //////////////    auth/admin/deactivate_user      //////////////  -->
<h1><?php echo lang('deactivate_heading');?></h1>
<p><?php echo sprintf(lang('deactivate_subheading'), $user->username);?></p>

<?php echo form_open("auth/deactivate/".$user->id);?>

  <p>
  	<?php echo lang('deactivate_confirm_y_label', 'confirm');?>
    <input type="radio" name="confirm" value="yes" checked="checked" />
    <?php echo lang('deactivate_confirm_n_label', 'confirm');?>
    <input type="radio" name="confirm" value="no" />
  </p>

  <?php echo form_hidden($csrf); ?>
  <?php echo form_hidden(array('id'=>$user->id)); ?>

  <p><?php echo form_submit('submit', lang('deactivate_submit_btn'));?></p>

<?php echo form_close();?>
<!-- //////////////    End auth/admin/deactivate_user      //////////////  -->