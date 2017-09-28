<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Transaction;
use App\Address;
use App\Aureus;

class UpdateUserBalanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'balance:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Users Balance';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $crypto = new Aureus(config('constants.rpcuser'), config('constants.rpcpassword'));
        $users = User::all();
        foreach ($users as $user) {
            $transactions = $crypto->listtransactions("");
            $addresses = $user->addresses()->get();
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
                            $user->addMoney($transactionStored->amount);
                        }
                    }
                }
                else{
                    // do nothing
                }
            }
            echo $user->email . ' balance : ' . $user->balance;
        }
        
    }
}
