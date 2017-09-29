<?php
clearBuffer();
$modulename	= "Grupos y Permisos";
$group_id	= numParam('id');
$title		= $group_id > 0 ? "Modificar" : "Nuevo";
$data		= Admins_groups::select($group_id);

if(is_array($data) && count($data) > 0 && empty($_POST)):
	$_POST = $data[0];
else:
	$fields = Admins_groups::getfields();
	foreach($fields as $k => $v):
		if(isset($_POST[$k])):
			$value = empty($_POST[$k]) ? NULL : $_POST[$k];
		else:
			$value = NULL;
		endif;
		$_POST[$k] = $value;
	endforeach;
	$_POST['group_status'] = $_POST['group_status'] == NULL ? 1 : $_POST['group_status'];
endif;
$callback	= array(
	"success"	=> "admins_groups.view.php",
	"error"		=> "admins_groups.form.php"
);
?>
<ul class="breadcrumb">
	<li><a href="" class="glyphicons home" onclick="module('dashboard');return!1;"><i></i> <?php echo sysName;?></a></li>
	<li class="divider"></li>
	<li><a href="" onclick="module('admins_groups&page=<?php echo pageNumber();?>');return!1;"><?php echo $modulename;?></a></li>
	<li class="divider"></li>
	<li><?php echo $title; ?></li>
</ul>
<div class="separator"></div>
<div class="heading-buttons">
	<h3 class="glyphicons th-list" style="width:50% !important;"><i></i><a href="" onclick="module('admins_groups&page=<?php echo pageNumber();?>');return!1;"><?php echo htmlspecialchars($modulename);?></a> &gt; <?php echo $title;?></h3>
	<div class="buttons pull-right">
		<a href="" class="btn btn-primary btn-icon glyphicons circle_arrow_left" onclick="module('admins_groups&page=<?php echo pageNumber();?>');return!1;"><i></i>Volver</a>
	</div>
</div>
<div class="separator"></div>
<form class="form-horizontal" style="margin-bottom: 0;" id="admins_groups_form" name="admins_groups_form" method="post" autocomplete="off" onsubmit="savedata('admins_groups');return!1;">
	<div class="well" style="padding-bottom: 20px; margin: 0;">
		<h4>Informaci&oacute;n de <?php echo $modulename;?></h4>
		<?php Message::alert();?>
		<hr class="separator" />
		<div class="row-fluid">
		<div class="span6">
				<div class="control-group<?php echo isset($error['group_name']) ? " error" : "";?>">
					<label class="control-label" for="group_name">Nombre</label>
					<div class="controls">
						<input class="" id="group_name" name="group_name" value="<?php echo htmlspecialchars($_POST['group_name']);?>" type="text" style="color:#000;" />

						<?php
						if(isset($error['group_name'])):
						?>
						<p class="error help-block"><span class="label label-important"><?php echo $error['group_name'];?></span></p>
						<?php
						endif;
						?>
					</div>
				</div>

				<div class="control-group<?php echo isset($error['group_permission']) ? " error" : "";?>">
					<label class="control-label" for="group_permission">Permisos</label>
					<div class="controls">
						<?php
						if(Login::get("group_isroot")==1):

							$tables = array(
								"admins_groups" => "Grupos",
								"admins" => "Administradores",
								"asistencias" => "Asistencias",
								"clientes" => "Clientes",
								"contacto" => "Contacto",
								"discotecas" => "Discotecas",
								"eventos" => "Eventos",
								"publicidad" => "Publicidad",
								"usuarios" => "Usuarios",
								"visitas" => "Visitas"

							);

							$permission = @json_decode(stripslashes($_POST['group_permission']));
							$option_disabled = $_POST['group_isroot'] == 1 ? true : false;
						?>
						<table width="50%" border="0" cellspacing="0" cellpadding="5">
							<tr>
								<th style="border-bottom:1px solid #ccc;" width="202" scope="col">&nbsp;</th>
								<th style="border-bottom:1px solid #ccc;" width="66" scope="col">Crear</th>
								<th style="border-bottom:1px solid #ccc;" width="66" scope="col">Modificar</th>
								<th style="border-bottom:1px solid #ccc;" width="66" scope="col">Eliminar</th>
							</tr>
							<?php
							$t=0;
							foreach($tables as $tk => $tv):
								$t++;
								$insert_checked = false;
								$update_checked = false;
								$delete_checked = false;

								if($_POST['group_isroot'] == 1):
									$insert_checked = true;
									$update_checked = true;
									$delete_checked = true;
								else:
									if($permission instanceof stdClass):
										eval('$insert_checked = $permission->' . $tk . '->insert == 1 ? true : false;');
										eval('$update_checked = $permission->' . $tk . '->update == 1 ? true : false;');
										eval('$delete_checked = $permission->' . $tk . '->delete == 1 ? true : false;');
									endif;
								endif;

								$border = $t < count($tables) ? 'border-bottom:1px solid #ccc;' : '';
							?>
							<tr>
								<th scope="row" style="<?php echo $border;?> text-align:left;"><?php echo htmlspecialchars($tv);?></th>
								<td style="<?php echo $border;?>" align="center"><input type="checkbox" name="<?php echo $tk;?>_permission_insert" id="<?php echo $tk;?>_permission_insert" value="1"<?php if($option_disabled){?> disabled="disabled"<?php } ?><?php if($insert_checked){?> checked="checked"<?php } ?> /></td>
								<td style="<?php echo $border;?>" align="center"><input type="checkbox" name="<?php echo $tk;?>_permission_update" id="<?php echo $tk;?>_permission_update" value="1"<?php if($option_disabled){?> disabled="disabled"<?php } ?><?php if($update_checked){?> checked="checked"<?php } ?> /></td>
								<td style="<?php echo $border;?>" align="center"><input type="checkbox" name="<?php echo $tk;?>_permission_delete" id="<?php echo $tk;?>_permission_delete" value="1"<?php if($option_disabled){?> disabled="disabled"<?php } ?><?php if($delete_checked){?> checked="checked"<?php } ?> /></td>
							</tr>
							<?php
							endforeach;
							?>
						</table>
						<?php
						endif;
						?>
					</div>
				</div>

				<div class="control-group<?php echo isset($error['group_isroot']) ? " error" : "";?>">
					<label class="control-label" for="group_isroot">Superadmin</label>
					<div class="controls">
						<input class="" id="group_isroot" name="group_isroot" value="1" type="checkbox"<?php if($_POST['group_isroot'] == 1){?> checked="checked"<?php } ?> />

						<?php
						if(isset($error['group_isroot'])):
						?>
						<p class="error help-block"><span class="label label-important"><?php echo $error['group_isroot'];?></span></p>
						<?php
						endif;
						?>
					</div>
				</div>

				<div class="control-group<?php echo isset($error['group_status']) ? " error" : "";?>">
					<label class="control-label" for="group_status">Activo</label>
					<div class="controls">
						<input class="" id="group_status" name="group_status" value="1" type="checkbox"<?php if($_POST['group_status'] == 1){?> checked="checked"<?php } ?> />

						<?php
						if(isset($error['group_status'])):
						?>
						<p class="error help-block"><span class="label label-important"><?php echo $error['group_status'];?></span></p>
						<?php
						endif;
						?>
					</div>
				</div>

						</div>
				</div>
		<hr class="separator" />
		<div class="form-actions">
			<input type="hidden" name="id" value="<?php echo $group_id;?>" />
			<input type="hidden" name="page" value="<?php echo pageNumber();?>" />
			<input type="hidden" name="token" value="<?php echo token("Admins_groups::save(".$group_id.")");?>" />

			<input type="hidden" name="callback" value="<?php echo token(json_encode($callback));?>" />
			<button type="submit" class="btn btn-icon btn-primary glyphicons circle_ok"><i></i>Aceptar</button>
			<button type="button" class="btn btn-icon btn-default glyphicons circle_remove" onclick="module('admins_groups&page=<?php echo pageNumber();?>');return!1;"><i></i>Cancelar</button>
		</div>
	</div>
</form>


<script type="text/javascript">
	$(document).ready(function () {

	});

</script>
