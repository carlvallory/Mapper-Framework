<?php
$params = "";
foreach($_POST as $k => $v):
	if($k != "id" && $k != "option"):
		$params .= "&{$k}=" . urlencode($v);
	endif;
endforeach;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Upload:Success</title>
<script type="text/javascript">with(window.parent){module('<?php echo $_POST['option'];?><?php echo $params;?>');}</script>
</head>
<body>
</body>
</html>