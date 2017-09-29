<?php
require_once('../_app/init.php');
if(!login()):
	print 'No tiene permisos para esta secci&oacute;n.';
	exit();
endif;

$record_set = "";
$checkbox_label = array();
$checkbox_img_true = '<span class="btn-action glyphicons ok btn-success"><i></i></span>';
$checkbox_img_false = '<span class="btn-action glyphicons remove btn-danger"><i></i></span>';
/*
Verificamos que la tabla exista
*/
$obj = new DB();
$table = "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '" . DBName . "' AND TABLE_NAME = '".param('module')."'";
$table = $obj->execute($table);
if(haveRows($table)):
	//Configuramos los titulos para las columnas
	$columns = "SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DBName . "' AND TABLE_NAME = '{$table[0]['TABLE_NAME']}'";
	$columns = $obj->execute($columns);
	if(is_array($columns) && count($columns)):
		foreach($columns as $column):
			if(strpos($column['COLUMN_NAME'], "_status") === false &&
			   strpos($column['COLUMN_NAME'], "_hidden") === false &&
			   strpos($column['COLUMN_NAME'], "_timestamp") === false &&
			   strpos($column['COLUMN_NAME'], "_clave") === false &&
			   strpos($column['COLUMN_NAME'], "_password") === false &&
			   strpos($column['COLUMN_NAME'], "_file_name") === false &&
			   strpos($column['COLUMN_NAME'], "_parent") === false &&
			   strpos($column['COLUMN_NAME'], "_left") === false &&
			   strpos($column['COLUMN_NAME'], "_right") === false &&
			   strpos($column['COLUMN_NAME'], "_image_small_path") === false &&
			   strpos($column['COLUMN_NAME'], "_image_small_url") === false &&
			   strpos($column['COLUMN_NAME'], "_image_big_url") === false &&
			   strpos($column['COLUMN_NAME'], "_image_big_path") === false &&
			   strpos($column['COLUMN_NAME'], "_file_path") === false):
				if(strpos($column['COLUMN_NAME'], "_image_small_url") === false  ||
					strpos($column['COLUMN_NAME'], "_image_big_url") === false  ||
					strpos($column['COLUMN_NAME'], "_file_url") === false):
					$field_label 	= "Link";
				else:
					$field_label 	= $column['COLUMN_NAME'];
				endif;
				if(strlen($column['COLUMN_COMMENT']) > 0):
					$column_config = @json_decode("{" . utf8_encode($column['COLUMN_COMMENT']) . "}");
					if($column_config instanceof stdClass):
						$field_label	= isset($column_config->label)	 ? $column_config->label 	: $field_label;
						$label[] = ucwords($field_label);
						if($column_config->type=="checkbox"):
							$checkbox_label[] = $field_label;
						endif;
					endif;
					$record_set[] = $column['COLUMN_NAME'];
				endif;
			endif;
		endforeach;
	endif;
	/*
	Instanciamos la clase
	*/
	$className = ucfirst(param('module'));
	$instance = new $className;
	$data = $instance->select(numParam('id'));
	if(haveRows($data)):
		echo '<table class="table table-bordered table-hover"><tbody>';
		foreach ($data as $rs):
			for($position=0;$position<=count($record_set)-1;$position++):
				$foreign = "SELECT REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_SCHEMA IS NOT NULL AND TABLE_SCHEMA = '" . DBName . "' AND TABLE_NAME = '".param('module')."' AND REFERENCED_COLUMN_NAME = '{$record_set[$position]}';";
				$fk = $obj->execute($foreign);
				if(haveRows($fk)):
					$fk_table = $fk[0]['REFERENCED_TABLE_NAME'];
					$fk_field_id = $fk[0]['REFERENCED_COLUMN_NAME'];
					$columns = null;
					$columns = "SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DBName . "' AND TABLE_NAME = '{$fk_table}'";
					$columns = $obj->execute($columns);
					if(is_array($columns) && count($columns)>0):
						foreach($columns as $column):
							if(strlen($column['COLUMN_COMMENT']) > 0):
								$column_config = @json_decode("{" . $column['COLUMN_COMMENT'] . "}");
								if($column_config instanceof stdClass):
									if(strpos($column['COLUMN_NAME'], "_status") === false):
										if(isset($column_config->select)):
											$field_name	= $column['COLUMN_NAME'];
										endif;
									endif;
								endif;
							endif;
						endforeach;
					endif;
					$fk_className = ucfirst($fk_table);
					$fk_instance = new $fk_className;
					$fk_data = $fk_instance->select($rs[$record_set[$position]]);
					echo '<tr><td><strong>'.ucwords($label[$position]).'</strong></td><td>'.$fk_data[0][$field_name].'</td></tr>';
				else:
					if(in_array($label[$position], $checkbox_label)):
						$checkbox_img = ($rs[$record_set[$position]]==1)? $checkbox_img_true : $checkbox_img_false;
						echo '<tr><td><strong>'.ucwords($label[$position]).'</strong></td><td>'.$checkbox_img.'</td></tr>';
					else:
						if (!filter_var($rs[$record_set[$position]], FILTER_VALIDATE_URL) === false):
							echo '<tr><td><strong>'.ucwords($label[$position]).'</strong></td><td><a href="'.$rs[$record_set[$position]].'" target="_blank">'.$rs[$record_set[$position]].'</a></td></tr>';
						else:
							if(isJSON($rs[$record_set[$position]])):
								$jsonObject = json_decode(stripslashes($rs[$record_set[$position]]));
								$result_data = '<ul>';
								foreach($jsonObject as $obj => $k):
									$result_data .= '<li><strong style="text-transform:capitalize">'.$obj.':</strong> '.$k.'</li>';
								endforeach;
								$result_data .= '</ul>';
								echo '<tr><td><strong>'.ucwords($label[$position]).'</strong></td><td>'.nl2br($result_data).'</td></tr>';
							else:
								echo '<tr><td><strong>'.ucwords($label[$position]).'</strong></td><td>'.nl2br($rs[$record_set[$position]]).'</td></tr>';
							endif;
						endif;
					endif;
				endif;
			endfor;
		endforeach;
		echo '</tbody></table>';
	endif;
else:
	print 'No se pudo encontrar el modulo.';
	exit();
endif;

?>
