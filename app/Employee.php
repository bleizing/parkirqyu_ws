<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'user_id', 'nama', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'alamat'
    ];

    public function user()
    {
    	return $this->belongsTo('App\User');
    }
}
