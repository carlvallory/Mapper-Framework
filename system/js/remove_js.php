<?php
require_once('../../_app/init.php');
if(login() && !empty($_POST)):

	if(!permissionDelete(param('option'))):
		setApplicationJavascript();
		print "alert('ACCESO RESTRINGIDO".'\n'."No tiene permisos suficientes para realizar esta acción');";
		exit;
	endif;

	$option		= ucfirst(stripslashes(trim(param('option'))));
	$id			= numParam('id');
	$callback	= stripslashes(trim(param('callback')));

	eval('$return = ' . $option . '::delete('.$id.');');

	clearBuffer();
	include_once( pathToView . strtolower($option) . '.' . $callback . '.php' );

else:
	setApplicationJavascript();
	print "alert('LA SESIÓN A EXPIRADO ".'\n'."Por favor, ingrese nuevamente su E-mail y Contraseña para acceder al sistema.');window.location.href='".baseAdminURL."'";
endif;
?>
