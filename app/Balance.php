<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    protected $fillable = [
        'user_id', 'nominal',
    ];

    public function user()
    {
    	return $this->belongsTo('App\User');
    }
}
