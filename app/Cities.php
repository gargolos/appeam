<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cities extends Model
{
    protected $table = 'ciudades';
    protected $fillable = [
        'id'
    ];
    

    public function ret_ID($nombre){
        //Devuelve el Id recibiendo el nombre
        $city =Cities::where('nombre', 'like', '%' . $nombre . '%')->get();
        $params_array = json_decode($city, true); //arreglo
        if(!empty($params_array)){
            return $params_array[0]['id'];}
            else { return 0;}
    }

    
}
