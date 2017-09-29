<?php
$_GET['query'] = isset($_GET['query']) ? $_GET['query'] : "";
$page	 = pageNumber();
$search	 = addslashes($_GET['query']);
$search	 = strlen(trim($search)) > 0 ? " AND admin_id = '{$search}' OR admin_names LIKE '{$search}' OR admin_email LIKE '{$search}'" : "";
$isroot = Login::get("group_isroot");
$filter = $isroot == 1 ? "" : "admin_id = " . Login::get("admin_id") . " AND ";
$listing = new Listing();
$listing->pgclick("module('admins&page=%s');return!1;");
$listing = $listing->get("admins", 20, NULL, $page, "WHERE {$filter} admin_hidden = 0 {$search} ORDER BY admin_id DESC");
?>
<ul class="breadcrumb">
	<li><a href="" class="glyphicons home" onclick="module('dashboard');return!1;"><i></i> <?php echo sysName;?></a></li>
	<li class="divider"></li>
	<li>Administradores</li>
</ul>
<div class="separator"></div>
<div class="heading-buttons">
	<h3 class="glyphicons parents" style="width:200px !important;"><i></i> Administradores</h3>
	<div class="buttons pull-right">
		<a href="" class="btn btn-primary btn-icon glyphicons circle_plus" onclick="create('admins',0);return!1;"><i></i>Nuevo</a>
	</div>
</div>
<div class="separator"></div>
<div class="innerLR">
<form name="searchform" id="searchform" method="get" onsubmit="module('admins&query='+$('#squery').val());return!1;">
<div class="input-append">
	<input class="span6" id="squery" name="query" type="text" value="<?php echo htmlspecialchars($_GET['query']);?>" placeholder="Buscar..." />
	<button class="btn" type="button"><i class="icon-search"></i></button>
</div>
</form>
<?php
if(is_array($listing['list']) && count($listing['list']) > 0):
?>
	<table class="table table-bordered table-condensed table-striped table-vertical-center checkboxs js-table-sortable">
		<thead>
			<tr>
				<th style="width: 1%;" class="uniformjs"><input type="checkbox" id="checkall_admins" name="checkall_admins" value="1" /></th>
				<th style="width: 1%;" class="center">ID</th>
				<th>Grupo</th>
				<th>Nombre</th>
				<th>E-mail</th>
				<th class="right" colspan="3">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
		<?php
		foreach($listing['list'] as $list):
		?>
			<tr class="selectable">
				<td class="center uniformjs"><input type="checkbox" name="check_admins_<?php echo $list['admin_id'];?>" value="<?php echo $list['admin_id'];?>" /></td>
				<td class="center"><?php echo $list['admin_id'];?></td>
				<?php $group = Admins_groups::select($list['group_id']);?>
				<td><strong><?php echo $group[0]['group_name'];?></strong></td>
				<td><strong><?php echo $list['admin_names'];?></strong></td>
				<td><?php echo $list['admin_email'];?></td>
				<td class="center" style="width: 150px;"><?php echo date('d/m/Y H:i:s', strtotime($list['admin_timestamp']));?></td>
				<td class="center" style="width: 80px;"><span class="label label-block label-<?php echo $list['admin_status'] == 1 ? "important" : "inverse";?>"><?php echo $list['admin_status'] == 1 ? "Activo" : "Inactivo";?></span></td>
				<td class="center" style="width: 60px;"><a href="" class="btn-action glyphicons pencil btn-success" onclick="create('admins',<?php echo $list['admin_id'];?>);return!1;"><i></i></a> <a href="" class="btn-action glyphicons remove_2 btn-danger" onclick="removedata('admins',<?php echo $list['admin_id'];?>,'view');return!1;"><i></i></a></td>
			</tr>
		<?php
		endforeach;
		?>
		</tbody>
	</table>
	<div class="separator top form-inline small">
		<div class="pull-left checkboxs_actions hide">
			<div class="row-fluid">
				<select style="color:#000;" onchange="bulkAction('admins',this);">
					<option value="0">Seleccionados</option>
					<option value="1">Activar</option>
					<option value="2">Desactivar</option>
					<option value="3">Eliminar</option>
				</select>
			</div>
		</div>
		<div class="pagination pull-right" style="margin: 0;">
			<?php echo $listing['navigation'];?>
		</div>
		<div class="clearfix"></div>
	</div>
<?php
else:
?>
<div class="alert alert-info">
<button type="button" class="close" data-dismiss="alert">&times;</button>
<strong>Sin datos</strong> No se encontraron registros</div>
<?php
endif;
?>
</div>
<br/>
<!-- End Content -->
