<?php

namespace App\Http\Controllers;

use App\Address;
use App\Aureus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Transaction;

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
            'balance' => Auth::user()->balance
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

    protected function updateBalance(){ // AG3TqTKPw1SviKjAWFNtQMimq4sWZSgUMD
        $transactions = $this->crypto->listtransactions("");
        $addresses = Auth::user()->addresses()->get();
        foreach ($transactions as $transaction) {

            $transactionStored = Transaction::find($transaction['txid']);
            if($transactionStored == null){
                foreach ($addresses as $address) {
                    if($transaction['address'] == $address->address && $transaction['category'] == 'receive'){
                        $transactionStored = new Transaction();
                        $transactionStored->txid        = $transaction['txid'];
                        $transactionStored->address     = $transaction['address'];
                        $transactionStored->category    = $transaction['category'];
                        $transactionStored->amount      = $transaction['amount'];
                        $transactionStored->blockhash   = $transaction['blockhash'];
                        $transactionStored->blocktime   = $transaction['blocktime'];
                        $transactionStored->save();
                        // array_push($result, $transaction);
                        
                        Auth::user()->addMoney($transactionStored->amount);
                    }
                }
            }
            else{
                // do nothing
            }
        }
        // foreach($transactions as $transaction)
        // {
        //     $addresses = Auth::user()->addresses()->get();
        //     foreach ($addresses as $address) {
        //         if($transaction['address'] ==  $address)
        //         {
        //             array_push($result, $transaction);
        //         }
        //     }
        // }
        return response()->json([
            'balance' => Auth::user()->balance,
        ], 200);
    }

}
