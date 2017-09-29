<?php
/*
* Anadir aqui la carpeta y los nombres de las clases principales
*/
$providers = array(
    'captcha/class.captcha.php'
);

if(is_array($providers)):
	foreach($providers as $provider):
		include __DIR__.'/'.$provider;
	endforeach;
endif;
?>
