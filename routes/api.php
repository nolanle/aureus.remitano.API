<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->group(function () {
    Route::get('getbalance', 'WalletController@getBalance');
    Route::get('getaddresses', 'WalletController@getAddresses');
    Route::get('getnewaddress', 'WalletController@getNewAddress');
    Route::get('updatebalance', 'WalletController@updateBalance');
});