<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use  Illuminate\Support\Facades\Validator;

use App\Trainings;

class TrainingController extends Controller
{
    public function index(Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if (isset($params_array['id_ciudad'])){ 
            $capacitaciones = DB::table('capacitaciones') 
            ->leftjoin('eventos','capacitaciones.id_evento','=', 'eventos.id')
            ->join('participantes','capacitaciones.id_participante','=', 'participantes.id') 
            ->select(['capacitaciones.id as id', 'capacitaciones.fecha as fecha','participantes.n as participante_n', 'participantes.ap as participante_ap','participantes.am as participante_am', 'participantes.id as id_participante', 'eventos.descripcion as descripcion', 'eventos.id as id_evento', 'eventos.fecha as fecha_evento',])
            ->where('eventos.id_ciudad', '=',  $params_array['id_ciudad'])
            ->get();
        }else{
        $capacitaciones = DB::table('capacitaciones') 
        ->join('eventos','capacitaciones.id_evento','=', 'eventos.id')
        ->join('participantes','capacitaciones.id_participante','=', 'participantes.id') 
        ->select(['capacitaciones.id as id', 'capacitaciones.fecha as fecha','participantes.n as participante_n', 'participantes.ap as participante_ap','participantes.am as participante_am', 'participantes.id as id_participante', 'eventos.descripcion as descripcion', 'eventos.id as id_evento', 'eventos.fecha as fecha_evento',])
        ->get();
        }
    //$capacitaciones = Trainings::all(); 

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'capacitaciones' => $capacitaciones
        ]);
        
    }

    public function show($id){
        $capacitaciones = Trainings::find($id);

        if(is_object($capacitaciones)){
            $data =[
                'code' => 200,
                'status' => 'success',
                'capacitaciones' => $capacitaciones
            ];
        }else{
            $data =[
                'code' => 404,
                'status' => 'error',
                'message' => 'La capacitacion no se ha localizado'
            ];
        }
        return response()->json($data, $data['code']);
    }


    public function store(Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'id_evento' => 'required',
                'id_participante' => 'required',
            ]);
          //  $ciudad = new Cities();                
           // $id_ciudad = $ciudad->ret_ID($params_array['ciudad']); //buscar el id

            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'La capacitacion no se ha creado',
                    'errors' => $validate->errors()
                );            
            }else{

                $capacitacion = new Trainings();
                $capacitacion->id_evento = $params_array['id_evento'];
                $capacitacion->fecha = $params_array['fecha'];
                $capacitacion->id_participante = $params_array['id_participante'];
                $capacitacion->save();
                
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'capacitacion' => $capacitacion
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos de la capacitacion'
            ];
        }
        return response()->json($data, $data['code']);

    }

    public function update($id, Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'id_evento' => 'required',
                'id_participante' => 'required',
            ]);



            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'La capacitacion no se ha creado',
                    'errors' => $validate->errors()
                );            
            }else{


                $capacitacion =  Trainings::firstOrNew (['id'=> $id]);
                unset($params_array['id']);
                $capacitacion->id_evento = $params_array['id_evento'];
                $capacitacion->fecha = $params_array['fecha'];
                $capacitacion->id_participante = $params_array['id_participante'];

                $capacitacion->save();
               
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'capacitacion' => $capacitacion
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos de la capacitacion'
            ];
        }
        return response()->json($data, $data['code']);

    }

    public function destroy($id){
        $capacitacion = Trainings::find($id);
        if(!empty($capacitacion)){
            $capacitacion->delete();
               
            $data =[
                'code' => 200,
                'status' => 'success',
                'capacitaciones' => $capacitacion
            ];
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'La capacitacion no existe.'
            ];
        }
        return response()->json($data, $data['code']);
    }
}
