<?php
class ImageUpload{
	
	/************************************
	* Procesa y redimensiona imagenes   *
	* Luis Portillo - Noviembre de 2010 *
	* Ultima modificacion: 13/10/2013 Lucho*
	*************************************/
	
	private $_Width;
	private $_Height;
	private $_SourcePath;
	private $_SourceFile;
	private $_TargetPath;
	private $_TargetFile;
	private $_Quality;
	private $_Canvas;
	private $_Center;
	private $_Align;
	private $_OutputFormat;
	private $_Transparency = false;
	private $_TransparentColor = array(0,0,0);
	private $_BackgorundColor  = array(0,0,0);
	
	function __Constructor(){}
	
	/*convierte a numero un valor*/
	private function toNumber($n){
		return is_numeric($n) ? $n : number_format($n, 0, "", "");
	}

	/*establece el nuevo tamaño de la imagen*/
	public function setSize($w,$h){
		
		if($this->toNumber($w) == 0 && $this->toNumber($h) == 0){
			Message::set("Especifique el alto o ancho máximo para la imagen", MESSAGE_ERROR);
			return false;
		}else{
			$this->_Width  = $w;
			$this->_Height = $h;
		}
		
	}
	
	/*establece calidad de la imagen*/
	public function setQuality($q){
		$this->_Quality = $this->toNumber($q);
	}
	
	/*establece directorio de origen*/
	public function setSource($s){
		$this->_SourcePath = $s;
	}
	
	/*directorio de destino de la imagen*/
	public function setTarget($t){
		$this->_TargetPath = $t;
	}
	
	/*devuelve el nombre del archivo de salida*/
	public function getOutputFileName(){
		return $this->_TargetFile . "." . strtolower($this->_OutputFormat);
	}
	
	/*formato del archivo final JPG|PNG|GIF*/
	
	public function setOutputFormat($f){
		$formats = array("JPEG","JPG","PNG","GIF");
		if(!array_search(strtoupper($f), $formats) || strtoupper($f) == "JPEG"){
			$this->_OutputFormat = "JPG";
		}else{
			$this->_OutputFormat = strtoupper($f);
		}
	}
	
	/*nombre del archivo de salida de la imagen*/
	public function setOutputFile($f){
		$this->_TargetFile = $f;
	}
	
	function setTransparency($c,$t=true){
		if(!is_array($c)){
			Message::set("Parámetros de color de transparencia incorrectos", MESSAGE_ERROR);
			return false;
		}else{
			if(count($c) < 3){
				Message::set("Orden de colores de transparencia incorrectos. Debe ser RGB", MESSAGE_ERROR);
				return false;
			}else{
				$this->_Transparency = true;
				$this->_TransparentColor = $c;
			}
		}
	}
	
	/*fondo de la imagen*/
	public function setBackgroundColor($c){
		if(!is_array($this->_BackgorundColor)){
			Message::set("Parámetros de color de fondo incorrectos", MESSAGE_ERROR);
			return false;
		}else{
			if(count($c) < 3){
				Message::set("Orden de colores de fondo incorrectos. Debe ser RGB", MESSAGE_ERROR);
				return false;
			}else{
				$this->_BackgorundColor = $c;
			}
		}
	}
	
	/*establece alineamiento de la imagen*/
	private function Alignment($m){
		
		$m = strtolower($m);
		$a = array(
			"top",
			"top_left",
			"top_middle",
			"top_center",
			"top_right",
			"middle_left",
			"middle",
			"center",
			"middle_right",
			"bottom",
			"bottom_left",
			"bottom_middle",
			"bottom_right"
		);
		
		if(!array_search($m, $a)){
			Message::set("Parámetro de alineación incorrecto.", MESSAGE_ERROR);
			return false;
		}else{
			$this->_Align = $m;
		}
		
	}
	
	public function setAlignment($a){
		$this->Alignment($a);
	}
	
	/*crea el recurso de imagen a partir de un archivo*/
	private function ImageFromFile($f){
		
		
		$ImageInfo = getimagesize($this->_SourcePath . $this->_SourceFile);
		
		switch($ImageInfo['mime']){
			case "image/jpeg":
				$image = imagecreatefromjpeg($f);
				break;
			case "image/gif":
				$image = imagecreatefromgif($f);
				if($this->_Transparency){
					$transparentColor = imagecolorallocate($image, $this->_TransparentColor[0], $this->_TransparentColor[1], $this->_TransparentColor[2]);
					imagecolortransparent($image,  $transparentColor);
				}
				break;
			case "image/png":
				$image = imagecreatefrompng($f);
				imagealphablending($image, false);
				imagesavealpha($image, true);
				break;
			default:
				Message::set("Formato de archivo incorrecto", MESSAGE_ERROR);
				return false;
		}
		
		return $image;
		
	}
	
	/*nombre del archivo a redimensionar*/
	
	public function fileToResize($f){
		$this->_SourceFile = $f;
	}
	
	/*redimensiona la imagen*/
	public function Resize(){
		
		if(!$this->_SourceFile){
			
			Message::set("No se encontró el archivo.", MESSAGE_ERROR);
			return false;
			
		}else{
			
			if(!$this->_Canvas){
				/* calcula las medidas de la imagen para escalar al nuevo tamaño de forma proporcional */
				list($ImageWidth, $ImageHeight, $ImageType, $ImageAttributes) = getimagesize($this->_SourcePath . $this->_SourceFile);
				
				
				if($this->_Height == 0):
					if($ImageWidth < $this->_Width):
						$this->_Height = $ImageHeight;
					else:
						$scaleheight = $this->_Width / $ImageWidth;
						$this->_Height = ceil($ImageHeight * $scaleheight);
					endif;
				endif;
				$NewCanvas = imagecreatetruecolor($this->_Width, $this->_Height);
				if($this->_Transparency){
					
					$transparency = imagecolorallocatealpha($NewCanvas, $this->_TransparentColor[0], $this->_TransparentColor[1], $this->_TransparentColor[2], 127);
					imagefill($NewCanvas, 0, 0, $transparency);
					imagealphablending($NewCanvas, false);
					imagesavealpha($NewCanvas, true);
					
				}
			}else{
				$NewCanvas = $this->ImageFromFile($this->_Canvas);
			}
			
			/*color de fondo*/
			if(!$this->_Transparency){
				if($this->_BackgorundColor[0] != 0 && $this->_BackgorundColor[1] != 1 && $this->_BackgorundColor[2] != 0){
					$BgColor = imagecolorallocate($NewCanvas, $this->_BackgorundColor[0], $this->_BackgorundColor[1], $this->_BackgorundColor[2]);
					imagefilledrectangle($NewCanvas, 0, 0, $this->_Width, $this->_Height, $BgColor);
				}
			}
			
			$Image = $this->ImageFromFile($this->_SourcePath . $this->_SourceFile);
			
			
			$Scale = ($ImageWidth < $ImageHeight) ? ($this->_Height / $ImageHeight) : ($this->_Width / $ImageWidth);
			
			$NewWidth	= ceil($ImageWidth * $Scale);
			$NewHeight	= ceil($ImageHeight * $Scale);
			
			/* calcula nuevamente la escala cuando las nuevas proporciones exceden el tamaño final _Width o _Height*/
			if($NewWidth > $this->_Width):
				$Scale = ($this->_Width / $ImageWidth);
				$NewWidth	= ceil($ImageWidth * $Scale);
				$NewHeight	= ceil($ImageHeight * $Scale);
			endif;
			
			if($NewHeight > $this->_Height):
				$Scale = ($this->_Height / $ImageHeight);
				$NewWidth	= ceil($ImageWidth * $Scale);
				$NewHeight	= ceil($ImageHeight * $Scale);
			endif;
			
			/*calcula coordenadas XY segun tipo de alineacion*/
			switch($this->_Align){
				case "top":
				case "top_left":
					$destX = 0;
					$destY = 0;
					break;
				case "top_center":
				case "top_middle":
					$destX = ceil(($this->_Width / 2) - ($NewWidth / 2));
					$destY = 0;
					break;
				case "top_right":
					$destX = ceil($this->_Width - $NewWidth);
					$destY = 0;
					break;
				case "center_left":
				case "middle_left":
					$destX = 0;
					$destY = ceil(($this->_Height / 2) - ($NewHeight / 2));
					break;
				case "middle":
				case "center":
					$destX = ceil(($this->_Width / 2) - ($NewWidth / 2));
					$destY = ceil(($this->_Height / 2) - ($NewHeight / 2));
					break;
				case "center_right":
				case "middle_right":
					$destX = ceil($this->_Height - $NewWidth);
					$destY = ceil(($this->_Height / 2) - ($NewHeight / 2));
					break;
				case "bottom":
				case "bottom_left":
					$destX = 0;
					$destY = $this->_Height - $NewHeight;
					break;
				case "bottom_center":
				case "bottom_middle":
					$destX = ceil(($this->_Width / 2) - ($NewWidth / 2));
					$destY = $this->_Height - $NewHeight;
					break;
				case "bottom_right":
					$destX = ceil($this->_Width - $NewWidth);
					$destY = $this->_Height - $NewHeight;
					break;
				default:
					$destX = 0;
					$destY = 0;
					
			}
			
			imagecopyresampled($NewCanvas, $Image, $destX, $destY, 0, 0, $NewWidth, $NewHeight, $ImageWidth, $ImageHeight);
			
			$this->_TargetFile	= !$this->_TargetFile	?	$this->_SourceFile : $this->_TargetFile;
			$this->_Quality		= !$this->_Quality	?	80 : $this->_Quality;
			
			switch($this->_OutputFormat){
				case "JPG":
				case "JPEG":
					imagejpeg($NewCanvas, $this->_TargetPath . $this->_TargetFile . "." . strtolower($this->_OutputFormat), $this->_Quality);
					break;
				case "PNG":
					$calidad = ceil($this->_Quality	/ 10);
					imagepng($NewCanvas, $this->_TargetPath . $this->_TargetFile . "." . strtolower($this->_OutputFormat), $calidad);
					break;
				case "GIF":
					imagegif($NewCanvas, $this->_TargetFile . $this->_TargetFile . "." . strtolower($this->_OutputFormat));
					break;
			}
			
			imagedestroy($NewCanvas);
			imagedestroy($Image);
			
		}
		
	}

}
?>