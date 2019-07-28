<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'invoice_id', 'transaction_type',
    ];

    public function invoice()
    {
    	return $this->belongsTo('App\Invoice');
    }

    public function petugas()
    {
    	return $this->belongsTo('App\User', 'petugas_id');
    }
}
