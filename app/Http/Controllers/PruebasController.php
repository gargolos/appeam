<?php

namespace App\Http\Controllers;
use App\Cities;
use App\Participants;
use Illuminate\Http\Request;

class PruebasController extends Controller
{
    //
    public function testORM(){
        /*
        $participantes = Participants::all();
 

        $cuenta=$participantes->validaRef('BRTE7701140');  


        foreach($participantes as $participante){
            echo "<h2>". $participante->n ."</h2>";
          //  echo "<h2>". $cuenta ."</h2>";
            echo  "<hr>";
        }
   

        echo  "<hr>";
        die();
*/
echo  "<h2>TEST<hr></h2>"; die();
    }
}
