<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

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
        $items = [];
        
        // ユーザに関連するルート取得
        $items = \DB::table('routes')
            ->join('route_detail', 'routes.id', '=', 'route_detail.route_id')
            ->join('user_route'  , 'routes.id', '=', 'user_route.route_id')
            ->select('routes.id','routes.description')
            ->where('user_route.user_id', $user->id)
            ->paginate(10);

        return view('users.show', [
            'user' => $user,
            'routes' => $routes,
            'count_owner' => $count_owner,
            'count_favorite' => $count_favorite,
        ]);
    }

}
