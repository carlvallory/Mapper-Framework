<?php

class File
{
	public static function last_folder($path)
	{
		preg_match_all('/[a-zA-Z0-9]+\//', $path, $aux);
		return $aux[0][count($aux[0]) - 1];
	}
	
	public static function Name(){
		return basename($_SERVER['SCRIPT_NAME']);
	}
	
	public static function URI(){
		$base = basename($_SERVER['SCRIPT_NAME']);
		return substr($base,0,-8);
	}
	
}

?>