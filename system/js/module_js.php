<?php
require_once('../../_app/init.php');
clearBuffer();
if(login()):
	if(!access(param('option'))):
		setApplicationJavascript();
		print "alert('ACCESO RESTRINGIDO".'\n'."No tiene permisos suficientes para acceder a esta sección');";
	else:
		$option = stripslashes(trim(param('option')));
		if(is_file( pathToView . $option . '.view.php')):
			include( pathToView . $option . '.view.php' );
		endif;
	endif;
else:
	setApplicationJavascript();
	print "alert('LA SESIÓN HA EXPIRADO" . '\n' . "Por favor, ingrese nuevamente su email y clave para acceder al sistema');window.location.href='".baseAdminURL."'";
	exit;
endif;
?>
