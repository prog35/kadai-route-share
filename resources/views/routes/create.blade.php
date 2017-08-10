<!DOCTYPE html> 
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <title>TEST</title>
    <link rel="stylesheet" type="text/css" href="../css/style2.css" />
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="http://maps.google.com/maps/api/js?key={{ env('GOOGLE_MAPS_JS_ID') }}&libraries=places" type="text/javascript"></script>
    <script type="text/javascript">
        $(function(){
            var renderFLG=false;
            var directionsDisplay;
            var directionsService=new google.maps.DirectionsService();
            var map,mode;
            var currentDirections=null;
            var startSpot="東京駅";
            var endSpot="六本木ヒルズ";

            initialize();

            /* 地図初期化 */
            function initialize() {
                var myOptions={
                    zoom:14,
                    center: new google.maps.LatLng(35.670236,139.749832),//虎の門
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                }
                /* 地図オブジェクト生成 */
                map=new google.maps.Map(document.getElementById("map"), myOptions);
                if(!renderFLG) render();
                // calcRoute(startSpot,endSpot);
            }
            /* ルート検索結果を描画 */
            function render(){
                dbg("render:"+renderFLG);
                renderFLG=true;
                /* ルートをレンダリング */
                directionsDisplay=new google.maps.DirectionsRenderer({
                    "map": map,
                    "preserveViewport": true,
                    "draggable": true
                });
                /* 右カラムにルート表示 */
                directionsDisplay.setPanel(document.getElementById("directions_panel"));
                /* 出発地点・到着地点マーカーが移動された時 */
                google.maps.event.addListener(directionsDisplay, 'directions_changed',function() {
                    currentDirections=directionsDisplay.getDirections();
                    var route=currentDirections.routes[0];
                    var s="";
                    for(var i=0; i<route.legs.length; i++) {
                        var routeSegment=i+1;
                        s+=route.legs[i].start_address+'to';
                        s+=route.legs[i].end_address+'\n';
                        s+=route.legs[i].distance.text;
                    }
                    dbg("directions_changed:"+s);
                });
            }
            /* モード変更 */
            $("#mode").bind("change",function(){
                $(".button-group button").removeClass("active");
                calcRoute(startSpot,endSpot);
                $("#show").addClass("active");
            });
            /* ルート算出 */
            function calcRoute(startSpot,endSpot){
                switch($("#mode").val()){
                    case "driving":
                        mode=google.maps.DirectionsTravelMode.DRIVING;
                        break;
                    case "bicycling":
                        mode=google.maps.DirectionsTravelMode.BICYCLING;
                        break;
                    case "transit":
                        mode=google.maps.DirectionsTravelMode.TRANSIT;
                        break;
                    case "walking":
                        mode=google.maps.DirectionsTravelMode.WALKING;
                        break;
                }
                if(!renderFLG) render();
                var request={
                    origin:startSpot,            /* 出発地点 */
                    destination:endSpot,        /* 到着地点 */
                    travelMode:mode                /* 交通手段 */
                };
                /* ルート描画 */
                directionsService.route(request, function(response, status) {
                    if (status==google.maps.DirectionsStatus.OK) {
                        dbg(response);
                        directionsDisplay.setDirections(response);
                    }else{
                        dbg("status:"+status);
                    }
                });
            }
            /* ルート表示・非表示切り替え */
            $(".button-group button").click(function(e){
                $(".button-group button").removeClass("active");
                var id=$(this).attr("id");
                if(id=="show"){
                    calcRoute(startSpot,endSpot);
                    $(this).addClass("active");
                }else{
                    $(this).addClass("active");
                    reset();
                }
            });
            /* ルート削除 */
            function reset(){
                currentDirections=null;
                directionsDisplay.setMap(null);
                renderFLG=false;
            }
	        
	        $("#mapButton").click(function(e){
	        	alert(startSpot);
	        	startSpot = document.getElementById('mapFrom').value;
	    		endSpot = document.getElementById('mapTo').value;
	    		calcRoute(startSpot,endSpot);
	        });
	  
        });
        var dbg=function(str){
            try{
                if(window.console && console.log){
                    console.log(str);
                }
            }catch(err){
                //alert("error:"+err);
            }
        }

            
        function getPlace(){
		    var mapFrom = document.getElementById('mapFrom');
		    if(mapFrom.value){
		        var service = new google.maps.places.PlacesService(map);
		        var searchValue = mapFrom.value;
		        var placeRequest = {
		            query: searchValue, //入力したテキスト
		        }
		 
		        //リクエストを送ってあげるとプライス情報を格納したオブジェクトを返してくれます。
		        service.textSearch(placeRequest,function(results,status){
		            var places = results[0];
		            toGeocode(places);
		        });
		    }
		}
		function toGeocode(places){
		    //取得したplacesオブジェクトから緯度と経度をgeocodeとして渡します。
		    var latlng = new google.maps.LatLng(places.geometry.location.lat(),places.geometry.loca.lng());
		    //ルート取得
		    getRoute(latlng);
		}
    </script>
    <style>
        #map { float:left; width:70%; height:100%; }
        #side { float:right; width:30%; height:100%; }
        #side .inner { padding:10px; overflow:auto; }
    </style>
</head>
<body>

    <div class="searchBox">
	   
	       <input id="mapFrom" type="text">
	       <input id="mapTo" type="text">
	       <button id="mapButton">検索</button>
	   
	</div>
    
    <div id="map"><!-- 地図の埋め込み表示 --></div>
    <div id="side">
        <div class="inner">
        <p>
            <label for="mode">モード：<select id="mode" name="mode">
                <option value="driving" selected>DRIVING（自動車）</option>
                <option value="bicycling">BICYCLING （自転車）</option>
                <option value="transit">TRANSIT（電車）</option>
                <option value="walking">WALKING（徒歩）</option>
            </select></label>
        </p>
        <div class="button-group">
            <button id="show" class="button active">ルート表示</button>
            <button id="hide" class="button">ルート非表示</button>
        </div>
        <div id="directions_panel" style="width:100%"></div>
    </div>
    </div><!-- #side -->
    <br clear="all" />
</body>
</html>
