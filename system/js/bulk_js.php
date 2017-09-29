<?php
require_once('../../_app/init.php');
if(login()):
	if(!isset($_POST['module'])):
		setApplicationJavascript();
		print "alert('Error en el módulo');";
		exit;
	else:

		$module = str_replace(";", "", $_POST['module']);
		$action = numParam('action');

		$permission = true;

		if(($action == 1 || $action == 2) && !permissionUpdate($module)):
			$permission = false;
		elseif($action == 3 && !permissionDelete($module)):
			$permission = false;
		endif;

		if(!$permission):
			setApplicationJavascript();
			print "alert('ACCESO RESTRINGIDO".'\n'."No tiene permisos suficientes para realizar la acción seleccionada.');";
			exit;
		endif;

		$ids	= array();

		foreach($_POST as $k => $v):
			if(strpos($k, "check_{$module}_") !== false):
				$ids[] = $v;
			endif;
		endforeach;
		$ids = json_encode($ids);
		eval('$error = ' . ucfirst($module) . "::bulk('{$action}', '{$ids}');");

	endif;
else:
	setApplicationJavascript();
	print "alert('LA SESIÓN A EXPIRADO ".'\n'."Por favor, ingrese nuevamente su E-mail y Contraseña para acceder al sistema.');window.location.href='".baseAdminURL."'";
endif;
?>
