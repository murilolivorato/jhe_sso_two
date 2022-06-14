<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SSOController;
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

Auth::routes(['register' => false, 'reset' => false]);
Route::get('/sso-login', [SSOController::class, 'getLogin'])->name("sso.login");
Route::get('/callback', [SSOController::class, 'getCallBack'])->name("sso.callback");
Route::get('/connect-user', [SSOController::class, 'connectUser'])->name("sso.connect");
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('index');
