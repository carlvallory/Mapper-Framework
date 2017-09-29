<?php
class Admins_groups extends Mysql{
	protected $tableName	= "admins_groups";
	protected $primaryKey = "group_id";
	protected $fields	= array(
		"group_name"	=> array("type" => "varchar",	"length"=> 128, "required" => "1", "validation" => "none"),
		"group_permission"	=> array("type" => "text",	"length"=> 65535, "required" => "0", "validation" => "none"),
		"group_isroot"	=> array("type" => "tinyint", "required" => "0", "validation" => "none"),
		"group_status"	=> array("type" => "tinyint", "required" => "0", "validation" => "none"),
		"group_hidden"	=> array("type" => "tinyint", "required" => "0", "validation" => "none"),
		"group_timestamp"	=> array("type" => "timestamp", "required" => "0", "validation" => "none")
	);

	public function __construct(){
	}

	//-- inserta o modifica un registro

	public static function save($id){

		$obj = new self($id);

		$obj->fields['group_name']['value']		= $_POST['group_name'];
		$obj->fields['group_status']['value']	= isset($_POST['group_status']) ? number($_POST['group_status']) : 0;
		$obj->fields['group_hidden']['value']	= '0';
		$obj->fields['group_isroot']['value']	= isset($_POST['group_isroot']) ? number($_POST['group_isroot']) : 0;
		$obj->fields['group_timestamp']['value']	=	date("Y-m-d H:i:s");

		if(Login::get("group_isroot") == 1):
			foreach($_POST as $pk => $pv):
				if(strpos($pk, "_permission_") !== false):
					$key = explode("|", str_replace("_permission_","|",$pk));
					$permission[$key[0]][$key[1]] = true;
				endif;
			endforeach;

			$permission['group']['update'] = true;
			$obj->fields['group_permission']['value'] = json_encode($permission);
		endif;

		if($id > 0):

			$res = self::select($id);
			if(Login::get("group_isroot") == 1):
				$obj->fields['group_isroot']['value'] = isset($_POST['group_isroot']) ? number($_POST['group_isroot']) : 0;
			else:
				$obj->fields['group_isroot']['value'] = $res[0]['group_isroot'];
			endif;
			if(!isset($permission)):
				$obj->fields['group_permission']['value'] = $res[0]['group_permission'];
			endif;

			if(count($res) > 0 && is_array($res)):

				$obj->fields['group_random_seed']['value'] = $res[0]['group_random_seed'];

			endif;

		endif;

		if($obj->validate($obj,$id)):
			$obj->update($obj, $id);
		else:
			return $obj->error;
		endif;
	}

	//-- oculta o elimina un registro
	public static function delete($id){
		$obj = new self();

		$admin = self::select($id);

		if(count($admin) > 0):
			$root = $admin[0]['group_isroot'] == 1 ? true : false;
		else:
			$root = false;
		endif;

		if(!$root):
			$delete = "UPDATE admins_groups SET group_hidden = 1 WHERE " . $obj->primaryKey . " = {$id}";
			$obj->Execute($delete);
		else:
			setApplicationJavascript();
			print "alert('El usuario es ROOT y no se puede eliminar');";
			exit;
		endif;
	}

	public static function get($where=null,$order=null){
		$obj = new self();
		$whr = $where == null ? "" : "{$where} AND ";
		$ord = $order == null ? "" : " ORDER BY {$order}";
		$sql = "SELECT * FROM " . $obj->tableName . " WHERE {$whr}group_status = 1 AND group_hidden = 0{$ord}";
		return $obj->execute($sql);
	}

	public static function set($field, $value, $where=null){
		$obj = new self();
		$obj->change($obj->tableName, $field, $value, $where);
	}

	public static function select($id){
		$obj = new self();
		return $obj->find($obj->tableName, $obj->primaryKey, $id);
	}

	public static function bulk($action, $ids){
		$obj = new self();
		$ids = json_decode($ids);

		switch($action):
			//activar
			case "1":
				foreach($ids as $id):
					$obj->change($obj->tableName, "group_status", 1, $obj->primaryKey . " = {$id}");
				endforeach;
				break;
			//desactivar
			case "2":
				foreach($ids as $id):
					$obj->change($obj->tableName, "group_status", 0, $obj->primaryKey . " = {$id}");
				endforeach;
				break;
			//eliminar
			case "3":
				foreach($ids as $id):
					self::delete($id);
				endforeach;
				break;
		endswitch;

	}

	public static function permission($table,$action=null){
		if(login()):
			if(Login::get("group_isroot") == 1):
				return true;
			else:
				$data = self::select(Login::get("group_id"));

				if(haveRows($data)):

					$data = $data[0];

					$permission = @json_decode($data['group_permission']);

					if($permission instanceof stdClass):

						if($action == null):

							eval('$allow_insert = $permission->' . $table . '->insert == 1 ? true : false;');
							eval('$allow_update = $permission->' . $table . '->update == 1 ? true : false;');
							eval('$allow_delete = $permission->' . $table . '->delete == 1 ? true : false;');
							return $allow_insert || $allow_update || $allow_delete ? true : false;

						else:

							eval('$allow = $permission->' . $table . '->' . strtolower($action) . ' == 1 ? true : false;');
							return $allow;

						endif;

					else:
						return false;
					endif;

				else:
					return false;
				endif;

			endif;
		else:
			return false;
		endif;
	}

	public static function combobox($selected=null,$onchange=null){
		$obj = new self();
		$fsel = ($selected == null || $selected == 0) ? ' selected="selected"' : '';
		$list = "SELECT group_id, group_name FROM admins_groups WHERE group_status = 1 AND group_hidden = 0 ORDER BY group_name ASC";
		$list = $obj->exec($list);
		print '<select name="group_id" id="groups_combo" onchange="'.$onchange.'" style="color:#000;">';
			print '<option value=""'.$fsel.'>Seleccionar</option>';
			if(is_array($list) && count($list) > 0):
				foreach($list as $dat):
					$select = $dat['group_id'] == $selected ? ' selected="selected"' : "";
					print '<option value="'.$dat['group_id'].'"'.$select.'>'.htmlspecialchars($dat['group_name']).'</option>';
				endforeach;
			endif;
		print '</select>';
	}

	public static function getfields(){
		$obj = new self();
		return $obj->fields;
	}

}
?>
