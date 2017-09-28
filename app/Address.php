<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class Address extends Model
{
    protected $collection = 'addresses';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        '_id', 'user_id', 'updated_at', 'created_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function transactions(){
        return $this->hasMany(Transaction::class);
    }

}
