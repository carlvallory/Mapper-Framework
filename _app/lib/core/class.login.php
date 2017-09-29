<?php
class Login extends Mysql {
	
	public static function set($email, $passw){
	
		$obj = new self();
		
		$email 	= addslashes(strtolower(trim($email)));
		$passw	= md5($passw . "_" . strrev(strtoupper($email)));
		
		$result	= $obj->find("admins", "admin_email", $email, "admin_status = 1 AND admin_hidden = 0");
		
		if(is_array($result) && count($result) > 0):
		
			$result = $result[0];
			
			$_POST['admin_id'] = $result['admin_id'];
			$_POST['admin_login_ip_address'] = getIpAddress();
			
			if($result['admin_password'] != $passw || $result['admin_status'] != 1):
				
				$_POST['admin_login_response'] = "FAILED";
				Admin_login_attempts::save(0);
				return false;
			else:
				foreach($result as $k => $v):
					$data[Encryption::Encrypt($k)] = Encryption::Encrypt($v);
				endforeach;
				$group_isroot = $obj->find("admins_groups", "group_id", $result['group_id'], "group_status = 1 AND group_hidden = 0");
				$data[Encryption::Encrypt('group_isroot')] = Encryption::Encrypt($group_isroot[0]['group_isroot']);
				$_SESSION[Encryption::Encrypt(adminLogin)]	= $data;
				$_POST['admin_login_response'] = "SUCCESSFUL";
				Admin_login_attempts::save(0);
				$last_login = "UPDATE admins SET admin_last_login = NOW() WHERE admin_id = {$result['admin_id']}";
				$obj->execute($last_login);
				return true;
			endif;
			
		else:
			return false;
		endif;	
	
	}

	public static function status(){
		if(isset($_SESSION[Encryption::Encrypt(adminLogin)])):
			$status = $_SESSION[Encryption::Encrypt(adminLogin)];
			$status = count($status);
			return $status > 0 ? true : false;
		else:
			return false;
		endif;
	}

    public static function get($var){
		
		$data = $_SESSION[Encryption::Encrypt(adminLogin)][Encryption::Encrypt($var)];
		return Encryption::Decrypt($data);
		
	}
	
	public static function access($section){
	
		if(self::status()):
			
			$group_id = self::get("group_id");

			$permission = Admins_groups::select($group_id);
			$permission = $permission[0]['group_permission'];

			if($permission == "full"):
				return true;
			else:
				if(is_array($permission->access) && count($permission) > 0):
					return in_array($section, $permission->access) ? true : false;
				else:
					return false;
				endif;
			endif;
		else:
			return false;
		endif;
		
	}
	
    //-- crea instancia de la clase
	static private function getInstance($id=null) {
        return new self($id);
    }

}

?>