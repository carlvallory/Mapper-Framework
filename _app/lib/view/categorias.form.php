<?php
$categoria_id = number(param('id'));
$_GET['categoria_parent'] = isset($_GET['categoria_parent']) ? $_GET['categoria_parent'] : 0;
$_POST['categoria_parent'] = isset($_POST['categoria_parent']) ? $_POST['categoria_parent'] : 0;
$page	 = pageNumber();
$parent	 = number($_GET['categoria_parent']) == 0 ? (number($_POST['categoria_parent']) == 0 ? 1 : number($_POST['categoria_parent'])) : number($_GET['categoria_parent']);

$title		= $categoria_id > 0 ? "Modificar categoría" : "Nueva categoría";
$data		= Categorias::select($categoria_id);

$categories = Categorias::tree($parent);

$tree = NULL;

foreach($categories as $category):
	$tree .= '<a href="" onclick="module(\'categories&page='.$page.'&parent='.$category['categoria_id'].'\');return!1;">'.$category['categoria_name'].'</a> &gt; ';
	$newparent = $category['categoria_id'];
endforeach;

$modulename = $tree;

if(is_array($data) && count($data) > 0 && !isset($_POST['categoria_id'])):
	$_POST = $data[0];
else:	
	$fields = Categorias::getfields();
	foreach($fields as $k => $v):
		if(isset($_POST[$k])):
			$value = empty($_POST[$k]) ? NULL : $_POST[$k];
		else:
			$value = NULL;
		endif;
		
		$_POST[$k] = $value;
	endforeach;
	$_POST['categoria_status'] = $_POST['categoria_status'] == NULL ? 1 : $_POST['categoria_status'];
endif;

$callback	= array(
	"success"	=> "categories.view.php",
	"error"		=> "categories.form.php"
);
?>
<ul class="breadcrumb">
	<li><a href="" class="glyphicons home" onclick="module('dashboard');return!1;"><i></i> <?php echo sysName;?></a></li>
	<li class="divider"></li>
	<li><?php echo $tree;?> <?php echo $title; ?></li>
</ul>
<div class="separator"></div>
<div class="heading-buttons">
	<h3 class="glyphicons sampler" style="width:80% !important;"><i></i><?php echo $tree;?> <?php echo $title;?></h3>
	<div class="buttons pull-right">
		<a href="" class="btn btn-primary btn-icon glyphicons circle_arrow_left" onclick="module('categories&page=<?php echo pageNumber();?>');return!1;"><i></i>Volver</a>
	</div>
</div>
<div class="separator"></div>
<form class="form-horizontal" style="margin-bottom: 0;" id="categories_form" name="categories_form" method="post" action="js/save" autocomplete="off" enctype="multipart/form-data" onsubmit="savedata('categories');return!1;">
	<div class="well" style="padding-bottom: 20px; margin: 0;">
		<h4>Información de la categoría</h4>
		<?php Message::alert();?>
		<hr class="separator" />
		<div class="row-fluid">
		<div class="span6">
				<div class="control-group<?php echo isset($error['categoria_name']) ? " error" : "";?>">
					<label class="control-label" for="categoria_name">Nombre</label>
					<div class="controls">
						<input class="focus" id="categoria_name" name="categoria_name" value="<?php echo htmlspecialchars($_POST['categoria_name']);?>" type="text" style="color:#000;" />
						
						<?php
						if(isset($error['categoria_name'])):
						?>
						<p class="error help-block"><span class="label label-important"><?php echo $error['categoria_name'];?></span></p>
						<?php
						endif;
						?>
					</div>
				</div>
				
				<div class="control-group<?php echo isset($error['categoria_status']) ? " error" : "";?>">
					<label class="control-label" for="categoria_status">Activo</label>
					<div class="controls">
						<input class="" id="categoria_status" name="categoria_status" value="<?php echo htmlspecialchars($_POST['categoria_status']);?>" type="checkbox" style="color:#000;"<?php if($_POST['categoria_status'] == 1){?> checked="checked"<?php } ?> />
						
						<?php
						if(isset($error['categoria_status'])):
						?>
						<p class="error help-block"><span class="label label-important"><?php echo $error['categoria_status'];?></span></p>
						<?php
						endif;
						?>
					</div>
				</div>

						</div>
				</div>
		<hr class="separator" />		
		<div class="form-actions">
			<input type="hidden" name="id" value="<?php echo $categoria_id;?>" />
			<input type="hidden" name="categoria_parent" value="<?php echo $parent;?>" />
			<input type="hidden" name="token" value="<?php echo token("Categorias::save(".$categoria_id.")");?>" />
			<input type="hidden" name="callback" value="<?php echo token(json_encode($callback));?>" />
			<button type="submit" class="btn btn-icon btn-primary glyphicons circle_ok"><i></i>Aceptar</button>
			<button type="button" class="btn btn-icon btn-default glyphicons circle_remove" onclick="module('categories&page=<?php echo pageNumber();?>');return!1;"><i></i>Cancelar</button>
		</div>
	</div>
</form>
