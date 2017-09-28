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
    // Route::get('updatebalance', 'WalletController@updateBalance');
    Route::get('getwithdrawfee', 'WalletController@getWithdrawFee');
    Route::post('withdraw', 'WalletController@withdraw');

    Route::get('temp', function (){
        $crypto = new \App\Aureus(config('constants.rpcuser'), config('constants.rpcpassword'));
        $transactions = $crypto->listtransactions();
        $transaction = $crypto->gettransaction('52155ad759c71cf43ceba4e8fa1f9b1dad489ca98ff9cb389191b3caa9322ec3');
        return response()->json([
            'data' => $transaction,
        ], 200);
        // $received = Transaction::forceCreate($crypto->gettransaction($transaction['txid']));
        // $user->addMoney($transaction['amount']);
    });
});