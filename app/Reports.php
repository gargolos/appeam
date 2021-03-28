<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reports extends Model
{
    protected $table = 'informes';
    protected $fillable = [
        'id'
    ];

    //Una funcion con los participantes del turno que regrese los ids de cada uno


}
