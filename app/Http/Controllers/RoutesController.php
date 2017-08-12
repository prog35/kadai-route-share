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

    }
    
    public function search($keyword)
    {
        // 登録されているルートを読み込み、ビューへ渡す
        $keyword = request()->keyword;
        
        if ($keyword) {
            $routes = Route::all()->where('','')->orderBy('created_at', 'desc')->paginate(10);
        }
        else {
            $routes = Route::all()->where('','')->orderBy('created_at', 'desc')->paginate(10);
        }
        
        $data = [
            'user' => $user,
            'routes' => $routes,
        ];
        
        return view('routes.index', [
            'keyword' => $keyword,
            'routes' => $routes,
        ]);
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
            
//　経路が登録されていない    
//         $this->validate($request, [
//             'info_window' => 'required',
//         ]);
        
        \DB::transaction(function() use ($request) {
            
            //改行コードを置換してLF改行コードに統一
            $info_window = trim(str_replace(array("\r\n","\r","\n"), "\n", $request->info_window));
            //LF改行コードで配列に格納
            $points = explode("\n", $info_window);
            
            // ２次元配列へ
            foreach ($points as $row) {
                $point = explode(',',$row);  
                $lat_lon[] = array($point[0],$point[1]);
            }
            
            // var_dump($this->getStaticMapUri($lat_lon));
            // return;
            //////////////////////
            // ルートの登録
            //////////////////////
            // ボタン名で一時保存、公開保存を判定する
            $status = 1;
            
            $route = Route::create([
                'description' => $request->description,
                'status' => $status,
                'polylin_latlon' => $this->getStaticMapUri($lat_lon),
            ]);
            //////////////////////
            // ルート明細の登録
            //////////////////////
            foreach($lat_lon as $row) {
                RouteDetail::create([
                    'route_id' => $route->id,
                    'latitude' => $row[0],
                    'longitude' => $row[1],
                ]);
            }
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
                ->select('routes.id','routes.description','routes.polylin_latlon',
                         'route_detail.latitude','route_detail.longitude','routes.created_at')
                ->where('routes.id', $id)
                ->orderby('created_at', 'desc')
                ->get();
            
            
            // 配列へ
            foreach ($routes as $route) {
                $latlons[] = "new google.maps.LatLng(" . $route->latitude . "," . $route->longitude . "),";
            }
    
            $latlons[count($latlons)-1] = rtrim($latlons[count($latlons)-1], ',');
            
            //var_dump($latlons);
            
            $data = [
                'user' => $user,
                'routes' => $routes,
                'latlons' => $latlons,
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
    
    
    public function getStaticMapUri($latlngs) {
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
