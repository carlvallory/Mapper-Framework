<?php
require_once('inc/init.php');
clearBuffer();
checkConfig();
if(!login()):
	include_once( pathToView . "cms.login.php" );
else:
	clearBuffer();
	include_once( 'dashboard__cms.php' );
endif;
?>
