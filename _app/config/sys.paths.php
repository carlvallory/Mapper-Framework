<?php
define( "baseAppFolder",	"project_folder");
define( "baseAdminFolder",	baseAppFolder . "system/" );
define( "baseSiteFolder", 	baseAppFolder . "");

define( "pathToRoot",   	realpath(dirname( __FILE__ ) ) . '/../..' );
define( "pathToLib",   		realpath(pathToRoot) . '/_app/lib/' );
define( "rootUpload",   	realpath(pathToRoot) . '/upload/' );
define( "pathToView",		realpath(pathToRoot) . '/_app/lib/view/' );
define( "pathToController",	realpath(pathToRoot) . '/_app/lib/controller/' );
define( "pathToTemplate", 	realpath(pathToRoot) . '/_app/templates/' );
define( "pathToLog",		realpath(pathToRoot) . '/_app/dblog/' );

define( "baseURL",     		"http://" . $_SERVER['SERVER_NAME'] . "/" . baseAppFolder );
define( "baseAdminURL",		"http://" . $_SERVER['SERVER_NAME'] . "/" . baseAdminFolder );
define( "baseSiteURL",		"http://" . $_SERVER['SERVER_NAME'] . "/" . baseSiteFolder );
define( "uploadURL",		baseURL . "upload/");
?>
