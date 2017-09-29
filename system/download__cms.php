<?php
require_once('../_app/init.php');
if(!login()):
	print 'No tiene permisos para esta secci&oacute;n.';
	exit();
endif;
/*
Configuracion para la exportacion
*/
$format_file = (strtolower(param('format')) == 'csv' || strtolower(param('format')) == 'txt' || strtolower(param('format')) == 'xls')? strtolower(param('format')) : 'xls';
if($format_file=="xls"):
	$table_ini = "<table>";
	$field_ini = "	<tr>";
	$field_sep_ini = "		<td>";
	$field_sep = "</td>";
	$field_end = "	</tr>";
	$table_end = "</table>";
else:
	$table_ini = "";
	$field_ini = "";
	$field_sep_ini = "";
	$field_sep = ";";
	$field_end = "
	";
	$table_end = "";
endif;
$row = "";
$record_set = "";
/*
Verificamos que la tabla exista
*/
$obj = new DB();
$table = "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '" . DBName . "' AND TABLE_NAME = '".param('export')."'";
$table = $obj->execute($table);
if(haveRows($table)):

	//Configuramos los titulos para las columnas
	$columns = "SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DBName . "' AND TABLE_NAME = '{$table[0]['TABLE_NAME']}'";
	$columns = $obj->execute($columns);
	if(is_array($columns) && count($columns)):
		$row .= $table_ini;
		foreach($columns as $column):
			if(strpos($column['COLUMN_NAME'], "_status") === false &&
				strpos($column['COLUMN_NAME'], "_hidden") === false &&
				//strpos($column['COLUMN_NAME'], "_timestamp") === false &&
				strpos($column['COLUMN_NAME'], "_clave") === false &&
			    strpos($column['COLUMN_NAME'], "_password") === false &&
				strpos($column['COLUMN_NAME'], "_file_name") === false &&
			   	strpos($column['COLUMN_NAME'], "_parent") === false &&
			   	strpos($column['COLUMN_NAME'], "_left") === false &&
			   	strpos($column['COLUMN_NAME'], "_right") === false &&
			   	strpos($column['COLUMN_NAME'], "_image_small_path") === false &&
			   	strpos($column['COLUMN_NAME'], "_image_big_path") === false &&
			   	strpos($column['COLUMN_NAME'], "_file_path") === false):
				if(strpos($column['COLUMN_NAME'], "_image_small_url") === false  ||
					strpos($column['COLUMN_NAME'], "_image_big_url") === false  ||
					strpos($column['COLUMN_NAME'], "_file_url") === false):
					$field_label 	= "";
				else:
					$field_label 	= $column['COLUMN_NAME'];
				endif;
				if(strlen($column['COLUMN_COMMENT']) > 0):
					$column_config = @json_decode("{" . utf8_encode($column['COLUMN_COMMENT']) . "}");
					if($column_config instanceof stdClass):
						$field_label	= isset($column_config->label)	 ? $column_config->label 	: $field_label;
						$row .= $field_sep_ini.ucwords(mb_convert_encoding(utf8_decode($field_label),'utf-16','utf-8')).$field_sep;
					endif;
					$record_set[] = $column['COLUMN_NAME'];
				endif;
			endif;
		endforeach;
		//$row = substr($row, 0, -1);
		//$row .= $field_end;
	endif;
	/*
	Instanciamos la clase
	*/
	$className = ucfirst(param('export'));
	$instance = new $className;
	$data = $instance->get();
	if(haveRows($data)):
		//$row .= $table_ini;
		foreach ($data as $rs):
			$row .= $field_ini;
			for($position=0;$position<=count($record_set)-1;$position++):
				$row .= $field_sep_ini.$rs[$record_set[$position]].$field_sep;
			endfor;
			$row .= $field_end;
		endforeach;
	endif;
	$row = ($format_file=="xls")? $row : substr($row, 0, -1);
	$row .= ($format_file=="xls")? $table_end : $field_end;
else:
	$row = "No existe la tabla.";
endif;

$file = param('export').'_'.date('d_m_Y_H_i_s').'.'.$format_file;
$path = '../download/';
$handle = fopen($path.$file,'w+');
fwrite($handle, utf8_decode($row));
fclose($handle);

if($format_file=="xls"):
	header("Content-type: application/vnd.ms-excel; name='excel'; charset=utf-16");
	header("Content-Transfer-Encoding: binary ");
else:
	header("Content-type: application/octet-stream");
endif;
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-disposition: attachment; filename=$file");
header("Pragma: no-cache");
header("Expires: 0");
readfile($path.$file);

?>