<?php
/**
* The Expression Database.
*       view auth/public/email_update.php
*       email_update form
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage views
*/
?>
<!-- //////////////    auth/public/email_update      //////////////  -->
 
<div  id="param" class="col-md-8 center-block"><br />
<a href="update_account">Back to Profile management</a><br />
<div class="form-group">
    <h3>Change Email via Email Verification</h3>
    <br />
    <?php if (! empty($message)) { ?>
    <div id="message"><?php echo $message; ?></div>
    <?php } ?>
    
    <?php echo form_open(current_url());	?>
    <div class="form-group">
        <label for="email_address">New Email Address:</label>
        <input type="text" size="50" id="email_address" name="email_address" value="<?php echo set_value('email_address');?>"/>
        <br />
        <input type="submit" name="update_email" id="submit" value="Submit" class="link_button large"/>
    </div>
    <?php echo form_close();?>

</div><!--  End Div param -->
</div><!--  End Div param -->
<!-- //////////////    auth/public/email_update      //////////////  -->