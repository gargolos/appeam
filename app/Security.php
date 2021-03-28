<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Security extends Model
{
    protected $table = 'security';

    //relacion de uno a muchos
    public function users(){
        return $this->hasMany('App\User');
    }
}
