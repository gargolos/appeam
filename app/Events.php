<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    protected $table = 'eventos';
    protected $fillable = [
        'id'
    ];
}
