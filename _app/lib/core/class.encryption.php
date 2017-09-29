<?php

class Encryption{
	
	public static function Encrypt($value, $key = authToken){
		
		$encType = MCRYPT_RIJNDAEL_256;
		$encMode = MCRYPT_MODE_ECB;
		$return  = mcrypt_encrypt($encType, $key, $value, $encMode, mcrypt_create_iv(mcrypt_get_iv_size($encType, $encMode), MCRYPT_RAND));
		$return  = strrev($return);
		return base64_encode($return);
		
	}

	public static function Decrypt($value, $key = authToken){

		$value	 = base64_decode($value);
		$value	 = strrev($value);
		
		$encType = MCRYPT_RIJNDAEL_256;
		$encMode = MCRYPT_MODE_ECB;
		$return  = mcrypt_decrypt($encType, $key, $value, $encMode, mcrypt_create_iv(mcrypt_get_iv_size($encType, $encMode), MCRYPT_RAND));
		
		return trim($return);
		
	}
}

?>