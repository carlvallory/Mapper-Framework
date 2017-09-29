<?php
require_once('../../_app/init.php');
if(login() && isset($_POST['token'])):

	$command	= Encryption::Decrypt($_POST['token']);
	$callback	= Encryption::Decrypt($_POST['callback']);

	$option = explode("::", $command);
	$option = strtolower($option[0]);
	$permission = true;
	if(numParam('id') > 0 && !permissionUpdate($option)):
		$permission = false;
	elseif(numParam('id') == 0 && !permissionInsert($option)):
		$permission = false;
	endif;

	if(!$permission):
		setApplicationJavascript();
		print "alert('ACCESO RESTRINGIDO".'\n'."No tiene permisos suficientes para realizar la acción');";
		exit;
	endif;

	@$callback = json_decode($callback);

	eval('$error = ' . $command . ";");

	if(number($error) > 0):
		$_POST['save_id'] = $error;
	endif;

	if(Message::type() == MESSAGE_ERROR):
		clearBuffer();
		include_once( pathToView . $callback->error );
	else:
		clearBuffer();
		include_once( pathToView . $callback->success );
	endif;
else:
	setApplicationJavascript();
	print "alert('LA SESIÓN A EXPIRADO ".'\n'."Por favor, ingrese nuevamente su E-mail y Contraseña para acceder al sistema.');window.location.href='".baseAdminURL."'";
endif;
?>
