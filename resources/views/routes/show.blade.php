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
	        
			var flightPlanCoordinates = [
				@foreach ($latlngs as $latlng)
					{{ $latlng }}
				@endforeach
			];
		    
		    var flightPath = new google.maps.Polyline({
		      path: flightPlanCoordinates,
		      strokeColor: "#0000ff",
		      strokeOpacity: 1.0,
		      strokeWeight: 5
		    });
		     
		    flightPath.setMap(map);
		    
		    // fit bounds　経路全体が表示される最適なサイズで表示
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
	作成日：{{ $route->created_at }}
  	<div class='row'>
		<div class='center-block'>
			<div id="map_canvas"></div>
			<div style="font-size:mideum;">距離: <span id="total" style="font-size:small;">{{ $route->total_distance }} km</span></div>
		</div>
  	</div>
  	<div class='row'>
	  	<div class='center-block'>
          	<div class="form-group">
            	{!! Form::label('description', '経路の説明') !!}
            	{!! Form::textarea('description', $route->description, ['class' => 'form-control', 'id' => 'description', 'rows' => '3', 'readonly'=> 'true' ]) !!}
	        </div>
        </div>
  	</div>
  	
  	@if (Auth::user()->is_owner($route->id))
	<div class='row' >
	  	<div class='center-block text-center'>
		    {!! Form::model($route, ['route' => ['routes.destroy', $route->id], 'method' => 'delete']) !!}
		        {!! Form::submit('削除',['class' => 'btn btn-danger']) !!}
		    {!! Form::close() !!}
		</div>
	</div>
	@endif
@endsection