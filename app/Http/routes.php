<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// トップページ
Route::get('/', 'WelcomeController@index');
// ユーザ登録
Route::get('signup', 'Auth\AuthController@getRegister')->name('signup.get');
Route::post('signup', 'Auth\AuthController@postRegister')->name('signup.post');
// ログイン認証
Route::get('login', 'Auth\AuthController@getLogin')->name('login.get');
Route::post('login', 'Auth\AuthController@postLogin')->name('login.post');
Route::get('logout', 'Auth\AuthController@getLogout')->name('logout.get');

Route::group(['middleware' => 'auth'], function () {
    // マイページ
    Route::resource('users', 'UsersController', ['only' => ['show']]);
    // ルート
    Route::resource('routes', 'RoutesController', ['only' => ['index','create','show','store', 'destroy']]);
    // お気に入り
    Route::post('favorite', 'UserRouteController@favo')->name('routes.favorite');
    Route::delete('favorite', 'UserRouteController@un_favo')->name('routes.unfavorite');
});

