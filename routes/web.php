<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if(Auth::check()) {
        return redirect('/show/'.Auth::user()->username);
    }
    return view('welcome');
});

Route::get('/logout', '\App\Http\Controllers\Auth\LoginController@logout');
Route::get('/login/token', 'LoginController@store');

/*Route::get('/login/token', function(Request $request) {
    dd($request->input('code'));
});*/

Route::get('/login', function () {
    return redirect("https://api.restream.io/login?response_type=code&client_id=3b5d20d7-2860-49da-bf54-e4f8c6dad488&redirect_uri=http://127.0.0.1:4000/login/token&state=test2");
});

Route::get('/show/{username}', 'LoginController@show');

Route::get('/enable/{username}/{channelId}', 'LoginController@enableChannel');
Route::get('/disable/{username}/{channelId}', 'LoginController@disableChannel');
Route::get('/remove_account', 'LoginController@destroy');