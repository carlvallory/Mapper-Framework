<?php
class Blowfish{
    private $key;
    private $cipher;

    function __construct($pass){
        $this->cipher = mcrypt_module_open('blowfish','','cbc','');
        $ks = mcrypt_enc_get_key_size($this->cipher);
        $this->key = pack('H*',sha1($pass));
    }

    function encryptString($plaintext, $iv = ''){
        if ($iv == '') {
            $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($this->cipher));
        }
        else {
            $iv = pack("H*", $iv);
        }
        mcrypt_generic_init($this->cipher, $this->key, $iv);
        $bs = mcrypt_enc_get_block_size($this->cipher); // get block size
        $plaintext = mb_convert_encoding($plaintext,'UTF-16BE'); // set to 2 byte, network order
        $pkcs = $bs - (strlen($plaintext) % $bs); // get pkcs5 pad length
        $pkcs = str_repeat(chr($pkcs), $pkcs); // create padding string
        $plaintext = $plaintext.$pkcs; // append pkcs5 padding to the data
        $result = mcrypt_generic($this->cipher, $plaintext);
        mcrypt_generic_deinit($this->cipher);
        return $iv.$result;
    }

    function decryptString($ciphertext){
        $bs = mcrypt_enc_get_block_size($this->cipher); // get block size
        $iv_size = mcrypt_enc_get_iv_size($this->cipher);
        if ((strlen($ciphertext) % $bs) != 0) { // check string is proper size
            exit(1);
        }
        $iv = substr($ciphertext, 0, $iv_size); // retrieve IV
        $ciphertext = substr($ciphertext, $iv_size);
        mcrypt_generic_init($this->cipher, $this->key, $iv);
        $result = mdecrypt_generic($this->cipher, $ciphertext); // decrypt
        //echo var_dump(unpack('c*',$iv))."\n";
        $padding = ord(substr($result,-1)); // retrieve padding
        $result = substr($result,0,$padding * -1); // and remove it
        mcrypt_generic_deinit($this->cipher);
        return $result;
    }

    function __destruct(){
        mcrypt_module_close($this->cipher);
    }
	
	public static function encrypt($string, $password = encryptionKey){
		$obj = new self($password);
		return bin2hex($obj->encryptString($string));
	}
	
	public static function decrypt($string, $password = encryptionKey){
		$obj = new self($password);
		return $obj->decryptString(pack("H*", $string));
	}
}
?>