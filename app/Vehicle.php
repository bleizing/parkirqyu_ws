<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'nomor_registrasi', 'vehicle_type',
    ];

    public function user()
    {
    	return $this->belongsTo('App\User');
    }

    public function invoices()
    {
    	return $this->hasMany('App\Invoice');
    }
}
