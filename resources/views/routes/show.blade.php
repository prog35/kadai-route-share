<!--http://matsup.blogspot.jp/2012/05/google-map-2.html-->
@extends('layouts.app')

@section('head')
    <link rel="stylesheet" type="text/css" href="../css/style2.css" />
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="http://maps.google.com/maps/api/js?key={{ env('GOOGLE_MAPS_JS_ID') }}&libraries=places" type="text/javascript"></script>
	<script type="text/javascript">

	    var map;
	    var centerLatLng = new google.maps.LatLng({{ $route->center_lat }}, {{ $route->center_lng }});	// 初期表示座標
	    var rendererOptions = { draggable: false };

		google.maps.event.addDomListener(window, 'load', initialize);
		
		// MAP初期化処理
	    function initialize() {
	        var myOptions = {
	            zoom: {{ $route->zoom }},
	            center: centerLatLng,
	            mapTypeId: google.maps.MapTypeId.ROADMAP,
	            scaleControl: true,
	            scaleControlOptions: { position: google.maps.ControlPosition.BOTTOM_CENTER }
	        }
	        map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	        
	        //directionsDisplay.setMap(map);
	
	
			var flightPlanCoordinates = [
				@foreach ($latlngs as $latlng)
					{{ $latlng }}
				@endforeach
				
		      //new google.maps.LatLng(37.772323, -122.214897),
		      //new google.maps.LatLng(21.291982, -157.821856),
		      //new google.maps.LatLng(-18.142599, 178.431),
		      //new google.maps.LatLng(-27.46758, 153.027892)
		    ];
		    
		    var flightPath = new google.maps.Polyline({
		      path: flightPlanCoordinates,
		      strokeColor: "#0000ff",
		      strokeOpacity: 1.0,
		      strokeWeight: 5
		    });
		     
		    flightPath.setMap(map);
		    
		    // fit bounds
			var latLngBounds = new google.maps.LatLngBounds() ;
			
			flightPath.getPath().forEach( function ( latLng ) {
				latLngBounds.extend( latLng ) ;
			} ) ;
			
			map.fitBounds(latLngBounds);
			// fitBounds.setMap( latLngBounds ) ;
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
		  <div class='row'>
		  	<div class='col-md-8'>
                      <div class="form-group">
                        {!! Form::label('description', '経路の説明') !!}
                        {!! Form::textarea('description', $route->description, ['class' => 'form-control', 'id' => 'description', 'readonly'=> 'true' ]) !!}
                    </div>
            </div>
		  </div>
@endsection