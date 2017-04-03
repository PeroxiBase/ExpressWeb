<?php
/**
* The Expression Database.
*       view templates/templates.php
*       load all views for html rendering
*       used by CTRL Visual and Display
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage views
*/
$this->load->view("templates/header_exp");
$this->load->view("templates/menu");
$this->load->view($contents);
$this->load->view("templates/footer_exp");
?>
