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

Route::get('/oauth/redirect', function() {
    $client = new Client();
    $returnedCode = request('code');

    $response = null;

    try {
    $response = $client->request('POST', 'https://api.restream.io/oauth/token', [
        'form_params' => [
            'grant_type'    => 'authorization_code',
            'redirect_uri'  => 'http://127.0.0.1:8000/oauth/redirect',
            'code'          => $returnedCode
        ],
        'auth' => [
            '3b5d20d7-2860-49da-bf54-e4f8c6dad488',
            'abcbc42e-c562-4ba8-8b2d-998fc778b824'
        ]
    ]);
    //$response = $response->getBody()->getContents();
    $response = json_decode($response->getBody()->getContents(), true);
    } catch (GuzzleException $e) {
        $errorMessage = $e->getMessage();
        $exceptionMessage = __METHOD__ . '. Received GuzzleException: ' . $errorMessage;
        //throw new ApiInvalidResponseException($exceptionMessage);
    }
    

    if ($response != null){
        //dd($response);
        return view('show', [
            'code'          => $returnedCode,
            'access_token'  => $response["access_token"],
            'refresh_code'  => $response["refresh_token"],
            'permission'    => true
        ]);
    } else {
        return view('show', [
            'permission' => false
        ]);
    }
});
