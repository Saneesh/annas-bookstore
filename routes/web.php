<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/redirect', function () {
    $query = http_build_query([
        'client_id' => env('PASSPORT_CLIENT_ID'),
        'redirect_uri' => env('APP_URL').'/callback',
        // 'redirect_uri' => 'http://localhost/auth/callback',
        'response_type' => 'token',
        'scope' => '',
    ]);

    return redirect(env('BOOKSTORE_URL').'/oauth/authorize?'.$query);
});

Route::get('/callback', function (Request $request) {
    $http = new GuzzleHttp\Client;

    $response = $http->post(env('BOOKSTORE_URL').'/oauth/token', [
        'form_params' => [
            'grant_type' => 'authorization_code',
            'client_id' => env('PASSPORT_CLIENT_ID'),
            'client_secret' => env('PASSPORT_CLIENT_SECRET'),
            'redirect_uri' => env('APP_URL').'/callback',
            'code' => $request->code,
        ],
    ]);
    $tokens = json_decode((string) $response->getBody(), true);
    $user = fetchUser($tokens['access_token'], $http);

    return view('authenticated', array_merge($tokens, $user));
});

if(!function_exists("fetchUser")) {
    function fetchUser($accessToken, $http){
        $response = $http->get(env('BOOKSTORE_URL').'/api/user', [
            'headers' =>[
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$accessToken,
            ]
        ]);

        return json_decode((string) $response->getBody(), true);
    }
}