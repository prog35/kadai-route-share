<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;

class UsersController extends Controller
{
   
   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        $count_owner = $user->owner_routes()->count();
        $count_favorite = $user->favorite_routes()->count();
        
        // ユーザに関連するルート取得
        $routes = \DB::table('route_detail')
            ->join('routes', 'route_detail.route_id', '=', 'routes.id')
            ->join('user_route', 'routes.id', '=', 'user_route.route_id')
            ->select('routes.id','routes.description',
                     \DB::raw('GROUP_CONCAT(route_detail.latitude) as lats,GROUP_CONCAT(route_detail.longitude) as lons'),
                     'routes.created_at')
            ->where('user_route.user_id', $user->id)
            ->groupby('routes.id','routes.description','routes.created_at')
            ->orderby('created_at', 'desc')
            ->paginate(10);

        return view('users.show', [
            'user' => $user,
            'routes' => $routes,
            'count_owner' => $count_owner,
            'count_favorite' => $count_favorite,
        ]);
    }

}
