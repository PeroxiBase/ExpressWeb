<?php
$this->load->view("templates/header_exp");
$this->load->view("templates/menu");
$this->load->view($contents);
print "<br />";
$this->load->view("templates/footer_exp");
?>
