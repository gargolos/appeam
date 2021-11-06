<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Locations extends Model
{
    protected $table = 'ubicaciones';
    protected $fillable = [
        'id'
    ];

    public function ret_ID($nombre, $idciudad){
        //Devuelve el Id recibiendo el nombre
        $circuit =Locations::where('nombre', 'like', '%' . $nombre . '%')->Where('id_ciudad', '=', $idciudad)->get();
        $params_array = json_decode($circuit, true); //arreglo
        if(!empty($params_array)){
        return $params_array[0]['id'];}
        else { return 0;}
    }
}
