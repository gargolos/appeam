<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use  Illuminate\Support\Facades\Validator;

use App\Events;
use App\Cities;

class EventController extends Controller
{
    public function index(Request $request){
        
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
    
      //$assignado = Assigned::all();
      if (isset($params_array['id_ciudad'])){ 

        $eventos = DB::table('eventos') 
        ->select(['*'])
        ->where('eventos.id_ciudad', '=',  $params_array['id_ciudad'])
        ->get();
      }else{
        $eventos = Events::all(); 
      }
      return response()->json([
        'code' => 200,
        'status' => 'success',
        'eventos' => $eventos
    ]);


        return response()->json([
            'code' => 200,
            'status' => 'success',
            'eventos' => $eventos
        ]);
        
    }

    public function show($id){
        $eventos = Events::find($id);
        if(is_object($eventos)){
            $data =[
                'code' => 200,
                'status' => 'success',
                'eventos' => $eventos
            ];
        }else{
            $data =[
                'code' => 404,
                'status' => 'error',
                'message' => 'El evento no se ha localizado'
            ];
        }
        return response()->json($data, $data['code']);
    }


    public function store(Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'descripcion' => 'required|string',
                'id_ciudad' => 'required',
            ]);
          //  $ciudad = new Cities();                
           // $id_ciudad = $ciudad->ret_ID($params_array['ciudad']); //buscar el id

            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El evento no se ha creado',
                    'errors' => $validate->errors()
                );            
            }else{

                $evento = new Events();
                $evento->descripcion = $params_array['descripcion'];
                $evento->fecha = $params_array['fecha'];
                $evento->id_ciudad = $params_array['id_ciudad'];
                $evento->save();
                
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'evento' => $evento
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos de la evento'
            ];
        }
        return response()->json($data, $data['code']);

    }

    public function update($id, Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'descripcion' => 'required|string',
                'id_ciudad' => 'required',
            ]);



            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El evento no se ha creado',
                    'errors' => $validate->errors()
                );            
            }else{


                $evento =  Events::firstOrNew (['id'=> $id]);
                unset($params_array['id']);

                $evento->descripcion = $params_array['descripcion'];
                $evento->fecha = $params_array['fecha'];
                $evento->id_ciudad = $params_array['id_ciudad'];

                $evento->save();
               
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'evento' => $evento
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos de la evento'
            ];
        }
        return response()->json($data, $data['code']);

    }

    public function destroy($id){
        $evento = Events::find($id);
        if(!empty($evento)){
            $evento->delete();
               
            $data =[
                'code' => 200,
                'status' => 'success',
                'eventos' => $evento
            ];
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'El evento no existe.'
            ];
        }
        return response()->json($data, $data['code']);
    }

}
