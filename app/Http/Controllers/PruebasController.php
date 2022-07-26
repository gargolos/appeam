<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use  Illuminate\Support\Facades\Validator;

use App\Cities;
//use App\Participants;

class PruebasController extends Controller
{
    //
    public function testORM(){
       /*
        $participantes = Participants::all();
 

   //     $cuenta=$participantes->validaRef('BRTE7701140');  


        foreach($participantes as $participante){
            echo "<h2>". $participante->n ."</h2>";
     //       echo "<h2>". $cuenta ."</h2>";
            echo  "<hr>";
        }
   

        echo  "<hr>";
        die();
*/

echo "<h2>Hola Test</h2>";

$ciudades = Cities::all();
     return response()->json([
            'code' => 200,
            'status' => 'success',
            'ciudades' => $ciudades
        ]);
    }
}
