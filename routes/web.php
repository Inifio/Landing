<?php

use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\GuzzleException;
use \GuzzleHttp\Psr7;
use \GuzzleHttp\Exception\RequestException;
use \GuzzleHttp\Exception\ServerException;

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
    return view('welcome');
});

Route::get('/login', function () {
    return redirect("https://api.restream.io/login?response_type=code&client_id=3b5d20d7-2860-49da-bf54-e4f8c6dad488&redirect_uri=http://127.0.0.1:8000/login/token&state=test2");
});
Route::get('/login/token', 'LoginController@store');


Route::get('/show/{username}', 'LoginController@show');