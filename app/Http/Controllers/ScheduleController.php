<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use  Illuminate\Support\Facades\Validator;

use App\Cities;
use App\Schedules;

class ScheduleController extends Controller
{
    public function __construct(){
        //$this->middleware('api.auth', ['except' =>['index', 'show']]);
    }

   public function index(Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
    
      //$assignado = Assigned::all();
      if (isset($params_array['id_ciudad'])){ 
        //entrega todo sin que revise a que ciudad pertence
        $horarios = DB::table('horarios') 
        ->join('ciudades','horarios.id_ciudad','=', 'ciudades.id')
        ->select(['horarios.id as id_horarios','hora_inicio','hora_fin', 'id_ciudad',  'ciudades.nombre as ciudad' ])
        ->where('horarios.id_ciudad', '=',  $params_array['id_ciudad'])
        ->get();

    }else { 
        $horarios = DB::table('horarios') 
        ->join('ciudades','horarios.id_ciudad','=', 'ciudades.id')
        ->select(['hora_inicio','hora_fin', 'id_ciudad',  'ciudades.nombre as ciudad' ])
        ->get();
      }
        return response()->json([
            'code' => 200,
            'status' => 'success',
            'horarios' => $horarios
        ]);
        
    }

    public function show($id){
        $horario = Schedules::find($id);
        if(is_object($horario)){
            $data =[
                'code' => 200,
                'status' => 'success',
                'horario' => $horario
            ];
        }else{
            $data =[
                'code' => 404,
                'status' => 'error',
                'message' => 'El horario no se ha localizado'
            ];
        }
        return response()->json($data, $data['code']);
    }


    public function store(Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'hora_inicio' => 'required|numeric',
                'hora_fin' => 'required|numeric',
                'ciudad' => 'required|alpha',
            ]);
  

            $ciudad = new Cities();                
            $id_ciudad = $ciudad->ret_ID($params_array['ciudad']); //buscar el id

            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El horario no se ha creado',
                    'errors' => $validate->errors()
                );
            }elseif( $id_ciudad==0 ) {
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El horario no se ha creado, la ciudad no existe en la base de datos.',
                );
            }else{

                $horario = new Schedules();
                $horario->id_ciudad = $id_ciudad ; //buscar el id
                $horario->hora_inicio = $params_array['hora_inicio'];
                $horario->hora_fin = $params_array['hora_fin'];
                $horario->save();
                
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'horario' => $horario
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos del horario'
            ];
        }
        return response()->json($data, $data['code']);

    }

    public function update($id, Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'hora_inicio' => 'required|numeric',
                'hora_fin' => 'required|numeric',
                'ciudad' => 'required|alpha',
            ]);


            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El horario no se ha creado',
                    'errors' => $validate->errors()
                );            
            }else{


                $horario =  Schedules::firstOrNew (['id'=> $id]);
                unset($params_array['id']);

                $horario->hora_inicio = $params_array['hora_inicio'];
                $horario->hora_fin = $params_array['hora_fin'];


                $horario->save();
               
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'horario' => $horario
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos del horario'
            ];
        }
        return response()->json($data, $data['code']);



    }

    public function destroy($id, Request $request){
        $horario = Schedules::find($id);
        if(!empty($horario)){
            $horario->delete();
               
            $data =[
                'code' => 200,
                'status' => 'success',
                'horario' => $horario
            ];
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'El horario no existe.'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function getSchedulesOfCity($idciudad, Request $request){
        $horarios = Schedules::where('id_ciudad', '=', $idciudad)->get();
        if(!empty($horarios)){        
               $data =[
                   'code' => 200,
                   'status' => 'success',
                   'horarios' => $horarios
               ];
           }else{
               $data =[
                   'code' => 400,
                   'status' => 'error',
                   'message' => 'La ciudad no existe.'
               ];
           }
           return response()->json($data, $data['code']);

    }
}
