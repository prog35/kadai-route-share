var _poly = new Array();
var _map;
var _marker_start;
var _geocoder;
var _polyLineOptions;
var _points = new Array();
var _allPoints = new Array();;
var _getLat = 35.7100815;
var _getLng = 139.80824159999997;
var _zoom = 18;

google.maps.event.addDomListener(window, 'load', function() {
	var center = new google.maps.LatLng(_getLat, _getLng);
	var latlng = new google.maps.LatLng(_getLat, _getLng);
	var opts = {
		zoom: 18,
		center: center,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	
	_map = new google.maps.Map(document.getElementById("map_canvas"), opts);
	_geocoder = new google.maps.Geocoder();
	_marker_start = new google.maps.Marker({
		position: latlng,
		map: _map,
		draggable: true
	});

	
	_polyLineOptions = {
		path: _points,
		strokeWeight: 5,
		strokeColor: "#cc0000",
		strokeOpacity: "0.7"
	}
	//////////////////
	// MapClick処理
	//////////////////
	google.maps.event.addListener(_map, 'click', function(e) {
		_points.push(e.latLng);
		_allPoints.push(e.latLng);
		
		if (_points.length > 2) {
			_points.shift();
		}
		
		if (_points.length > 1) {
			_polyLineOptions.path = _points;
			var poly = new google.maps.Polyline(_polyLineOptions);
			
			_poly.push(poly);
			poly.setMap(_map);
		}
	});
	
	var contentString = 'test';
	var infowindow = new google.maps.InfoWindow({
		content: contentString
	});
	
	google.maps.event.addListener(_marker_start, 'click', function() {
		infowindow.open(_map, _marker_start);
	});
	
	// ?
	google.maps.event.addListener(_marker_start, 'dragend', function() {
		var p = _marker_start.getPosition();
		_getLat = p.lat();
		_getLng = p.lng();
		_map.setCenter(_marker_start.getPosition());
		_marker_start.setPosition(_marker_start.getPosition());
		$("#lat").html(_getLat);
		$("#lng").html(_getLng);
	});
	
	google.maps.event.addListener(_map, 'zoom_changed', function() {
		_zoom = _map.getZoom();
	});
});

$(function() {
	$("#execute").click(function() {
		var addr = $("#addr").val();
		
		_geocoder.geocode({'address': addr}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				_getLat = results[0].geometry.location.lat();
				_getLng = results[0].geometry.location.lng();
				_map.setCenter(results[0].geometry.location);
				_marker_start.setPosition(results[0].geometry.location);
				$("#lat").html(_getLat);
				$("#lng").html(_getLng);
			} else {
				alert('');
			}
		});
		
		return false;
	});
	
	// ?
	$("#delLine").click(function() {
		jQuery.each(_poly, function() {
			this.setMap(null); 
		});
		
		_polyLineOptions.path = null;
		_points = Array();
		_allPoints = Array();
	});
	
	// ? 
	$("#redelLine").click(function() {
		var d = _poly.length;
		var dp = _allPoints[_allPoints.length - 2];
		
		if (_poly.length > 0) {
			_allPoints.pop();
			
			_poly.pop().setMap(null);
			
			if (_points.length > 0) {
				_points.pop();
			}
			
			//alert(dp);
			_points[0] = dp;
		} else {
			_points = Array();
			_allPoints = Array();
		}
	});

	$(window).bind('keydown', 'C-z', function(e) {
		var d = _poly.length;
		var dp = _allPoints[_allPoints.length - 2];
		
		if (_poly.length > 0) {
			_allPoints.pop();
			
			_poly.pop().setMap(null);
			
			if (_points.length > 0) {
				_points.pop();
			}
			
			//alert(dp);
			_points[0] = dp;
		} else {
			_points = Array();
			_allPoints = Array();
		}
	});

	$("#viewcode").click(function() {
		var linestr = "";
		var center = _map.getCenter();
		var i = 0;
		
		jQuery.each(_allPoints, function() {
			if (i > 0) {
				linestr += ',\n';
			}

			linestr += 'new google.maps.LatLng' + this;
			i++;
		});
	
	
		var htmlStr = '<script src="http://maps.google.com/maps/api/js?sensor=false&region=JP" type="text/javascript"><\/script>\n';
		htmlStr += '<script type="text/javascript">\n';
		htmlStr += 'google.maps.event.addDomListener(window, "load", function() {\n';
		htmlStr += 'var' + ' center = new google.maps.LatLng(' + center.lat() + ', ' + center.lng() + ')\n';
		htmlStr += 'var' + ' latlng = new google.maps.LatLng(' + _getLat + ', ' + _getLng + ')\n';
		htmlStr += 'var' + ' opts = {zoom: ' + String(_zoom) + ', center: center, mapTypeId: google.maps.MapTypeId.ROADMAP}\n';
		htmlStr += 'var' + ' map = new google.maps.Map(document.getElementById("map_canvas"), opts)\n';
		htmlStr += 'var' + ' geocoder = new google.maps.Geocoder()\n';
		htmlStr += 'var' + ' marker = new google.maps.Marker({position: latlng, map: map, draggable: false})\n';
		htmlStr += 'var' + ' points = [' + linestr + ']\n';
		htmlStr += 'var' + ' polyLineOptions = {path: points, strokeWeight: 5, strokeColor: "#cc0000", strokeOpacity: "0.7"}\n';
		htmlStr += 'var' + ' polyObj = new google.maps.Polyline(polyLineOptions)\n';
		htmlStr += 'polyObj.setMap(map)\n';
		htmlStr += 'var' + ' contentString = ""\n';
		htmlStr += 'var' + ' infowindow = new google.maps.InfoWindow({content: contentString})\n';
		htmlStr += 'google.maps.event.addListener(marker, "click", function() {infowindow.open(map,marker);})\n';
		htmlStr += '});\n';
		htmlStr += '<\/script>';
	
		$("#code").val(htmlStr);
	});
	
	$("#viewlinecode").click(function() {
		var linestr = "";
		var i = 0;
		
		jQuery.each(_allPoints, function() {
			if (i > 0) {
				linestr += ',\n';
			}

			linestr += 'new google.maps.LatLng' + this;
			i++;
		});
		
		$("#code").val(linestr);
	});
	

	var w = 700;
	var h = 500;
	var mapstr = '<div id="map_canvas" style="width:' + String(w) + 'px; height:' + String(h) + 'px"></div>';
	$("#mapcode").val(mapstr);
	$("#code").val();
	$("#lat").html(_getLat);
	$("#lng").html(_getLng);
	
	$("#w").blur(function(){
		var map_w = $("#w").val();
		var map_h = $("#h").val();
		//var map_w_str = String(map_w) + "px";
		//$("#map_canvas").css("width:", map_w_str);
		mapstr = '<div id="map_canvas" style="width:' + map_w + 'px; height:' + map_h + 'px"></div>';
		$("#mapcode").val(mapstr);
	});
	
	$("#h").blur(function(){
		var map_w = $("#w").val();
		var map_h = $("#h").val();
		//var map_h_str = String(map_h) + "px";
		//$("#map_canvas").css("height:", map_h_str);
		mapstr = '<div id="map_canvas" style="width:' + map_w + 'px; height:' + map_h + 'px"></div>';
		$("#mapcode").val(mapstr);
	});
	
});