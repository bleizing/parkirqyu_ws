<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'user_id', 'invoice_type', 'nominal',
    ];

    public function user()
    {
    	return $this->belongsTo('App\User');
    }

    public function transaction()
    {
    	return $this->hasOne('App\Transaction');
    }
}
