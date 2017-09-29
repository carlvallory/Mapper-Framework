<?php
require_once('../../_app/init.php');
if(login()):

	$permission = true;

	if(numParam('id') > 0 && !permissionUpdate(param('option'))):
		$permission = false;
	elseif(numParam('id') == 0 && !permissionInsert(param('option'))):
		$permission = false;
	endif;

	if(!$permission):
		setApplicationJavascript();
		print "alert('ACCESO RESTRINGIDO".'\n'."No tiene permisos suficientes para acceder a esta sección');";
		exit;
	endif;

	$option = stripslashes(trim(param('option')));
	if(is_file( pathToView . $option . '.form.php')):
		include( pathToView . $option . '.form.php' );
	endif;
else:
	setApplicationJavascript();
	print "alert('LA SESIÓN A EXPIRADO ".'\n'."Por favor, ingrese nuevamente su E-mail y Contraseña para acceder al sistema.');window.location.href='".baseAdminURL."'";
endif;
?>
