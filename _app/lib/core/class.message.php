<?php
class Message{
    
    
    public static function set($message, $type, $code = NULL){
		
		$type = strtoupper($type);
		
        switch($type):
            case "ERROR":
            case "SUCCESS":
            case "WARNING":
            case "INFORMATION":
                $messageType = $type;
                break;
           default:
                $messageType = "ERROR";
        endswitch;
        
        $message = array(
            "message"   =>  $message,
            "type"      =>  $messageType,
            "code"      =>  $code
        );
        
        $_SESSION[messageVar] = $message;
        
    }
	
	public static function text(){
		if(isset($_SESSION[messageVar]['message'])):
			$txt = $_SESSION[messageVar]['message'];
			unset($_SESSION[messageVar]);
			return $txt;
		else:
			return "";
		endif;
	}
	
	public static function type(){
		if(isset($_SESSION[messageVar]['type'])):
			return $_SESSION[messageVar]['type'];
		else:
			return "";
		endif;
	}
    
    public static function get(){
        
        if(isset($_SESSION[messageVar])):
            
            $Message = $_SESSION[messageVar];
            $GLOBALS[ "_" . $Message['type'] ] = $Message;
            unset($_SESSION[messageVar]);
            
			$ErrorHTML = '<div class="' . strtolower($Message['type']) . '">' . $Message['message'] . '</div>';
			return $ErrorHTML;
			
        endif;
        
    }
	
	public static function alert(){
        
        if(isset($_SESSION[messageVar])):
            
            $Message = $_SESSION[messageVar];
            $GLOBALS[ "_" . $Message['type'] ] = $Message;
            unset($_SESSION[messageVar]);
            
			$ErrorHTML = '<div class="alert alert-error">
							<button type="button" class="close" data-dismiss="alert">&times;</button>
							<strong></strong> '.htmlspecialchars($Message['message']).'
						</div>';
			
			echo $ErrorHTML;
			
        endif;
        
    }
    
}

?>