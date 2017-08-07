<!-- http://web-hon.com/contens/googlemap/ -->

<!DOCTYPE html>
<html>
<head>
	<style>
		body {
			margin: 10px 40px;
			width: 960px;
		}
		
		#map_canvas {
			width: 720px;
			height: 500px;
		}
		
		#map_wrap {
			width: 720px;
			height: 500px;
			margin: 20px 0 0 0;
			padding: 5px;
			border: solid 1px #ccc;
			background-color: #fefefe;
			-webkit-box-shadow: 0px 0px 5px 0px #ccc;
			-moz-box-shadow: 0px 0px 5px 0px #ccc;
			box-shadow: 0px 0px 5px 0px #ccc;
		}
	</style>
	
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js" ></script>
	<!--<script src="http://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_JS_ID') }}&sensor=false&region=JP" type="text/javascript"></script>-->
	<script src="http://maps.google.com/maps/api/js?key={{ env('GOOGLE_MAPS_JS_ID') }}" type="text/javascript"></script>
	<script type="text/javascript" src="js/map.js"></script>
	<script type="text/javascript" src="js/jquery.keybind.js"></script>

</head>
<body>
	<div style="margin:20px 0 10px 0;">
		<input type="button" value="1つ前に戻る" id="redelLine">　<input type="button" value="ラインを全て削除" id="delLine">
	</div>
	<!-- canvas -->
	<div id="map_wrap">
		<div id="map_canvas"></div>
	</div>

	<div style="margin: 20px 0 40px 0;">緯度：<span id="lat"></span> 経度：<span id="lng"></span></div>

	<div style="margin:20px 0 40px 0;">
		<h3 style="margin:5px 0;padding:0;">1.以下のコードをマップを表示したい箇所に貼付けて下さい。</h3>
		<div style="margin:0 0 10px 0;">
			地図の大きさ：<input type="text" id="w" name="w" size="5" placeholder="横幅" value="700" style="padding:3px;"> px　<input type="text" id="h" name="h" size="5" placeholder="高さ" value="500" style="padding:3px;"> px
		</div>
		<textarea id="mapcode" name="" cols="70" rows="1" readonly onclick="this.select(0,this.value.length)"></textarea>
	</div>
	
	
	<div style="margin:20px 0 60px 0;">
		<h3 style="margin:5px 0;padding:0;">2.以下のコードを&lt;head&gt;&lt;/head&gt;の間に貼付けて下さい</h3>
		<div style="margin:10px 0 10px 0;">ラインの地点（緯度・経度）のみを欲しい方は、ポイントのみを使用してください。</div>
		<input type="button" value="コードを表示" id="viewcode">　<input type="button" value="ラインのポイントのみを表示" id="viewlinecode"><br /><br />
		<textarea id="code" name="" cols="70" rows="10" readonly onclick="this.select(0,this.value.length)"></textarea>
	</div>

</body>
</html>
