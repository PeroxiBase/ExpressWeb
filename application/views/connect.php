<!DOCTYPE html>
<html>
	<head>
		<title>Login</title>
		<link type="text/css" rel='stylesheet' href="<?php echo base_url('/assets/css/connect.css'); ?>"/>
		<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Arvo" />
		<script src="<?php echo base_url().'assets/js/jquery-2.1.4.min.js'; ?>"></script>
	</head>
	<body>
		<h2>Welcome</h2>
		<div class="formID">
			<?php
				echo form_fieldset('Login');
				echo validation_errors();
				echo form_open('visual','id="connectForm"');
				$data = array(
							'name'=>'username',
              'placeholder'  => 'Pseudo'
						);
				echo form_input($data);
				echo form_submit('login','Connect');
				echo form_fieldset_close();
			?>
		</div>
	</body>
</html>
