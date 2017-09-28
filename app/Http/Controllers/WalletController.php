<?php

namespace App\Http\Controllers;

use App\Address;
use App\Aureus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Transaction;
use App\Http\Requests\WithdrawRequest;

class WalletController extends Controller
{
    protected $crypto;

    /**
     * WalletController constructor.
     */
    function __construct(){
        $this->crypto = new Aureus(config('constants.rpcuser'), config('constants.rpcpassword'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function getBalance(){
        //$balance = $this->crypto->getbalance("");
        return response()->json([
            'balance' => number_format(Auth::user()->balance, 8, '.', '')
        ], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function getAddresses(){
        // $addresses = $this->crypto->getaddressesbyaccount("");
        $addresses = Auth::user()->addresses()->get();
        return response()->json([
            'addresses' => $addresses
        ], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function getNewAddress(){
        $address = $this->crypto->getnewaddress("");
        $addressModel = new Address();
        $addressModel->user_id = Auth::user()->id;
        $addressModel->address = $address;
        $addressModel->save();
        return response()->json([
            'address' => $address
        ], 200);
    }

    protected function getWithdrawFee(){
        return response()->json([
            'fee' => config('constants.withdraw_fee')
        ], 200);
    }

    protected function withdraw(WithdrawRequest $request){
        $amount = (double)$request->amount;
        $address = $request->address;
        if ($amount + (double)config('constants.withdraw_fee') <= Auth::user()->balance) {

            $validate = $this->crypto->validateaddress($address);
            if($validate['isvalid']){

                $response = $this->crypto->sendtoaddress($address, $amount);
                if ($response != false) {
                    
                    // Auth::user()->balance -= $amount + (double)config('constants.withdraw_fee');
                    // Auth::user()->save();
                    Auth::user()->addMoney( - $amount - (double)config('constants.withdraw_fee') );

                    // save transaction
                    $transaction = $this->crypto->gettransaction($response);
                    $sent = Transaction::forceCreate($transaction);

                    // response to client
                    return response()->json([
                        'status' => true,
                        'address' => $address,
                        'amount' => $amount,
                        'fee' => (double)config('constants.withdraw_fee'),
                        'transaction' => $sent,
                    ], 200);
                }
                return response()->json([
                    'status' => false,
                    'message' => 'System Error',
                ], 400);
            }
            return response()->json([
                'status' => false,
                'message' => 'Aureus address invalid',
            ], 400);
        }
        return response()->json([
            'status' => false,
            'message' => 'Insufficient funds',
        ], 400);
    }

    

}
