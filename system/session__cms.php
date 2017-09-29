<?php
require_once('inc/init.php');
$location = "";
switch($_SERVER['REQUEST_METHOD']):
	case "POST":

		$admin_email  = addslashes(trim($_POST['login_email']));
		$admin_status = db::execute("SELECT admin_id, admin_status FROM admins WHERE admin_email='{$admin_email}'");

		$failed_login_attempts = 0;

		if(haveRows($admin_status)):

			if($admin_status[0]['admin_status'] == 0):
				setLocked("USUARIO BLOQUEADO");
			endif;

		endif;

		if((int)$_SESSION['_lgatt'] > 5):

			$admin_data  = Admins::get("admin_email='{$admin_email}'");

			if(haveRows($admin_data)):
				Admins::set("admin_status",0,"admin_email='{$admin_email}'");
				$message = "USUARIO BLOQUEADO: Se detectaron intentos fallidos de ingreso.";
			else:
				$message = "ACCESO BLOQUEADO: Se detectaron intentos fallidos de ingreso.";
			endif;

			setLocked($message);
			exit;

		endif;

		if($_POST['token'] == token("login")):
			if($_SESSION['_intctc']>3):
				if(!Captcha::validate($_POST['captcha_text'])):
					Message::set("Código incorrecto","ERROR");
					$_SESSION['_lgatt'] = (int)$_SESSION['_lgatt'] + 1;
				endif;
			endif;

			$login = Login::set($_POST['login_email'], $_POST['login_pass']);
			if(!$login):

				if(haveRows($admin_status)):
					$failed_attempts = "SELECT * FROM admin_login_attempts WHERE admin_id = {$admin_status[0]['admin_id']} AND admin_login_response = 'FAILED' AND UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(admin_login_timestamp) < 600";
					$failed_attempts = db::execute($failed_attempts);

					if(count($failed_attempts) > 5):
						Admins::set("admin_status",0,"admin_id='{$admin_status[0]['admin_id']}'");
						setLocked("USUARIO BLOQUEADO: Se detectaron intentos fallidos de ingreso.");
					endif;
				endif;

				$_SESSION['_intctc'] = (int)$_SESSION['_intctc'] + 1;
				Message::set("Email o clave incorrectos", "ERROR");
			else:
				$location = "dashboard";
			endif;

		else:
			$_SESSION['_intctc'] = (int)$_SESSION['_intctc'] + 1;
			session_regenerate_id(true);
			Message::set("Error de autenticación", "ERROR");
		endif;
		redirect(baseAdminURL . $location);
		break;
	case "GET":
		if($_GET['action'] != "logout"):
			setNotFound();
		else:
			session_regenerate_id(true);
			session_unset();
			session_destroy();
			redirect(baseAdminURL);
		endif;
		break;
	default:
		setUnauthorized();
		redirect(baseAdminURL . $location);
endswitch;

?>
