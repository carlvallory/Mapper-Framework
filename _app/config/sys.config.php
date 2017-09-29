<?php
/*zona horaria*/
@date_default_timezone_set('America/Asuncion');

/**/
define( "isLocal",     true ); 			/*si se trabaja de manera local*/
define( "sysName",      "SISTEMA" );    	/*nombre del cms*/
define( "sysVersion",   "V 3.1.5");
define( "domainName",	"getmapper.xyz");
define( "logEnabled", 	true);	    	/*habilita log de consultas sql*/

/**/
define( "encryptionKey", "hbLfOiutjHSMzM5" );

/**/
$domainName = explode('.', domainName);
$domainName = $domainName[0];
$sessionSub = strtolower(metaphone($domainName));

define( "authToken",		strrev(md5(sha1(session_id()))));
define( "adminLogin",		"_adl_" . $sessionSub );
define( "userLogin",		"_usl_" . $sessionSub );
define( "messageVar", 		"_msgflash_" . $sessionSub );

/**/
define( "MESSAGE_ERROR",		"ERROR" );
define( "MESSAGE_SUCCESS",		"SUCCESS" );
define( "MESSAGE_WARNING",		"WARNING" );
define( "MESSAGE_INFORMATION",	"INFORMATION" );
define( "MESSAGE_QUESTION",		"QUESTION" );

/**/
require_once( 'sys.paths.php' );
require_once( 'sys.db.php' );
require_once( 'sys.smtp.php' );
?>
