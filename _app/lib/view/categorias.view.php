<?php
$_GET['query'] = isset($_GET['query']) ? $_GET['query'] : "";
$_GET['parent'] = isset($_GET['parent']) ? $_GET['parent'] : 0;
$_POST['categoria_parent'] = isset($_POST['categoria_parent']) ? $_POST['categoria_parent'] : 0;
$page	 = pageNumber();
$search	 = addslashes($_GET['query']);
$search	 = strlen(trim($search)) > 0 ? " AND categoria_id = '{$search}' OR categoria_name LIKE '%{$search}%'" : "";
$parent	 = number($_GET['parent']) == 0 ? (number($_POST['categoria_parent']) == 0 ? 1 : number($_POST['categoria_parent'])) : number($_GET['parent']);

$tree = NULL;
$categories = Categorias::tree($parent);

foreach($categories as $category):
	$tree .= '<a href="" onclick="module(\'categories&page='.$page.'&parent='.$category['categoria_id'].'\');return!1;">'.$category['categoria_name'].'</a> &gt; ';
	$newparent = $category['categoria_id'];
endforeach;
$tree = substr($tree,0,-5);

$listing = new Listing();
$listing->pgclick("module('categories&page=%s');return!1;");
$listing = $listing->get("categories", 20, NULL, $page, "WHERE categoria_parent = {$parent} AND categoria_hidden = 0 {$search} ORDER BY categoria_id DESC");
?>
<ul class="breadcrumb">
	<li><a href="" class="glyphicons home" onclick="module('dashboard');return!1;"><i></i> <?php echo sysName;?></a></li>
	<li class="divider"></li>
	<li><?php echo $tree;?></li>
</ul>
<div class="separator"></div>
<div class="heading-buttons">
	<h3 class="glyphicons sampler" style="width:80% !important;"><i></i> <?php echo $tree;?></h3>
	<div class="buttons pull-right">
		<a href="" class="btn btn-primary btn-icon glyphicons circle_plus" onclick="create('categories','0&categoria_parent=<?php echo $newparent;?>');return!1;"><i></i>Nuevo</a>
	</div>
</div>
<div class="separator"></div>
<div class="innerLR">
<form name="searchform" id="searchform" method="get" onsubmit="module('categories&query='+$('#squery').val());return!1;">
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
				<th style="width: 1%;" class="uniformjs"><input type="checkbox" /></th>
				<th style="width: 1%;" class="center">ID</th>
				<th>Nombre</th>
				<th class="right" colspan="3">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
		<?php
		foreach($listing['list'] as $list):
		?>
			<tr class="selectable">
				<td class="center uniformjs"><input type="checkbox" name="check_<?php echo $list['categoria_id'];?>" /></td>
				<td class="center"><?php echo $list['categoria_id'];?></td>
				<td><strong><a href="" onclick="module('categories&page=<?php echo $page;?>&parent=<?php echo $list['categoria_id'];?>');return!1;"><?php echo $list['categoria_name'];?></a></strong></td>
				<td class="center" style="width: 150px;"><?php echo $list['categoria_timestamp'];?></td>
				<td class="center" style="width: 80px;"><span class="label label-block label-<?php echo $list['categoria_status'] == 1 ? "important" : "inverse";?>"><?php echo $list['categoria_status'] == 1 ? "Activo" : "Inactivo";?></span></td>
				<td class="center" style="width: 150px;">
					<a href="" title="Modificar" class="btn-action glyphicons pencil btn-success" onclick="create('categories','<?php echo $list['categoria_id'];?>&categoria_parent=<?php echo $list['categoria_parent'];?>');return!1;"><i></i></a>
					<a href="" title="Eliminar" class="btn-action glyphicons remove_2 btn-danger" onclick='removeserial({"option":"categories","id":"<?php echo $list['categoria_id'];?>","callback":"view", "categoria_parent":"<?php echo $list['categoria_parent'];?>"});return!1;'><i></i></a>
					&nbsp;&nbsp;
					
					<a href="" title="Productos" class="btn-action glyphicons more_items btn-warning" onclick="module('products&categoria_id=<?php echo $list['categoria_id'];?>');return!1;"><i></i></a>
				</td>
			</tr>
		<?php
		endforeach;
		?>
		</tbody>
	</table>
	<div class="separator top form-inline small">
		<div class="pull-left checkboxs_actions hide">
			<div class="row-fluid">
				<select style="color:#000;" onchange="checkedAction('categories',this);">
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