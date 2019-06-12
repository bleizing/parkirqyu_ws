<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'user_type',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        
    ];

    public function employee()
    {
        return $this->hasOne('App\Employee');
    }

    public function vehicles()
    {
        return $this->hasMany('App\Vehicle');
    }

    public function balance()
    {
        return $this->hasOne('App\Balance');
    }

    public function invoices()
    {
        return $this->hasMany('App\Invoice');
    }

    public function petugas_transaction()
    {
        return $this->hasOne('App\Transaction', 'petugas_id', 'id');
    }
}
