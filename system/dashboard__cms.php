<?php
include_once('inc/init.php');
if(!login()):
	redirect(baseAdminURL);
endif;
?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>    <html class="lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>    <html class="lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html>
<!--<![endif]-->
<head>
<title><?php echo sysName . " " . sysVersion;?></title>
<?php include('inc/cms_head.php');?>
</head>
<body><!-- Start Content -->
<div class="container-fluid">
	<div class="navbar main">
		<?php include( pathToView . 'cms.menu_top.php' );?>
	</div>
	<div id="wrapper">
		<div id="menu" class="hidden-phone">
			<div id="menuInner">
				<?php include( pathToView . 'cms.menu_left.php');?>
			</div>
		</div>
		<div id="content">
			<?php include( pathToView . 'dashboard.view.php');?>
		</div>
	</div>
</div>
<!-- JQueryUI v1.9.2 -->
<script src="theme/scripts/jquery-ui-1.9.2.custom/js/jquery-ui-1.9.2.custom.min.js"></script>
<!-- JQueryUI Touch Punch -->
<!-- small hack that enables the use of touch events on sites using the jQuery UI user interface library -->
<script src="theme/scripts/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js"></script>
<!-- MiniColors -->
<script src="theme/scripts/jquery-miniColors/jquery.miniColors.js"></script>
<!-- Themer -->
<script>var themerPrimaryColor = '#DA4C4C';</script>
<script src="theme/scripts/jquery.cookie.js"></script>
<script src="theme/scripts/themer.js"></script>
<!-- Resize Script -->
<script src="theme/scripts/jquery.ba-resize.js"></script>
<!-- Uniform -->
<script src="theme/scripts/pixelmatrix-uniform/jquery.uniform.min.js"></script>
<!-- Bootstrap Script -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- Bootstrap Extended -->
<script src="bootstrap/extend/bootstrap-select/bootstrap-select.js"></script>
<script src="bootstrap/extend/bootstrap-toggle-buttons/static/js/jquery.toggle.buttons.js"></script>
<script src="bootstrap/extend/bootstrap-hover-dropdown/twitter-bootstrap-hover-dropdown.min.js"></script>
<script src="bootstrap/extend/jasny-bootstrap/js/jasny-bootstrap.min.js" type="text/javascript"></script>
<script src="bootstrap/extend/jasny-bootstrap/js/bootstrap-fileupload.js" type="text/javascript"></script>
<script src="bootstrap/extend/bootbox.js" type="text/javascript"></script>
<script src="bootstrap/extend/bootstrap-wysihtml5/js/wysihtml5-0.3.0_rc2.min.js" type="text/javascript"></script>
<script src="bootstrap/extend/bootstrap-wysihtml5/js/bootstrap-wysihtml5-0.0.2.js" type="text/javascript"></script>
<!-- Custom Onload Script -->
<script src="theme/scripts/load.js"></script>
<?php
checkConfig();
?>
<div id="viewModal" class="modal fade hide" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Datos del Registro</h4>
      </div>
      <div class="modal-body">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
</body>
</html>
