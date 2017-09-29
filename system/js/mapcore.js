polygon_object = null;
polygon_waiting = false;
marker_object = null;
marker_waiting = false;
markers = {};
last_marker = null;
layer_position = [];

kml_layers = {};
kml_layer_position = [];

geocoder = null;
overlay = false;

function initmap(){

	var mapProp	= {
		center: new google.maps.LatLng(-25.283744665002256,-57.575419757324255), 
		zoom:13, 
		mapTypeId:google.maps.MapTypeId.ROADMAP
	};
	
	map	= new google.maps.Map(document.getElementById("mapcanvas"), mapProp);
	
	if($('.LatLng').val()){
		//loadMapa();
		var lat_long = $('.LatLng').val().split(',');
		var lat = lat_long[0];
		var lng = lat_long[1];
		setMarker(lat,lng);
	}
}

function drawPolygon(){

	if(polygon_object == null && !polygon_waiting){
	
		polygon_waiting = true;
		
		drawingManager = new google.maps.drawing.DrawingManager({
		drawingMode: google.maps.drawing.OverlayType.POLYGON,
		drawingControl: false,
		polygonOptions: {
			editable: true,
			clickable: true
		}
	  });
	  drawingManager.setMap(map);
	  
	  
	  google.maps.event.addListener(drawingManager, 'overlaycomplete', function(event){
		polygon_object = event;
		poly = event.overlay;
		$('.LatLng').val(poly.getPath().getArray().toString().replace('(', '').replace(')', ''));
		if(event.type == google.maps.drawing.OverlayType.POLYGON){
			drawingManager.setMap(null);
			event.overlay.setEditable(false);
			google.maps.event.addListener(poly.getPath(), 'insert_at', function(){
				var points = poly.getPath().getArray().toString().replace('(', '').replace(')', '');
				//console.log('Insert: ' +points);
				$('.LatLng').val(points);
			});
			
			google.maps.event.addListener(poly.getPath(), 'set_at', function(){
				var points = poly.getPath().getArray().toString().replace('(', '').replace(')', '');
				//console.log('Set: ' +points);
				$('.LatLng').val(points);
			});
			
			google.maps.event.addListener(poly, 'click', function(){
				poly.setEditable(true);
			});
			
			google.maps.event.addListener(map, 'click', function(){
				poly.setEditable(false);
			});
		
		}
	  });
	}else{
		deletePolygon();
	}
}

function placeMarker(){

	if(marker_object == null && !marker_waiting){
		
		marker_waiting = true;
		var drawingManager = new google.maps.drawing.DrawingManager({
		drawingMode: google.maps.drawing.OverlayType.MARKER,
		drawingControl: false,
		markerOptions:{
			draggable:true
		}
		});
		drawingManager.setMap(map);
		
		google.maps.event.addListener(drawingManager, 'markercomplete', function(marker){
			marker_object = marker;
			var point = marker.getPosition().toString().replace('(', '').replace(')', '');
			$('.LatLng').val(point);
			
			google.maps.event.addListener(marker,'rightclick', function(){
				marker_object = marker;
				deleteMarker();
			});
			
			google.maps.event.addListener(marker, 'drag', function(){
				var point = marker.getPosition().toString().replace('(', '').replace(')', '');
				$('.LatLng').val(point);
			});
			
			google.maps.event.addListener(marker, 'click', function(){
				marker_object = marker;
			});
			
			drawingManager.setMap(null);
		});
	}else{
		deleteMarker();
	}
}

function deleteMarker(){
	if(marker_object != null){
		if(confirm('¿Desea eliminar el marcador de ubicación?')){
			marker_object.setMap(null);
			marker_object = null;
			marker_waiting = false;
			$('.LatLng').val('');
		}
	}
}

function removeMarker(){
	if(marker_object != null){
		marker_object.setMap(null);
		marker_object = null;
		marker_waiting = false;
	}
}

function deletePolygon(){
	if(polygon_object != null){
		if(confirm('¿Desea eliminar el área delimitada?')){
			polygon_object.overlay.setMap(null);
			polygon_object = null;
			polygon_waiting = false;
		}
	}
}

function removePolygon(){
	if(polygon_object != null){

		polygon_object.overlay.setMap(null);
		polygon_object = null;
		polygon_waiting = false;
	}
}
function setMarker(lat,lng){
	var markerpos = new google.maps.LatLng(lat,lng);
	marker_object = new google.maps.Marker({
	position: markerpos,
	map: map,
	draggable: false
	});
	
	google.maps.event.addListener(marker_object, 'drag', function(){
		var point = marker_object.getPosition().toString().replace('(', '').replace(')', '');
	});
	map.setCenter(markerpos)
}

function addMarkers(list, layer_index){
	
	markers[layer_index] = [];

	var last_pos;
	
	if(!overlay){
		clearMarkers();
	}
	
	$.each(list, function(i, row){
		
		var sep=row.outlet_latlng.search(',');
		var lat=row.outlet_latlng.substring(1, sep);
		var lng=row.outlet_latlng.substring(sep+2, row.outlet_latlng.length -1);
		var pos = new google.maps.LatLng(lat,lng);
		last_pos = pos;
		
		var circle_icon = {
			path: google.maps.SymbolPath.CIRCLE,
			fillOpacity: 1,
			fillColor: '#'+row.ic,
			strokeOpacity: 1.0,
			strokeColor: '#000000',
			strokeWeight: 1.0, 
			scale: 6
		}
		
		var infowindow = new google.maps.InfoWindow({position: pos});
		
		marker = new google.maps.Marker({
			position: pos,
			icon: circle_icon,
			map: map,
			draggable: false,
			zIndex: Object.keys(markers).length
		});
		
		google.maps.event.addListener(marker, 'click', function(){
			
			var mark = this;
			var content = row.outlet_id;
			google.maps.event.trigger(map, 'mousedown');
			
			$.ajax({
				type:"GET",
				url: 'js/info',
				data:{"id":row.outlet_id},
				error: function(){
					alert('Error leer la información del punto de venta');
				},
				success: function(info){
					content = info;
					infowindow.setContent(info);
					infowindow.open(map, mark);
				}
			});
			
		});
		
		google.maps.event.addListener(map, 'mousedown', function(){
			infowindow.close();
		});
		markers[layer_index].push(marker);
	});
	markerCluster = new MarkerClusterer(map, markers[layer_index]);
	markerCluster.setMaxZoom(13);
}


function clearMarkers(layer_index){
	for(var i=0; i< markers[layer_index].length; i++){
		markerCluster.removeMarker(markers[layer_index][i]);
		markers[layer_index][i].setMap(null);
	}
}

function placeMultiMarker(){
}

function loadKML(el, layer, token){
	if(el.checked){
		kml_layers[layer.toString()] = [];
		kmlLayer = new google.maps.KmlLayer({
			url: 'http://dev.guaoo.com/staging/ica_maps/web/js/kml?filter='+layer+'&token='+token,
			suppressInfoWindows: true
		});
	
		google.maps.event.addListener(kmlLayer, 'click', function(kmlEvent) {
			showInfoWindow(kmlEvent.latLng, kmlEvent.featureData.name);
		});
		
		kml_layers[layer.toString()].push(kmlLayer);
		kmlLayer.setMap(map);
		
	}else{
		removeKML(layer);
	}
}

function removeKML(layer){
	kml_layers[layer.toString()][0].setMap(null);
	kml_layers[layer.toString()].splice(0,1);
}

function showInfoWindow(position, text){
	google.maps.event.trigger(map, 'mousedown');
	$.ajax({
		type:"GET",
		url: 'js/info',
		data:{"id":text},
		error: function(){
			alert('Error leer la información del punto de venta');
		},
		success: function(info){
			var infowindow = new google.maps.InfoWindow({
				content: info, 
				position: position,
				pixelOffset: new google.maps.Size(0,0),
				maxWidth: '300px'
			})
			infowindow.open(map);
			
			google.maps.event.addListener(map, 'mousedown', function(){
				infowindow.close();
			});
		}
	});
}

function movemap(){
	if(typeof drawingManager != 'undefined'){
		drawingManager.setMap(null);
		polygon_waiting = false;
	}
}


function loadMapa(markerUbica, coordenadas){

	var markerUbica = $('.LatLng').val();
	var coordenadas = $('.LatLng').val();

	var lat_long = markerUbica.split(',');
	var latitud = lat_long[0];
	//latitud = latitud.substring(1);
	//var longitud = lat_long[1].substring(0,latitud.length-1);
	var longitud = lat_long[1];

	var mapOptions = {
		zoom: 13,
		center: new google.maps.LatLng(latitud, longitud),
		mapTypeId: google.maps.MapTypeId.MAP
	};


	var cobertura;
	
	var map = new google.maps.Map(document.getElementById('map_actual'),mapOptions);

	var marker = new google.maps.Marker({
		map: map,
		position: new google.maps.LatLng(latitud, longitud),
		draggable: false
	});
	/*
	if(coordenadas){	
		coordenadas = coordenadas.substring(1, coordenadas.length-1);
		coordenadas = coordenadas.split(',');
		var coord = '';
		for(var i = 0; i<coordenadas.length; i++){
			coord = coord+"new google.maps.LatLng("+coordenadas[i]+"),";
		}

		coord = coord.substring(0, coord.length-1);

		// Define the LatLng coordinates for the polygon's path.
		eval('(coordenadas = ['+coord+'])');

		// Construct the polygon.
		cobertura = new google.maps.Polygon({
			paths: coordenadas,
			strokeColor: '#FF0000',
			strokeOpacity: 0.8,
			strokeWeight: 2,
			fillColor: '#FF0000',
			fillOpacity: 0.35
		});

		cobertura.setMap(map);
	}*/
	
}

function eliminaMapa(){
	if(confirm('¿Desea editar el mapa?')){
		$('#map_actual').remove();
		$('#optElimina').remove();
		$('#listaEditar').show();
		//elimina el area
		polygon_object.overlay.setMap(null);
		polygon_object = null;
		polygon_waiting = false;
	}
	
}