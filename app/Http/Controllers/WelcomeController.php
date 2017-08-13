<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Route;

class WelcomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $data = [];
        if (\Auth::check()) {
            $user = \Auth::user();
            $routes = \DB::table('route_detail')
                ->join('routes', 'route_detail.route_id', '=', 'routes.id')
                ->select('routes.id','routes.description','routes.static_map_url',
                         \DB::raw('GROUP_CONCAT(route_detail.lat) as lats,GROUP_CONCAT(route_detail.lng) as lngs'),
                         'routes.created_at')
                ->groupby('routes.id','routes.description','static_map_url','routes.created_at')
                ->orderby('created_at', 'desc')
                ->paginate(10);
//            $routes = Route::all();->orderby('created_at', 'desc')->paginate(10);
            
            $data = [
                'user' => $user,
                'routes' => $routes,
            ];
        }
        return view('welcome', $data);
    }

   
}
