<?php
/**
* The Expression Database.
*       view admin/login.php
*       admin login form
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage views
*/
?>
<!-- //////////////    admin/login  //////////////  -->
<div id="right">
  <span style="color: red"><?php echo isset($_SESSION['auth_message']) ? $_SESSION['auth_message'] : FALSE; ?></span> 
    <form action="login" method="post" name="identification">
      <table summary="">
        <tr>
          <td><label for="login">Username:</label></td>
          <td><input type="text" name="username" id="login" title="login name, required" required/></td>
        </tr>
        <tr>
          <td><label for="password">Password:</label></td>
          <td><input type="password" name="password" id="password" title="Your password" required/></td>
        </tr>
        <tr>
          <td><?php echo lang('login_remember_label', 'remember');?></td>
          <td><?php echo form_checkbox('remember', '1', FALSE, 'id="remember"');?><br /></td>
        </tr>
        <tr>
          <td colspan="2"> 
                <input type="submit"  class="ui-button ui-widget ui-state-default ui-corner-all" value="login" />
          </td>
        </tr>
      </table>
    </form>
	
    <p><a href="forgot_password"><?php echo lang('login_forgot_password');?></a></p>
    
</div>

<div id="left">
    <h2>Login </h2>
    <p>You need a login name in order to access Expression database:<br /> 
    if you do not have any, please send an email to 
    <a href="mailto:void@dom.org?subject=Login%20request%20for%20ExpressionDb">The ExpressWeb Team</a>.</p>
</div>
<!-- //////////////    admin/login  //////////////  -->
