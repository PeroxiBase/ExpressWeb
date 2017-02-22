<?php
$this->load->view("templates/header");
$this->load->view("templates/menu");
$this->load->view($contents);
print "<br />";
$this->load->view("templates/footer");
?>
