<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParkirRate extends Model
{
    protected $fillable = [
        'satu_jam_pertama', 'tiap_jam', 'per_hari', 'parkir_type',
    ];
}
