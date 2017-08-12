<!--http://matsup.blogspot.jp/2012/05/google-map-2.html-->

<!DOCTYPE html> 
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <title>TEST</title>
    <link rel="stylesheet" type="text/css" href="../css/style2.css" />
    
   <!--追加-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<!--ここまで-->

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="http://maps.google.com/maps/api/js?key={{ env('GOOGLE_MAPS_JS_ID') }}&libraries=places" type="text/javascript"></script>
	<script type="text/javascript">

	    var map;
	    var centerLatLng = new google.maps.LatLng(35.0, 136.5);
	    var rendererOptions = { draggable: true };
	    var directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions);
	    var directionsService = new google.maps.DirectionsService();
	
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
	        
	        directionsDisplay.setMap(map);
	        //directionsDisplay.setPanel(document.getElementById("directionsPanel"));
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
	    }
	// --------------------------------------------------
		// ルート変更
	    function modifyDirectionDataProcess(result) {
	        var total = 0;
	        var myroute = result.routes[0];
	        for (i = 0; i < myroute.legs.length; i++) { total += myroute.legs[i].distance.value; }
	        total = total / 1000;
	        document.getElementById("total").innerHTML = total + " km";
	        displayRouteString(result);
	    }
	// --------------------------------------------------
		//　座標設定
	    function setPoints(map, latlng) {
	        var geocoder = new google.maps.Geocoder();
	        var strData;
	        if (document.getElementById("start").checked) { 
	            document.getElementById("start_pts").value = latlng.lat().toFixed(9)+","+latlng.lng().toFixed(9);
	        }
	        if (document.getElementById("end").checked) { 
	            document.getElementById("end_pts").value = latlng.lat().toFixed(9)+","+latlng.lng().toFixed(9);
	        }
	        if (document.getElementById("waypoint").checked) {
	            strData = document.getElementById("waypoints").value;
	            strData += latlng.lat().toFixed(9)+","+latlng.lng().toFixed(9)+"\n";
	            document.getElementById("waypoints").value = strData;
	        }
	    }
	// --------------------------------------------------
	    function clearAddr() {
	        document.getElementById("start_pts").value = '';
	        document.getElementById("end_pts").value = '';
	        document.getElementById("waypoints").value = '';
	    }
	// --------------------------------------------------
	    function calcRoute() {
	        var start = document.getElementById("start_pts").value;
	        var end = document.getElementById("end_pts").value;
	        var hw_flag;
	        var toll_flag;
	        var strData = '';
	
	        var waypts = [];
	        var waypoints = document.getElementById("waypoints").value;
	        var wptsArray = waypoints.split("\n");
	        for (var i = 0; i < wptsArray.length; i++) {
	            if (wptsArray[i] != '') { waypts.push({location:wptsArray[i], stopover:true}); }
	        }
	
	        if (document.getElementById("nonhighway").checked) { hw_flag = true; } else { hw_flag = false; }
	        if (document.getElementById("nontollway").checked) { toll_flag = true; } else { toll_flag = false; }
	
	        var request = {
	            origin: start, 
	            destination: end,
	            waypoints: waypts,
	            optimizeWaypoints: true,
	            avoidHighways: hw_flag,
	            avoidTolls: toll_flag,
	            travelMode: google.maps.DirectionsTravelMode.DRIVING
	        };
	        directionsService.route(request, function(response, status) {
	            if (status == google.maps.DirectionsStatus.OK) {
	                directionsDisplay.setDirections(response);
	                displayRouteString(response);
	            } else { alert("Directions Service ERROR : "+status+"\n"+direcErr[status]); }
	        });
	    }
	// --------------------------------------------------
	</script>
	</head>
	
	<body>
		<div class='container'>
		  <div class='row'>
			<div class='col-md-8'>
				<div id="map_canvas"></div>
			</div>
			<div class='col-md-4' id="control_panel">
	    		<font class="boldblack" size="+1">　2点間の経路検索</font>　radio ボタン＋クリックで指定
			    <table border="0" cellpadding="2" cellspacing="0">
			    	<tr>
			    		<td colspan="4" style="font-size:small;">
			      　		・各項目は<font class="blue">住所・名称の直接入力可</font>。
			        		<font class="black">Lat,Lng (緯度,経度) の方が精度高</font><br>
			      　		・検索後，<font class="blue">ドラッグによる経路変更可</font>。
			        		<font class="black">経路点数値データも自動修正</font><br>
			      		</td>
			      	</tr>
			    </table>
			    <hr size="1" class="lightgray">
	    		<form name="select_points">
			      	<table border="0" cellpadding="1" cellspacing="0">
			        	<tr>
			        		<td style="font-size:small;"> 
			          			<input type="radio" name="select_points" style="font-size:small;" id="start" checked>出発点
			          		</td>
			          		<td>
			          			<input type="text" size="38" id="start_pts">
			          		</td>
			          	</tr>
			        	<tr>
			        		<td style="font-size:small;"> 
			          			<input type="radio" name="select_points" style="font-size:small;" id="end">到着点
			          		</td>
			          		<td>
			          			<input type="text" size="38" id="end_pts">
			          		</td>
			          	</tr>
			        	<tr>
			        		<td style="font-size:small;"> 
			          			<input type="radio" name="select_points" id="waypoint">経由地
			          		</td>
			          		<td>
			          			<textarea cols="36" rows="7" id="waypoints" style="font-size:small;"></textarea>
			          		</td>
			          	</tr>
			
			        	<tr>
			        		<td colspan="4" align="center" style="font-size:small;">
			     　   			<input type="checkbox" id="nonhighway" class="black">高速道路除く　　
			        　			<input type="checkbox" id="nontollway" class="black">有料道路除く
			        		</td>
			        	</tr>
			        	<tr>
			        		<td colspan="4" align="center">
				        　		<input type="button" class="boldred100" onclick="calcRoute();" value="経路を求める">　　
					        　	<input type="button" class="boldblue100" onclick="clearAddr();" value="入力値クリア">
					        </td>
					    </tr>
					</table>
			    </form>
	
		        <hr size="1" class="lightgray">
			    <table border="0" cellpadding="4" cellspacing="0">
			      	<tr>
		      			<td>
		      				<div style="font-size:mideum;">距離: <span id="total" style="font-size:small;"></span></div>
			      		</td>
			      	</tr>
			      	<tr>
			      		<td>　　
			      			<textarea cols="40" rows="8" id="info_window" style="font-size:x-small;"></textarea>
		      			</td>
		  		  	</tr>
			    </table>
				<hr size="1" class="lightgray">
		    	<div id="directionsPanel" style="margin:3px; font-size:small; width:340px; height:320px; overflow:scroll;">
		      		
			    </div>
			</div>
		  </div>
		</div>
	</body>
</html>