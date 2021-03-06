<!--http://matsup.blogspot.jp/2012/05/google-map-2.html-->
@extends('layouts.app')

@section('head')

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="http://maps.google.com/maps/api/js?key={{ env('GOOGLE_MAPS_JS_ID') }}&libraries=places" type="text/javascript"></script>
	<script type="text/javascript">

	    var map;
	    var centerLatLng = new google.maps.LatLng(35.0, 136.5);	// 初期表示座標
	    var rendererOptions = { draggable: true };
	    var directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions);	// ルート検索結果表示
	    var directionsService = new google.maps.DirectionsService();					// ルート検索
	
		// Direction service 結果のステータス
		// OK, MAX_WAYPOINTS_EXCEEDED, NOT_FOUND, INVALID_REQUEST, 
		// OVER_QUERY_LIMIT, REQUEST_DENIED, UNKNOWN_ERROR, ZERO_RESULTS
	    var direcStat = google.maps.DirectionsStatus; 
	    var direcErr = new Array(); //ルート結果のエラーメッセージ
	        direcErr[direcStat.INVALID_REQUEST] = "DirectionsRequest が無効";
	        direcErr[direcStat.MAX_WAYPOINTS_EXCEEDED] = "経由点がが多すぎます。経由点は 8 以内です。";
	        direcErr[direcStat.NOT_FOUND] = "いずれかの点が緯度経度に変換できませんでした。";
	        direcErr[direcStat.OVER_QUERY_LIMIT] = "単位時間当りのリクエスト制限回数を超えました。";
	        direcErr[direcStat.REQUEST_DENIED] = "このサイトからはルートサービスを使用できません。";
	        direcErr[direcStat.UNKNOWN_ERROR] = "不明なエラーです。もう一度試すと正常に処理される可能性があります。";
	        direcErr[direcStat.ZERO_RESULTS] = "ルートを見つけられませんでした。";  
	
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
	        //directionsDisplay.setPanel(document.getElementById("directionsPanel"));	// ルート案内は使わない
	    	// CLickイベント追加
	        google.maps.event.addListener(map, 'click', function(mouseEvent) {
	            setPoints(map, mouseEvent.latLng);
	        });
	        // ルート変更時イベント追加
	        google.maps.event.addListener(directionsDisplay, 'directions_changed', function() {
	            modifyDirectionDataProcess(directionsDisplay.directions);
	        });
	    }
		// ==================================================
		// ルート座標を表示
	    function displayRouteString(result) {
	        var strData = '';
	      
	        for (var j = 0; j < (result.routes.length); j++) {
	            for (var i = 0; i < (result.routes[j].overview_path.length); i++) {
	                var lat = result.routes[j].overview_path[i].lat().toFixed(9);
	                var lng = result.routes[j].overview_path[i].lng().toFixed(9);
	                strData += lat+','+lng+"\n";
	            } 
	        }
	        
	        document.getElementById("info_window").value = strData;
	        document.getElementById("zoom").value = map.getZoom();
	        
	        var pos = map.getCenter();
	        document.getElementById("center_lat").value = pos.lat();
	        document.getElementById("center_lng").value = pos.lng();
	
	    }
	// --------------------------------------------------
		// ルート変更
	    function modifyDirectionDataProcess(result) {
	        var total = 0;
	        var myroute = result.routes[0];
	        for (i = 0; i < myroute.legs.length; i++) { 
	        	total += myroute.legs[i].distance.value; 
	        }
	        total = total / 1000;
	        document.getElementById("total").innerHTML = total + " km";
	        document.getElementById('total_distance').value = total;
	        displayRouteString(result);
	    }
	// --------------------------------------------------
		//　座標設定
	    function setPoints(map, latlng) {
	        var geocoder = new google.maps.Geocoder();
	        var strData;
	        if (document.getElementById("start").checked) { 
	            document.getElementById("startPoint").value = latlng.lat().toFixed(9)+","+latlng.lng().toFixed(9);
	        }
	        if (document.getElementById("end").checked) { 
	            document.getElementById("endPoint").value = latlng.lat().toFixed(9)+","+latlng.lng().toFixed(9);
	        }
	        if (document.getElementById("mdl").checked) {
	            strData = document.getElementById("mdlPoints").value;
	            strData += latlng.lat().toFixed(9)+","+latlng.lng().toFixed(9)+"\n";
	            document.getElementById("mdlPoints").value = strData;
	        }
	    }
	// --------------------------------------------------
		// クリア処理
	    function clearAddr() {
	    	
	    	directionsDisplay.setMap(null);
	        document.getElementById("startPoint").value = '';
	        document.getElementById("endPoint").value = '';
	        document.getElementById("mdlPoints").value = '';
	        document.getElementById("info_window").value = '';
	        document.getElementById("zoom").value = '';
	        document.getElementById("center_lat").value = '';
	        document.getElementById("center_lng").value = '';
	        document.getElementById("total_distance").value = '';
	        
	        document.getElementById("total").innerHTML = "";
	    }
	// --------------------------------------------------
		//経路を求める
	    function calcRoute() {
	        var start = document.getElementById("startPoint").value;
	        var end = document.getElementById("endPoint").value;
	        var hw_flag;
	        var toll_flag;
	        var strData = '';
	
	        var waypts = [];
	        var waypoints = document.getElementById("mdlPoints").value;
	        var wptsArray = waypoints.split("\n");
	        for (var i = 0; i < wptsArray.length; i++) {
	            if (wptsArray[i] != '') { waypts.push({location:wptsArray[i], stopover:true}); }
	        }
	
	        if (document.getElementById("nonhighway").checked) { hw_flag = true; } else { hw_flag = false; }
	        if (document.getElementById("nontollway").checked) { toll_flag = true; } else { toll_flag = false; }
	
			directionsDisplay.setMap(map);
	
	        var request = {
	            origin: start, 
	            destination: end,
	            waypoints: waypts,
	            optimizeWaypoints: true,
	            avoidHighways: hw_flag,
	            avoidTolls: toll_flag,
	            travelMode: google.maps.DirectionsTravelMode.DRIVING
	        };
	        // ルート検索して結果がOKであればルートを表示
	        directionsService.route(request, function(response, status) {
	            if (status == google.maps.DirectionsStatus.OK) {
	                directionsDisplay.setDirections(response);
	                displayRouteString(response);
	            } else { alert("Directions Service ERROR : "+status+"\n"+direcErr[status]); }
	        });
	    }
	// --------------------------------------------------


		function checkForm() {
			var err_msg = "";
			if (document.getElementById('info_window').value == "" ) {
				err_msg = "経路を指定してください。";
			}
			
			if (document.getElementById('description').value == "" ) {
				if(err_msg.length > 0) err_msg += '\n';
				err_msg += "経路の説明を入力してください。";
			}
			
			if (err_msg.length > 0 ) {
				alert(err_msg);
				return false;
			}
			else {
				return true;
			}
		}

	</script>
@endsection

@section('content')
		  <div class='row'>
			<div class='col-md-8'>
				<div id="map_canvas"></div>
				<p>
					<div style="font-size:mideum;">距離: <span id="total" style="font-size:small;"></span></div>
				</p>
			</div>
			<div class='col-md-4'>

				<div class="panel panel-default small">
				  <div class="panel-heading">
				    <h3 class="panel-title">ルート指定方法</h3>
				  </div>
				  <div class="panel-body" id='info_msg'>
				    <ul class="list-unstyled">
						<li>経路は出発地、到着地、経由地の入力後に「経路を表示」ボタンで表示できます。</li>
						<li>各項目は住所または名称を指定します。</li>
						<li>ラジオボタン選択後に地図上をクリックすることでも指定可能です。</li>
						<li>経路表示後にドラッグで経路の変更が可能です。</li>
				    </ul>
				  </div>
				</div>


	            <div class="form-group">
	                {!! Form::radio('select_points','出発地',true,['id' => 'start']) !!}
	                {!! form::label('startPoint', '出発地') !!}
	                {!! form::text('startPoint', old('startPoint'), ['class' => 'form-control', 'id' => 'startPoint']) !!}
				</div>
				
				<div class="form-group">
	                {!! Form::radio('select_points','到着地',false,['id' => 'end']) !!}
	                {!! Form::label('endPoint', '到着地') !!}
	                {!! Form::text('endPoint', old('endPoint'), ['class' => 'form-control', 'id' => 'endPoint']) !!}
	            </div>
	
	                    
				<div class="form-group">
	                {!! Form::radio('select_points','経由地',false,['id' => 'mdl']) !!}
	                {!! Form::label('mdlPoints', '経由地') !!}
	                {!! Form::textarea('mdlPoints', old('mdlPoints'), ['class' => 'form-control', 'id' => 'mdlPoints', 'rows' => '4']) !!}
	                
	                {!! Form::checkbox('black','高速道路除く',false,['id' => 'nonhighway']) !!}
	                {!! form::label('black', '高速道路除く') !!}
	                {!! Form::checkbox('black','有料道路除く',false,['id' => 'nontollway']) !!}
	            	{!! form::label('black', '有料道路除く') !!}
	            </div>
	            
	            <div class="form-group">
	                {!! form::button('経路を表示', ['class' => 'btn btn-default','id' => 'btncalcRoute', 'onClick' => 'calcRoute()']) !!}
	                {!! form::button('入力値クリア', ['class' => 'btn btn-default','id' => 'btncalcClear', 'onClick' => 'clearAddr()']) !!}
	            </div>
			</div>
		  <div class='row'></div>
			<div class='col-md-8' id="control_panel">
                {!! Form::open(['route' => 'routes.store', 'onSubmit' => 'return checkForm()']) !!}

  					<div class="form-group">
  						<!--{!! Form::textarea('info_window', null, ['class' => 'form-control', 'id' => 'info_window']) !!}-->
  						{!! Form::hidden('info_window', old('info_window'), ['class' => 'form-control', 'id' => 'info_window']) !!}
  						{!! Form::hidden('zoom', old('zoom'), ['class' => 'form-control', 'id' => 'zoom']) !!}
  						{!! Form::hidden('center_lat', old('center_lat'), ['class' => 'form-control', 'id' => 'center_lat']) !!}
  						{!! Form::hidden('center_lng', old('center_lng'), ['class' => 'form-control', 'id' => 'center_lng']) !!}
  						{!! Form::hidden('total_distance', old('total_distance'), ['class' => 'form-control', 'id' => 'total_distance']) !!}
  					</div>
                    
                    <div class="form-group">
                        {!! Form::label('description', '経路の説明') !!}
                        {!! Form::textarea('description', old('description'), ['class' => 'form-control', 'id' => 'description', 'rows' => '3']) !!}
                    </div>
                    
                    <div class="form-group text-center">
                        {!! form::submit('経路を登録', ['class' => 'btn btn-success']) !!}
                    </div>
                    
                    
                {!! Form::close() !!}
                
   		    	<div id="directionsPanel" style="margin:3px; font-size:small; width:340px; height:320px; overflow:scroll;">

			</div>
		  </div>
@endsection