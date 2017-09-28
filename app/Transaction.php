<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class Transaction extends Model
{
    protected $collection = 'transactions';
    protected $primaryKey = 'txid';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'address_id', 'updated_at', 'created_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function address(){
        return $this->belongsTo(Address::class);
    }

}
