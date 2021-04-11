<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shifts extends Model
{
    protected $table = 'turnos';
    protected $fillable = [
        'id'
    ];
    //relacion de uno a muchos
   // public function users(){
   //     return $this->hasMany('App\User');
   // }
    public function ciudad(){
        return $this->belongsTo('App\Cities', 'id_ciudad' );
    }
}
