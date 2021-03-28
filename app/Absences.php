<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Absences extends Model
{
    protected $table = 'ausencias';
    protected $fillable = [
        'id'
    ];
}
