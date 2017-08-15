<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Route;
use App\RouteDetail;

class RoutesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // 登録されているルートを読み込み、ビューへ渡す
        $keyword = request()->keyword;
        
        if (empty($keyword)) {
            $routes = \DB::table('routes')->orderBy('created_at', 'desc')->paginate(10);
        }
        else {
            $param = $keyword;
            $routes = \DB::table('routes')
                ->where('description','like',"%{$param}%")
                ->orderBy('created_at', 'desc')
                ->paginate(10);
    
        }
        
        $data = [
            'user' => \Auth::user(),
            'routes' => $routes,
            'keyword' => $keyword,
        ];
        

        return view('welcome', $data);
    }
    


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('routes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // 経路も必須
        // 説明は必須
        $this->validate($request, [
            'info_window' => 'required',
            'description' => 'required|max:255',
        ]);
        
        \DB::transaction(function() use ($request) {
            
            //改行コードを置換してLF改行コードに統一
            $info_window = trim(str_replace(array("\r\n","\r","\n"), "\n", $request->info_window));
            //LF改行コードで配列に格納
            $points = explode("\n", $info_window);
            
            // ２次元配列へ
            foreach ($points as $row) {
                $point = explode(',',$row);  
                $latlngs[] = array($point[0],$point[1]);
            }
            
            // var_dump($this->getStaticMapUrl($latlngs));
            // return;
            //////////////////////
            // ルートの登録
            //////////////////////
            // ボタン名で一時保存、公開保存を判定する
            $status = 1;
            
            $route = Route::create([
                'description' => $request->description,
                'status' => $status,
                'static_map_url' => $this->getStaticMapUrl($latlngs),
                'zoom' => $request->zoom,
                'center_lat' => $request->center_lat,
                'center_lng' => $request->center_lng,
            ]);
            //////////////////////
            // ルート明細の登録
            //////////////////////
            foreach($latlngs as $row) {
                RouteDetail::create([
                    'route_id' => $route->id,
                    'lat' => $row[0],
                    'lng' => $row[1],
                ]);
            }
            /////////////////////
            // UserRoutemの登録
            ////////////////////
            $request->user()->owner($route->id);
        });
        
        
        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = [];

        if (\Auth::check()) {
            $user = \Auth::user();
            $routes = \DB::table('route_detail')
                ->join('routes', 'route_detail.route_id', '=', 'routes.id')
                ->select('routes.id','routes.description','routes.static_map_url','routes.zoom','routes.center_lat','routes.center_lng',
                         'route_detail.lat','route_detail.lng','routes.created_at')
                ->where('routes.id', $id)
                ->orderby('created_at', 'desc')
                ->get();
            
            
            // latlngを配列へ
            foreach ($routes as $route) {
                $latlngs[] = "new google.maps.LatLng(" . $route->lat . "," . $route->lng . "),";
            }
    
            $latlngs[count($latlngs)-1] = rtrim($latlngs[count($latlngs)-1], ',');
            
            // var_dump($routes);
            
            $data = [
                'user' => $user,
                'route' => $routes[0],
                'latlngs' => $latlngs,
            ];
        }
 
        return view('routes.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    
    
    public function getStaticMapUrl($latlngs) {
        // ポリラインエンコードしてURLを出力（実際は改行は入っていない）
        return "http://maps.google.com/maps/api/staticmap?sensor=false&size=640x640&maptype=roadmap&path=color:0x0000ffFF%7Cweight:5%7Cenc:".$this->latlon2GooglePolyline($latlngs)."\n";
    }
    // 配列に入った緯度経度をポリラインエンコードする
    // １つのポイントは array(緯度10進数,経度10進数) の形式
    // ポイントを array( ポイント, ポイント, ... ) で列挙
    function latlon2GooglePolyline($data)
    {
        $plat    = false;    // 1つ前の緯度
        $plon    = false;    // 1つ前の経度
        $query   = "";
        
        foreach($data as $point){
        // 初回以外は差分を取る
            $lat    = $plat !== false ? $point[0] - $plat : $point[0];
            $lon    = $plon !== false ? $point[1] - $plon : $point[1];
            
            //var_dump($point[0]." ".$point[1]);
            // 緯度と経度をポリラインエンコードしてクエリに追加
            $query .= $this->GooglePolyline_Encode($lat);
            $query .= $this->GooglePolyline_Encode($lon);
    
            $plat    = $point[0];
            $plon    = $point[1];
        }
    
        return $query;
    }
    
    // 単体の緯度または経度をポリラインエンコードする
    // 入力値 $value は10進数の緯度または経度
    function GooglePolyline_Encode($value)
    {
        $value    = ($value * pow(10,5)) << 1;    // ステップ2,3,4: 10^5を掛け左1ビットシフト
        if( $value < 0 ) $value = ~$value;        // ステップ5: 元の数値が負の時はビット反転
    
        //ステップ6,7: 下位から5ビットずつ切り出す（逆順になる）
        for($i=0;$i<6;$i++){
            $arr[$i]    = $value & 0x0000001F;
            $value    = $value >> 5;
        }
        
        // 不要なバイトを取り除く（上位で0が続く間そのバイトは除去、但し0の時は1バイト残す）
        while( $arr[ count($arr)-1 ] === 0 && count($arr) > 1 ){
            array_pop($arr);
        }
    
        //ステップ8: 最下位バイト以外に後続ビットを立てる（0x20論理和を加算）
        for($i=0;$i<count($arr)-1;$i++){
            $arr[$i]    |= 0x20;
        }
    
        //ステップ9,10,11: 各バイトに63を加算してASCII文字に変換
        $ret    = '';
        for($i=0;$i<count($arr);$i++){
            $ret    .= chr($arr[$i]+63);
        }
        return $ret;
    }
}
