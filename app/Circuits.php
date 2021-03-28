<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Circuits extends Model
{
    protected $table = 'circuitos';
    protected $fillable = [
        'id'
    ];
        //relacion de uno a muchos
        public function users(){
            return $this->hasMany('App\User');
        }

        public function ret_ID($nombre, $idciudad){
            //Devuelve el Id recibiendo el nombre
            $circuit =Circuits::where('nombre', 'like', '%' . $nombre . '%')->Where('id_ciudad', '=', $idciudad)->get();
            $params_array = json_decode($circuit, true); //arreglo
            if(!empty($params_array)){
            return $params_array[0]['id'];}
            else { return 0;}
        }

        public function city(){
            return $this->hasOne('App\City');
        }
        
        public function participants(){
            return $this->hasMany('App\Participants');
        }


        
}
