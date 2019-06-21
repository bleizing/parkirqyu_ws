<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_type',
    ];

    public function user()
    {
    	return $this->belongsTo('App\User');
    }

    public function transaction()
    {
    	return $this->hasOne('App\Transaction');
    }

    public function vehicle()
    {
        return $this->belongsTo('App\Vehicle');
    }
}
