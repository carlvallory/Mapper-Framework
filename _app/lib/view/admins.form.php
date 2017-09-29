<?php
$modulename	= "Administradores";
$admin_id	= numParam('id');
$title		= $admin_id > 0 ? "Modificar Administrador" : "Nuevo Administrador";
$data		= Admins::select($admin_id);
if(is_array($data) && count($data) > 0 && empty($_POST)):
	$_POST = $data[0];
	$_POST['admin_password'] = NULL;
else:
	$fields = Admins::getfields();
	foreach($fields as $k => $v):

		if(isset($_POST[$k])):
			$value = empty($_POST[$k]) ? NULL : $_POST[$k];
		else:
			$value = NULL;
		endif;

		$_POST[$k] = $value;
	endforeach;
	$_POST['admin_status'] = $_POST['admin_status'] ==  NULL ? 1 : $_POST['admin_status'];
endif;
$callback	= array(
	"success"	=> "admins.view.php",
	"error"		=> "admins.form.php"
);
?>
<ul class="breadcrumb">
	<li><a href="" class="glyphicons home" onclick="module('dashboard');return!1;"><i></i> <?php echo sysName;?></a></li>
	<li class="divider"></li>
	<li><a href="" onclick="module('admins&page=<?php echo pageNumber();?>');return!1;"><?php echo $modulename;?></a></li>
	<li class="divider"></li>
	<li><?php echo $title; ?></li>
</ul>
<div class="separator"></div>
<div class="heading-buttons">
	<h3 class="glyphicons parents" style="width:400px !important;"><i></i> <?php echo $title;?></h3>
	<div class="buttons pull-right">
		<a href="" class="btn btn-primary btn-icon glyphicons circle_arrow_left" onclick="module('admins&page=<?php echo pageNumber();?>');return!1;"><i></i>Volver</a>
	</div>
</div>
<div class="separator"></div>
<form class="form-horizontal" style="margin-bottom: 0;" id="admins_form" name="admins_form" method="post" autocomplete="off" onsubmit="savedata('admins');return!1;">
	<div class="well" style="padding-bottom: 20px; margin: 0;">
		<h4>Información del Administrador</h4>
		<?php Message::alert();?>
		<hr class="separator" />
		<div class="row-fluid">
		<div class="span6">
			<div class="control-group<?php echo isset($error['group_id']) ? " error" : "";?>">
				<label class="control-label" for="group_id">Grupo</label>
				<div class="controls">
					<?php Admins_groups::combobox($_POST['group_id']);?>
					<?php
					if(isset($error['group_id'])):
					?>
					<p class="error help-block"><span class="label label-important"><?php echo $error['group_id'];?></span></p>
					<?php
					endif;
					?>
				</div>
			</div>

				<div class="control-group<?php echo isset($error['admin_names']) ? " error" : "";?>">
					<label class="control-label" for="admin_names">Nombre</label>
					<div class="controls">
						<input class="" id="admin_names" name="admin_names" value="<?php echo htmlspecialchars($_POST['admin_names']);?>" type="text" style="color:#000;" />

						<?php
						if(isset($error['admin_names'])):
						?>
						<p class="error help-block"><span class="label label-important"><?php echo $error['admin_names'];?></span></p>
						<?php
						endif;
						?>
					</div>
				</div>

				<div class="control-group<?php echo isset($error['admin_email']) ? " error" : "";?>">
					<label class="control-label" for="admin_email">E-mail</label>
					<div class="controls">
						<input class="" id="admin_email" name="admin_email" value="<?php echo htmlspecialchars($_POST['admin_email']);?>" type="text" style="color:#000;" />

						<?php
						if(isset($error['admin_email'])):
						?>
						<p class="error help-block"><span class="label label-important"><?php echo $error['admin_email'];?></span></p>
						<?php
						endif;
						?>
					</div>
				</div>

				<div class="control-group<?php echo isset($error['admin_password']) ? " error" : "";?>">
					<label class="control-label" for="admin_password">Clave</label>
					<div class="controls">
						<input class="" id="admin_password" name="admin_password" value="<?php echo htmlspecialchars($_POST['admin_password']);?>" type="password" style="color:#000;" />

						<?php
						if(isset($error['admin_password'])):
						?>
						<p class="error help-block"><span class="label label-important"><?php echo $error['admin_password'];?></span></p>
						<?php
						endif;
						?>
					</div>
				</div>

				<div class="control-group<?php echo isset($error['admin_status']) ? " error" : "";?>">
					<label class="control-label" for="admin_status">Activo</label>
					<div class="controls">
						<input class="" id="admin_status" name="admin_status" value="1" type="checkbox" style="color:#000;"<?php if($_POST['admin_status'] == 1){?> checked="checked"<?php } ?> />

						<?php
						if(isset($error['admin_status'])):
						?>
						<p class="error help-block"><span class="label label-important"><?php echo $error['admin_status'];?></span></p>
						<?php
						endif;
						?>
					</div>
				</div>


						</div>
				</div>
		<hr class="separator" />
		<div class="form-actions">
			<input type="hidden" name="id" value="<?php echo $admin_id;?>" />
			<input type="hidden" name="token" value="<?php echo token("Admins::save(".$admin_id.")");?>" />
			<input type="hidden" name="callback" value="<?php echo token(json_encode($callback));?>" />
			<button type="submit" class="btn btn-icon btn-primary glyphicons circle_ok"><i></i>Aceptar</button>
			<button type="button" class="btn btn-icon btn-default glyphicons circle_remove" onclick="module('admins&page=<?php echo pageNumber();?>');return!1;"><i></i>Cancelar</button>
		</div>
	</div>
</form>
