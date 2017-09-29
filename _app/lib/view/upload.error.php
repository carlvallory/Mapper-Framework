<?php
$params = "";
$errors = "";
foreach($_POST as $k => $v):
	if($k != "id" && $k != "option"):
		$params .= "{$k}=".urlencode($v)."&";
	endif;
endforeach;
$params = substr($params,0,-1);

if(is_array($error)):
	foreach($error as $ek => $ev):
		$errors .= "&error_{$ek}={$ev}";
	endforeach;
endif;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Upload:error</title>
<script type="text/javascript">with(window.parent){create('<?php echo $_POST['option'];?>','<?php echo $_POST['id'].'&error=1&'.$params.$errors;?>');}</script>
</head>
<body>
</body>
</html>