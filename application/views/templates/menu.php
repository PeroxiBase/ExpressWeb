<?php
$path= base_url();
$username =$this->session->username;
?>
<nav class="navbar navbar-inverse navbar-fixed-top" >
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="<?php print "${path}"; ?>" ><span class="glyphicon glyphicon-home"></span></a>
    </div>
    <ul class="nav navbar-nav">
<?php if ($this->ion_auth->logged_in())
{
$path= base_url();
?>
      <li><a href=<?php print"${path}visual"; ?> >RUN ANALYSIS</a></li>
      <li><a href=<?php print"${path}visual/howPage"; ?> >HOW IT WORKS</a></li>
      <?php
      if($username !="demo")
      { ?>
      <li><a href="<?php print "${path}auth_public/update_account"; ?>" >PROFILE</a></li>
<?php 
        }
  }
    if($this->ion_auth->is_admin())
{   
?>
    <!--  <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Admin<span class="caret"></span></a>
        <ul class="dropdown-menu"> -->
          <li><a href="<?php print "${path}dashboard"; ?>" >USERS ACCOUNT</a></li>
          <li><a href="<?php print "${path}create_table"; ?>" >DATABASE MANAGEMENT</a></li>
   <!--     </ul>
      </li> -->
<?php } ?>
    </ul>
    <ul class="nav navbar-nav navbar-right">
<?php if ($this->ion_auth->logged_in()){ ?>
      <li><a href="<?php print "${path}auth/logout"; ?>" ><span class="glyphicon glyphicon-user"></span> Log Out</a></li>
<?php } 
else {?> 
      <li><a href="<?php print "${path}welcome/request_account"; ?>" ><span class="glyphicon glyphicon-user"></span> Sign Up</a></li>
      <li><a href="<?php print "${path}auth/login"; ?>" ><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
<?php } ?>
    </ul>
  </div>
</nav>
<div class="container-fluid" id ="block1" style="height:auto">
