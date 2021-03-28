<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Participants extends Model
{
    protected $table = 'participantes';
    protected $fillable = [
        'id'
    ];

    //relacion de uno a muchos
    public function ciudad(){
        return $this->belongsTo('App\Cities', 'id_ciudad' );
    }

    public function circuito(){
        return $this->belongsTo('App\Circuits', 'id_circuito' );
    }

    public function turno(){
        return $this->belongsTo('App\Shifths', 'id_turno' );
    }


}
