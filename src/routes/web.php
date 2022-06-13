<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
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

Route::get('/login', function (Request $request) {
    $request->session()->put("state", $state = Str::random(40));
    $query = http_build_query([
        "client_id" => "96886ab2-045e-47ac-9004-dd28787dfd72",
        "redirect_url" => "http://localhost:8080/callback",
        "response_type" => "code",
        "scope" => "",
        "state" => $state
    ]);
    return redirect("http://localhost:8080/oauth/authorize?" . $query);
});

Route::get('/callback', function (Request $request) {
    $state = $request->session()->pull("state");

    throw_unless(strlen($state) > 0 && $state == $request->state, InvalidArgumentException::class);
    $response = Http::asForm()->post(
        "http://localhost:8080/oauth/token",
        [
        "grand_type" => "authorization_code",
        "client_id" => "96886ab2-045e-47ac-9004-dd28787dfd72",
        "client_secret" => "ziEBXJR2OQvio2b6K8WWStw8bReZyfrrnOHMf1nk",
        "redirect_url" => "http://localhost:8080/callback",
        "code" => $request->code

    ]);
});
