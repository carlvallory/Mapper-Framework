function module(){
	var t = arguments[0];
	var p = '';
	if(arguments.length > 1){
		p = '&p=' + arguments[1];
	}
	$.ajax({
	  type: "GET",
	  url: 'js/module?option='+t+p,
	  error: function(){
		alert('Error al ingresar al módulo.');
	  },
	  success: function(res, status, xhr){
		  var type = xhr.getResponseHeader("Content-type");
		  if(type != 'application/x-javascript'){
			$('#content').html(res);
			$('.focus').focus();
		  }
	  }
	}).done(function(){
		loaduniforms();
	});
}


function create(t,p){
	$.ajax({
	  type: "GET",
	  url: 'js/moduleform?option='+t+'&id='+p,
	  error: function(){
		alert('Error al ingresar al módulo.');
	  },
	  success: function(res, status, xhr){
		  var type = xhr.getResponseHeader("Content-type");
		  if(type != 'application/x-javascript'){
			$('#content').html(res);
			$('.focus').focus();
		  }
	  }
	}).done(function(){
		loaduniforms();
	});
}

function savedata(t){
	var param = $('#'+t+'_form').serializeArray();
	$.ajax({
	  type: "POST",
	  url: 'js/save',
	  data: param,
	  error: function(){
		alert('Error al ingresar al módulo.');
	  },
	  success: function(res, status, xhr){
		  var type = xhr.getResponseHeader("Content-type");
		  if(type != 'application/x-javascript'){
			$('#content').html(res);
			$('.focus').focus();
		  }
	  }
	}).done(function(){
		loaduniforms();
	});
}

function removedata(t,p,c){
	if(confirm('¿Está seguro que desea eliminar este registro?')){
		$.ajax({
		  type: "POST",
		  url: 'js/remove',
		  data: {option: t, id: p, callback: c},
		  error: function(){
			alert('Error al ingresar al módulo.');
		  },
		  success: function(res, status, xhr){
			  var type = xhr.getResponseHeader("Content-type");
			  if(type != 'application/x-javascript'){
				$('#content').html(res);
				$('.focus').focus();
			  }
		  }
		}).done(function(){
			loaduniforms();
		});
	}
}


function bulkAction(m,v){
	
	var value = v.options[v.selectedIndex].value;
	var serial = {};
	
	if(value != 0){
		
		if(value == 3){
			if(!confirm('¿Está seguro que desea eliminar los registros seleccionados?')){
				return false;
			}
		}
		
		serial["module"] = m;
		serial["action"] = value;
		
		$(':checkbox').each(function(){
			if(this.checked){
				serial[this.name] = this.value;
			}
		});
		
		$.ajax({
			type: "POST",
			url: 'js/bulk',
			data: serial,
			success: function(res, status, xhr){
				var type = xhr.getResponseHeader("Content-type");
				if(type != 'application/x-javascript'){
					module(m);
					$('.focus').focus();
				}
			},
			error: function(){
				alert('Error al ejecutar la consulta');
			}
		});
		
	}
	
}

function checkedAction(m,v){
	bulkAction(m,v);
}

function removeserial(serial){
	if(confirm('¿Está seguro que desea eliminar este registro?')){
		$.ajax({
		  type: "POST",
		  url: 'js/remove',
		  data: serial,
		  success: function(res, status, xhr){
			var type = xhr.getResponseHeader("Content-type");
			if(type != 'application/x-javascript'){
				$('#content').html(res);
				$('.focus').focus();
			}
		  }
		}).done(function() {
		  loaduniforms();
		});
	}
}

function removeit(serial){
	removeserial(serial);
}

function loaduniforms(){
	$('[data-toggle="tooltip"]').tooltip();
	$('input[type="checkbox"]').uniform();
	$('.checkboxs thead :checkbox').change(function(){
		if ($(this).is(':checked'))
		{
			$('.checkboxs tbody :checkbox').prop('checked', true).parent().addClass('checked');
			$('.checkboxs tbody tr.selectable').addClass('selected');
			$('.checkboxs_actions').show();
		}
		else
		{
			$('.checkboxs tbody :checkbox').prop('checked', false).parent().removeClass('checked');
			$('.checkboxs tbody tr.selectable').removeClass('selected');
			$('.checkboxs_actions').hide();
		}
	});
	
	$('.checkboxs tbody').on('click', 'tr.selectable', function(e){
		var c = $(this).find(':checkbox');
		var s = $(e.srcElement);
		
		if (e.srcElement.nodeName == 'INPUT')
		{
			if (c.is(':checked'))
				$(this).addClass('selected');
			else
				$(this).removeClass('selected');
		}
		else if (e.srcElement.nodeName != 'TD' && e.srcElement.nodeName != 'TR' && e.srcElement.nodeName != 'DIV')
		{
			return true;
		}
		else
		{
			if (c.is(':checked'))
			{
				c.prop('checked', false).parent().removeClass('checked');
				$(this).removeClass('selected');
			}
			else
			{
				c.prop('checked', true).parent().addClass('checked');
				$(this).addClass('selected');
			}
		}
		if ($('.checkboxs tr.selectable :checked').size() == $('.checkboxs tr.selectable :checkbox').size())
			$('.checkboxs thead :checkbox').prop('checked', true).parent().addClass('checked');
		else
			$('.checkboxs thead :checkbox').prop('checked', false).parent().removeClass('checked');

		if ($('.checkboxs tr.selectable :checked').size() >= 1)
			$('.checkboxs_actions').show();
		else
			$('.checkboxs_actions').hide();
	});
	
	if ($('.checkboxs tbody :checked').size() == $('.checkboxs tbody :checkbox').size() && $('.checkboxs tbody :checked').length)
		$('.checkboxs thead :checkbox').prop('checked', true).parent().addClass('checked');
	
	if ($('.checkboxs tbody :checked').length)
		$('.checkboxs_actions').show();
	
	$('.radioboxs tbody tr.selectable').click(function(e){
		var c = $(this).find(':radio');
		if (e.srcElement.nodeName == 'INPUT')
		{
			if (c.is(':checked'))
				$(this).addClass('selected');
			else
				$(this).removeClass('selected');
		}
		else if (e.srcElement.nodeName != 'TD' && e.srcElement.nodeName != 'TR')
		{
			return true;
		}
		else
		{
			if (c.is(':checked'))
			{
				c.attr('checked', false);
				$(this).removeClass('selected');				
			}
			else
			{
				c.attr('checked', true);
				$('.radioboxs tbody tr.selectable').removeClass('selected');
				$(this).addClass('selected');
			}
		}
	});
	
	if ($('.datepicker').length) 
	{
		$(".datepicker").datepicker({
			showOtherMonths:true,
			dateFormat:"yyyy-mm-dd"
		});
	}
	
	if($('#pluploadUploader').length){
		$("#pluploadUploader").pluploadQueue({
			// General settings
			runtimes : 'gears,browserplus,html5',
			url : 'js/save',
			max_file_size : '10mb',
			chunk_size : '10mb',
			unique_names : true,
	
			// Resize images on clientside if we can
			resize : {width : 320, height : 240, quality : 90},
	
			// Specify what files to browse for
			filters : [
				{title : "Fotos", extensions : "jpg,gif,png,mp4,mpg,mpeg,flv,wmv"},
				{title : "Comprimidos en Zip", extensions : "zip"}
			],
	
			// Flash settings
			flash_swf_url : 'theme/scripts/plupload/js/plupload.flash.swf',
	
			// Silverlight settings
			silverlight_xap_url : 'theme/scripts/plupload/js/plupload.silverlight.xap',
			multipart_params: getFormData($('#pluploadForm'))
		});
		
		// Client side form validation

		$('#pluploadForm').submit(function(e) {
			var uploader = $('#pluploadUploader').pluploadQueue();
				
			// Files in queue upload them first
			
			if (uploader.files.length > 0) {
				// When all files are uploaded submit form
				uploader.bind('StateChanged', function() {
					if (uploader.files.length === (uploader.total.uploaded + uploader.total.failed)) {
						$('#pluploadForm').submit();
					}
				});
				
				uploader.start();
				
			} else {
				alert('Debe seleccionar uno o más archivos');
			}

			return false;
		});
		
	}
	
}


function getFormData($form){
    var unindexed_array = $form.serializeArray();
    var indexed_array = {};

    $.map(unindexed_array, function(n, i){
        indexed_array[n['name']] = n['value'];
    });

    return indexed_array;
}

function viewModal(module,id){
	$('.modal-body').empty();
	$('.modal-body').load('view?module='+module+'&id='+id);
	$('#viewModal').modal('show');
}