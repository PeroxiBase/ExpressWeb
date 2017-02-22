<?php
/**
* The Expression Database .
*
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<picard.sylvain3@gmail.com>
*@version 1.0
*@package expressionWeb
*/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{  
    
  public function __construct()
  {
    parent::__construct();
    $this->load->model("generic");     
    $this->config->load('expressWeb');
    // html page tab title
    $this->header_name = $this->config->item('header_name');
    $this->footer_title = $this->config->item('header_name');
 }
 
 
 public function back($step = -1) 
 {
	return "    <a href=\"javascript:history.go($step)\"  class=\"ui-button ui-widget ui-state-default ui-corner-all\">&lt;&lt; Back</a>";
 }
 
}
