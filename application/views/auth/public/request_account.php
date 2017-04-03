<?php
/**
* The Expression Database.
*       view auth/public/request_account.php
*       request_account form
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage views
*/
?>
<!-- //////////////    auth/public/request_account      //////////////  -->
<div  id="param" class="col-md-8 center-block">
<?php 
#print "<div class=\"row\">\n";
print "      <div class=\"form-group\">\n";
echo "<h1>".lang('request_account')."</h1><p>";
echo lang('create_user_subheading')."</p>";
?>

        <div id="infoMessage"><?php echo $this->session->flashdata('message'); ?></div>

</div>

<?php echo form_open(uri_string());?>

     <div class="form-group"> 
            <?php echo lang('create_user_fname_label', 'first_name','class="col-xs-3"');?>
            <?php echo form_input($first_name); ?>
      </div>

      <div class="form-group">
            <?php echo lang('create_user_lname_label', 'last_name','class="col-xs-3"');?>  
            <?php echo form_input($last_name); ?>
      </div>
      
      <div class="form-group">
            <?php echo lang('edit_identity_lname_label', 'identity','class="col-xs-3"');?>  
            <?php echo form_input($identity); ?>
      </div>
      
      <div class="form-group">
            <?php echo lang('create_user_email_label', 'email','class="col-xs-3"');?>  
            <?php echo form_input($email); ?>
      </div>
      
      <div class="form-group">
            <?php echo lang('create_user_company_label', 'company','class="col-xs-3"');?>  
            <?php echo form_input($company); ?>
      </div>

    <!--  <div class="form-group">
            <?php #echo lang('create_user_phone_label', 'phone','class="col-xs-3"'); 
             #echo form_input($phone); 
             ?>
      </div> -->

      <div class="form-group">
            <?php echo lang('create_user_password_label', 'password',"class='col-xs-3' title='".$password['title']."'"); ?>  
            <?php echo form_input($password); ?>
      </div>

      <div class="form-group">
            <?php echo lang('create_user_password_confirm_label', 'password_confirm','class="col-xs-3"  title="At least 8 characters"');?> 
            <?php echo form_input($password_confirm); ?>
      </div>

      <div class="form-group">
          <?php
              echo form_submit('submit', lang('request_account'));
          ?>
      </div>
        <?php echo form_close();  ?>      
      <b>Note</b>: Password : min_lenght : <?php print $config['min_password_length']; ?>
      max_lenght <?php print $config['max_password_length']; ?><br />
</div> <!--  End Div param -->
<!-- //////////////    End auth/public/request_account      //////////////  -->
