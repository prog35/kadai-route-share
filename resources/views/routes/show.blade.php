<!--http://matsup.blogspot.jp/2012/05/google-map-2.html-->
@extends('layouts.app')

@section('head')
    <link rel="stylesheet" type="text/css" href="../css/style2.css" />
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="http://maps.google.com/maps/api/js?key={{ env('GOOGLE_MAPS_JS_ID') }}&libraries=places" type="text/javascript"></script>
	<script type="text/javascript">

	    var map;
	    var centerLatLng = new google.maps.LatLng(35.0, 136.5);	// 初期表示座標
	    var rendererOptions = { draggable: false };

		google.maps.event.addDomListener(window, 'load', initialize);
		
		// MAP初期化処理
	    function initialize() {
	        var myOptions = {
	            zoom: 9,
	            center: centerLatLng,
	            mapTypeId: google.maps.MapTypeId.ROADMAP,
	            scaleControl: true,
	            scaleControlOptions: { position: google.maps.ControlPosition.BOTTOM_CENTER }
	        }
	        map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	        
	        //directionsDisplay.setMap(map);
	
	
			var flightPlanCoordinates = [
				@foreach ($latlons as $latlon)
					{{ $latlon }}
				@endforeach
				
		      //new google.maps.LatLng(37.772323, -122.214897),
		      //new google.maps.LatLng(21.291982, -157.821856),
		      //new google.maps.LatLng(-18.142599, 178.431),
		      //new google.maps.LatLng(-27.46758, 153.027892)
		    ];
		    
		    var flightPath = new google.maps.Polyline({
		      path: flightPlanCoordinates,
		      strokeColor: "#FF0000",
		      strokeOpacity: 1.0,
		      strokeWeight: 2
		    });
		     
		    flightPath.setMap(map);
	    }
	

	</script>
@endsection

@section('content')
		<h1>経路詳細</h1>
		  <div class='row'>
			<div class='col-md-8'>
				<div id="map_canvas"></div>
				<div style="font-size:mideum;">距離: <span id="total" style="font-size:small;"></span></div>
			</div>
		  </div>
@endsection