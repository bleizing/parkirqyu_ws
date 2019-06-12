<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'invoice_id', 'nominal'
    ];

    public function invoice()
    {
    	return $this->belongsTo('App\Invoice');
    }

    public function petugas()
    {
    	return $this->belongsTo('App\User', 'id', 'petugas_id');
    }
}
