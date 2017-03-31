<?php
/**
* The Expression Database.
*  application menus
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage views
*/
#### dirty way to change URL to SSL or NOT.
if( ! empty($_SERVER['HTTPS']) )
{
     $pathS= $this->config->config['base_url']."/";
     $path= $this->config->config['base_urlNS']."/";
     $SSL='on';
}
else 
{
        $path= $this->config->config['base_url']."/"; 
        $pathS= $this->config->config['base_urlNS']."/";
        $SSL='off';
}
$username =$this->session->username;
?>
<!-- //////////////    templates/menu  //////////////  -->
<nav class="navbar navbar-inverse navbar-fixed-top" >
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#expw-navbar" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
          <a class="navbar-brand" href="<?php print "${path}"; ?>" ><span class="glyphicon glyphicon-home"></span></a>
        </div>
        
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="expw-navbar">
            <ul class="nav navbar-nav">
<?php if ($this->ion_auth->logged_in())
{
?>
              <li><a href=<?php print"${path}visual"; ?> >RUN ANALYSIS</a></li>
              <li><a href=<?php print"${path}visual/howPage"; ?> >HOW IT WORKS</a></li>
              <?php
              if($username !="demo")
              { ?>
              <li><a href="<?php print "${pathS}auth_public/update_account"; ?>" >PROFILE</a></li>
<?php 
        }
  }
    if($this->ion_auth->is_admin())
{   
?>
                  <li><a href="<?php print "${pathS}dashboard"; ?>" >USERS ACCOUNT</a></li>
                  <li><a href="<?php print "${pathS}create_table"; ?>" >Db Admin</a></li>
<?php } ?>
            </ul>
            <ul class="nav navbar-nav navbar-right">
<?php if ($this->ion_auth->logged_in()){ ?>
                <li><a href="<?php print "${path}auth/logout"; ?>" ><span class="glyphicon glyphicon-user"></span>Log Out</a></li>
<?php } 
else {?> 
                <li><a href="<?php print "${pathS}welcome/request_account"; ?>" ><span class="glyphicon glyphicon-user"></span> Sign Up</a></li>
                <li><a href="<?php print "${pathS}auth/login"; ?>" ><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
<?php } ?>
            </ul>
       </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

<div class="container" id ="block1" style="height:auto">
<!-- //////////////    End templates/menu  //////////////  -->

