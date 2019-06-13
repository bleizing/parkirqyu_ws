<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'user_id', 'nomor_registrasi', 'nama_pemilik', 'alamat', 'merk', 'type', 'tahun_pembuatan', 'nomor_rangka', 'nomor_mesin', 'vehicle_type',
    ];

    public function user()
    {
    	return $this->belongsTo('App\User');
    }
}
