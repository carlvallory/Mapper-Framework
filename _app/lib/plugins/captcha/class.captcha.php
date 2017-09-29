<?php
/*Luis Portillo
* Noviembre de 2008
* Captcha para Formularios

** Modificado: abril 2016 **
** Modificado: Octubre 2014 **
** Modificado: Setiembre 2013 **
*/
class Captcha {

    public $width  = 180;
    public $height = 60;
	public $wordsFile = '';
    public $minWordLength = 4;
    public $maxWordLength = 7;
    public $session_var = '_captcha';
    public $backgroundColor = array(255, 255, 255);
    public $colors = array(
        array(27,78,181), // R
        array(22,163,35), // G
        array(214,36,7),  // B
    );
    public $shadowColor = false; //array(0, 0, 0);
    public $fonts = array(
        'Antykwa'  => array('spacing' => -1, 'minSize' => 27, 'maxSize' => 30, 'font' => 'AntykwaBold.ttf'),
        'Heineken' => array('spacing' => 1, 'minSize' => 24, 'maxSize' => 32, 'font' => 'Heineken.ttf'),
        'VeraSans' => array('spacing' => 1, 'minSize' => 20, 'maxSize' => 26, 'font' => 'VeraSansBold.ttf'),
    );

    /** configuracion de deformacion en ejes X Y */
    public $Yperiod    = 5;
    public $Yamplitude = 3;
    public $Xperiod    = 5;
    public $Xamplitude = 3;

    /** rotaci�n de caracteres */
    public $maxRotation = 4;

    /**
     * Factor de calidad de la imagen
     * 1: low, 2: medium, 3: high
     */
    public $scale = 2;

	/* efecto blur */
    public $blur = false;
    public $debug = false;

    public $imageFormat = 'jpeg';
	public $im;

    public function __construct($config = array()) {
    }

    public static function install(){

    }

    public static function render() {

		$obj = self::getInstance();

        $ini = microtime(true);

        $obj->ImageAllocate();

        $text = $obj->GetCaptchaText();
        $fontcfg  = $obj->fonts[array_rand($obj->fonts)];

        $obj->WriteText($text, $fontcfg);


        $_SESSION[Encryption::Encrypt($obj->session_var)] = Encryption::Encrypt($text);

        $obj->WaveImage();
        if ($obj->blur) {
            imagefilter($obj->im, IMG_FILTER_GAUSSIAN_BLUR);
        }
        $obj->ReduceImage();


        if ($obj->debug) {
            imagestring($obj->im, 1, 1, $obj->height-8,
                "$text {$fontcfg['font']} ".round((microtime(true)-$ini)*1000)."ms",
                $obj->GdFgColor
            );
        }

        $obj->WriteImage();
        $obj->Cleanup();
    }

    protected function ImageAllocate() {
        // libera memoria
        if (!empty($this->im)) {
            imagedestroy($this->im);
        }

        $this->im = imagecreatetruecolor($this->width*$this->scale, $this->height*$this->scale);

        // color de fondo
        $this->GdBgColor = imagecolorallocate($this->im,
            $this->backgroundColor[0],
            $this->backgroundColor[1],
            $this->backgroundColor[2]
        );
        imagefilledrectangle($this->im, 0, 0, $this->width*$this->scale, $this->height*$this->scale, $this->GdBgColor);

        // color del texto
        $color           = $this->colors[mt_rand(0, sizeof($this->colors)-1)];
        $this->GdFgColor = imagecolorallocate($this->im, $color[0], $color[1], $color[2]);

        // color de sombra color
        if (!empty($this->shadowColor)) {
            $this->GdShadowColor = imagecolorallocate($this->im,
                $this->shadowColor[0],
                $this->shadowColor[1],
                $this->shadowColor[2]
            );
        }
    }

	/*genera texto aleatorio*/
    protected function GetCaptchaText() {
        $text = $this->GetDictionaryCaptchaText();
        if (!$text) {
            $text = $this->GetRandomCaptchaText();
        }
        return $text;
    }


    protected function GetRandomCaptchaText($length = null) {
        if (empty($length)) {
            $length = rand($this->minWordLength, $this->maxWordLength);
        }

        $words  = "abcdefghijlmnopqrstvwyz";
        $vocals = "aeiou";

        $text  = "";
        $vocal = rand(0, 1);
        for ($i=0; $i<$length; $i++) {
            if ($vocal) {
                $text .= substr($vocals, mt_rand(0, 4), 1);
            } else {
                $text .= substr($words, mt_rand(0, 22), 1);
            }
            $vocal = !$vocal;
        }
        return $text;
    }


	/* trae palabras del dicionario de palabras */
    function GetDictionaryCaptchaText($extended = false) {
        if (empty($this->wordsFile)) {
            return false;
        }

        $fp     = fopen($this->wordsFile, "r");
        $length = strlen(fgets($fp));
        if (!$length) {
            return false;
        }
        $line   = rand(0, (filesize($this->wordsFile)/$length)-1);
        if (fseek($fp, $length*$line) == -1) {
            return false;
        }
        $text = trim(fgets($fp));
        fclose($fp);


        /** vocales aleatorias */
        if ($extended) {
            $text   = str_split($text, 1);
            $vocals = array('a', 'e', 'i', 'o', 'u');
            foreach ($text as $i => $char) {
                if (mt_rand(0, 1) && in_array($char, $vocals)) {
                    $text[$i] = $vocals[mt_rand(0, 4)];
                }
            }
            $text = implode('', $text);
        }

        return $text;
    }

	/* escribe el texto */
    protected function WriteText($text, $fontcfg = array()) {
        if (empty($fontcfg)) {
            $fontcfg  = $this->fonts[array_rand($this->fonts)];
        }
        $fontfile = __DIR__ . '/fonts/' .$fontcfg['font'];


        /* tama�o de las letras */
        $lettersMissing = $this->maxWordLength-strlen($text);
        $fontSizefactor = 1.1+($lettersMissing*0.09);

        /* genera el texto */
        $x      = 10*$this->scale;
        $y      = 2+round(($this->height*27/40)*$this->scale);
        $length = strlen($text);
        for ($i=0; $i<$length; $i++) {
            $degree   = rand($this->maxRotation*-1, $this->maxRotation);
            $fontsize = rand($fontcfg['minSize'], $fontcfg['maxSize'])*$this->scale*$fontSizefactor;
            $letter   = substr($text, $i, 1);

            if ($this->shadowColor) {
                $coords = imagettftext($this->im, $fontsize, $degree,
                    $x+$this->scale, $y+$this->scale,
                    $this->GdShadowColor, $fontfile, $letter);
            }
            $coords = imagettftext($this->im, $fontsize, $degree,
                $x, $y,
                $this->GdFgColor, $fontfile, $letter);
            $x += ($coords[2]-$x) + ($fontcfg['spacing']*$this->scale);
        }
    }



    /* deforma la imagen */
    protected function WaveImage() {
        /* eje x */
        $xp = $this->scale*$this->Xperiod*rand(1,3);
        $k = rand(0, 100);
        for ($i = 0; $i < ($this->width*$this->scale); $i++) {
            imagecopy($this->im, $this->im,
                $i-1, sin($k+$i/$xp) * ($this->scale*$this->Xamplitude),
                $i, 0, 1, $this->height*$this->scale);
        }

        /* eje y*/
        $k = rand(0, 100);
        $yp = $this->scale*$this->Yperiod*rand(1,2);
        for ($i = 0; $i < ($this->height*$this->scale); $i++) {
            imagecopy($this->im, $this->im,
                sin($k+$i/$yp) * ($this->scale*$this->Yamplitude), $i-1,
                0, $i, $this->width*$this->scale, 1);
        }
    }


	/* reduce la imagen a su tama�o final*/
    protected function ReduceImage() {

        $imResampled = imagecreatetruecolor($this->width, $this->height);
        imagecopyresampled($imResampled, $this->im,
            0, 0, 0, 0,
            $this->width, $this->height,
            $this->width*$this->scale, $this->height*$this->scale
        );
        imagedestroy($this->im);
        $this->im = $imResampled;
    }

 	/* salida */
    protected function WriteImage() {
        if ($this->imageFormat == 'png') {
            header("Content-type: image/png");
            imagepng($this->im);
        } else {
            header("Content-type: image/jpeg");
            imagejpeg($this->im, null, 80);
        }
    }

    /* libera memoria */
    protected function Cleanup() {
        imagedestroy($this->im);
    }

	public static function validate($text){
		$obj = self::getInstance();
		if(isset($_SESSION[Encryption::Encrypt($obj->session_var)])):
			$value = Encryption::Decrypt($_SESSION[Encryption::Encrypt($obj->session_var)]);
			return strtolower($value) == $text ? true : false;
		else:
			return false;
		endif;
	}

	public static function reset(){
		unset($_SESSION[Encryption::Encrypt($obj->session_var)]);
	}

	private static function getInstance(){
		return new self();
	}
}

?>
