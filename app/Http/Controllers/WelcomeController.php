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
            
            $routes = \DB::table('routes')->orderBy('created_at', 'desc')->paginate(10);
            
            $data = [
                'user' => $user,
                'routes' => $routes,
                'keyword' => "",
            ];
        }
        return view('welcome', $data);
    }

   
}
