<?php
require_once('inc/init.php');
clearBuffer();
if(!login()):
	if(isset($_POST['token'])):
	else:
		$success = false;
	endif;
	include_once( pathToView . "cms.reset_password.php" );
else:
	clearBuffer();
	setNotFound();
endif;
?>