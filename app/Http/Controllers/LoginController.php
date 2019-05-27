<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \GuzzleHttp\Exception\ServerException;
use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\GuzzleException;
use \GuzzleHttp\Psr7;
use Carbon\Carbon;

use App\Users;
use DB;

class LoginController extends Controller
{
    public function store() {
        $client = new Client();
        $returnedCode = request('code');

        try {
            $response = $client->request('POST', 'https://api.restream.io/oauth/token', [
                'form_params' => [
                    'grant_type'    => 'authorization_code',
                    'redirect_uri'  => 'http://127.0.0.1:8000/login/token',
                    'code'          => $returnedCode
                ],
                'auth' => [
                    '3b5d20d7-2860-49da-bf54-e4f8c6dad488',
                    'd26ac74f-eb81-434f-8fa0-e8dfe3366828'
                ]
            ]);
            $response = json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            $errorMessage = $e->getMessage();
            if (!$e->hasResponse()) {
                $exceptionMessage =
                    __METHOD__ . '. Received invalid response from Twitch API: ' . $errorMessage .
                    '. Response: No response.';
                return redirect("/")->with('error', $exceptionMessage);
            }

            $response = $e->getResponse();

            $responseBody = $response->getBody();
            $responseDecoded = json_decode($responseBody, true);

            //dd($responseDecoded);
            return redirect("/")->with('error', $responseDecoded["error"]["message"]);
        }

        try {
            $profileData = $client->request('GET', 'https://api.restream.io/v2/user/profile', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $response["access_token"],
                ]
            ]);
            $profileData = json_decode($profileData->getBody()->getContents(), true);
            if ($user = DB::table('users')->where('username', $profileData["username"])->first()) {
                return redirect()->action(
                    'LoginController@show', ['username' => $profileData["username"]]
                );
            }
        } catch (GuzzleException $e) {
            $errorMessage = $e->getMessage();
            $exceptionMessage = __METHOD__ . ". Received ServerException: $errorMessage";
            if (!$e->hasResponse()) {
                $exceptionMessage =
                    __METHOD__ . '. Received invalid response from Twitch API: ' . $errorMessage .
                    '. Response: No response.';
                return redirect("/")->with('error', $exceptionMessage);
            }

            $response = $e->getResponse();

            $responseBody = $response->getBody();
            $responseDecoded = json_decode($responseBody, true);

            //dd($responseDecoded);
            return redirect("/")->with('error', $responseDecoded["error"]["message"]);
        }


        // Creating a new user and saving it to the database
        $user = new Users();
        $user->id = $profileData["id"];
        $user->username = $profileData["username"];
        $user->email = $profileData["email"];
        $user->auth_code = $response["access_token"];
        $user->auth_code_epoch = $response["accessTokenExpiresEpoch"];
        $user->refresh_code = $response["refresh_token"];
        $user->refresh_code_epoch = $response["refreshTokenExpiresEpoch"];
        $user->save();

        //dd($profileData);

        return redirect()->action(
            'LoginController@show', ['username' => $profileData["username"]]
        );
    }

    public function show($username) {
        $client  = new Client();
        $user = DB::table('users')
            ->where('username', $username)
            ->first();

        //dd($user);

        // Check the expiration of the access code, if expired, get a new one
        $current_timestamp = Carbon::now()->timestamp;
        if ($current_timestamp >= $user->auth_code_epoch) {
            $this->refreshToken($username);
        }

        try {   // Getting a list of channels user has added on Restream
            $channels = $client->request('GET', 'https://api.restream.io/v2/user/channel/all', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $user->auth_code
                ]
            ]);
            $channels = json_decode($channels->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            $errorMessage = $e->getMessage();
            $exceptionMessage = __METHOD__ . ". Received ServerException: $errorMessage";
            if (!$e->hasResponse()) {
                $exceptionMessage =
                    __METHOD__ . '. Received invalid response from Twitch API: ' . $errorMessage .
                    '. Response: No response.';
                return redirect("/")->with('error', $exceptionMessage);
            }

            $response = $e->getResponse();

            $responseBody = $response->getBody();
            $responseDecoded = json_decode($responseBody, true);

            //dd($responseDecoded);
            return redirect("/")->with('error', $responseDecoded["error"]["message"]);
        }

        try {   // Get list of platforms and IDs
            $response = $client->request('GET', 'https://api.restream.io/v2/platform/all');
            $platform_ids = json_decode($response->getBody()->getContents(), true);
        } catch(GuzzleException $e) {
            $errorMessage = $e->getMessage();
            $exceptionMessage = __METHOD__ . ". Received ServerException: $errorMessage";
            if (!$e->hasResponse()) {
                $exceptionMessage =
                    __METHOD__ . '. Received invalid response from Twitch API: ' . $errorMessage .
                    '. Response: No response.';
                return redirect("/")->with('error', $exceptionMessage);
            }

            $response = $e->getResponse();

            $responseBody = $response->getBody();
            $responseDecoded = json_decode($responseBody, true);

            //dd($responseDecoded);
            return redirect("/")->with('error', $responseDecoded["error"]["message"]);
        }

        $channelList = array(); // Creating array for use bellow
        $embedPlayer = null;
        for ($i = 0; $i < count($channels); $i++) {
            for($j = 0; $j < count($platform_ids); $j++) {
                if($platform_ids[$j]["id"] == $channels[$i]["streamingPlatformId"]) {
                    // Enable embed if there's an embedable platform
                    if($embedPlayer === null && $channels[$i]["enabled"] === true && $channels[$i]["embedUrl"] !== "") {
                        //dd($channels[$i]);
                        $embedPlayer = [
                           "embedEnabled" => true,
                           "platform" => $platform_ids[$j]["name"],
                           "embedURL" => $channels[$i]["embedUrl"],
                            "displayName" => $channels[$i]["displayName"],
                        ];
                        //dd($embedPlayer);
                    }
                    array_push($channelList, [
                        "platformId" =>  $platform_ids[$j]["name"],
                        "platformImage" => $platform_ids[$j]["image"]["png"],
                        "url" => $channels[$i]["url"],
                        "displayName" => $channels[$i]["displayName"],
                        "enabled" => $channels[$i]["enabled"]
                    ]);
                }
            }
        }

        //dd($embedPlayer);
        return \response()->view('show', [
            'channels' => $channelList,
            'embed' => $embedPlayer
        ]);

    }

    private function refreshToken($username) {
        $user = DB::table('users')->where('username', $username)->first();
        //dd($user);
        $client = new Client();

        try {
            $response = $client->request('POST', 'https://api.restream.io/oauth/token', [
                'form_params' => [
                    'grant_type'    => 'refresh_token',
                    'redirect_uri'  => 'http://127.0.0.1:8000/login/token',
                    'refresh_token' => $user->refresh_code
                ],
                'auth' => [
                    '3b5d20d7-2860-49da-bf54-e4f8c6dad488',
                    'd26ac74f-eb81-434f-8fa0-e8dfe3366828'
                ]
            ]);
            $response = json_decode($response->getBody()->getContents(), true);

        } catch (GuzzleException $e) {
            // TODO: Setup exceptions
            $response = $e;
        }

        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'auth_code' => $response["access_token"],
                'auth_code_epoch' => $response["accessTokenExpiresEpoch"],
                'refresh_code' => $response["refresh_token"],
                'refresh_code_epoch' => $response["refreshTokenExpiresEpoch"]
            ]);

        return redirect()->action(
            'LoginController@show', ['username' => $username]
        );
    }
}
