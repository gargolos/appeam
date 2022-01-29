<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use  Illuminate\Support\Facades\Validator;

use App\Experiences;

class ExperienceController extends Controller
{
    public function index(Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

      if (isset($params_array['id_ciudad'])){ 
        $experiencias = DB::table('experiencias') 
        ->select(['*'])
        ->where('experiencias.id_ciudad', '=',  $params_array['id_ciudad'])
        ->get();
      }else{
        $experiencias = DB::table('experiencias') 
        ->select(['*'])
        ->get();   
      }
  
      //  $experiencias = Experiences::all(); 
      
      return response()->json([
        'code' => 200,
        'status' => 'success',
        'experiencias' => $experiencias
    ]);


        return response()->json([
            'code' => 200,
            'status' => 'success',
            'experiencias' => $experiencias
        ]);
        
    }

    public function show($id){
        $experiencias = Experiences::find($id);
        if(is_object($experiencias)){
            $data =[
                'code' => 200,
                'status' => 'success',
                'experiencias' => $experiencias
            ];
        }else{
            $data =[
                'code' => 404,
                'status' => 'error',
                'message' => 'La experiencia no se ha localizado'
            ];
        }
        return response()->json($data, $data['code']);
    }


    public function store(Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'id_ciudad' => 'required',
            ]);
          //  $ciudad = new Cities();                
           // $id_ciudad = $ciudad->ret_ID($params_array['ciudad']); //buscar el id

            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'La experiencia no se ha creado',
                    'errors' => $validate->errors()
                );            
            }else{

                $experiencia = new Experiences();
                
                $experiencia->fecha = $params_array['fecha'];
                $experiencia->nombre = $params_array['nombre'];
                $experiencia->correo = $params_array['correo'];
                $experiencia->descripcion = $params_array['descripcion'];
                $experiencia->imagen = $params_array['imagen'];
                $experiencia->id_participante = $params_array['id_participante'];
                $experiencia->id_ciudad = $params_array['id_ciudad'];
                $experiencia->save();
                
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'experiencia' => $experiencia
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos de la experiencia'
            ];
        }
        return response()->json($data, $data['code']);

    }

    public function update($id, Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'id_ciudad' => 'required',
            ]);



            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'La experiencia no se ha creado',
                    'errors' => $validate->errors()
                );            
            }else{


                $experiencia =  Experiences::firstOrNew (['id'=> $id]);
                unset($params_array['id']);

                $experiencia->fecha = $params_array['fecha'];
                $experiencia->nombre = $params_array['nombre'];
                $experiencia->correo = $params_array['correo'];
                $experiencia->descripcion = $params_array['descripcion'];
                $experiencia->imagen = $params_array['imagen'];
                $experiencia->id_participante = $params_array['id_participante'];
                $experiencia->id_ciudad = $params_array['id_ciudad'];

                $experiencia->save();
               
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'experiencia' => $experiencia
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos de la experiencia'
            ];
        }
        return response()->json($data, $data['code']);

    }

    public function destroy($id){
        $experiencia = Experiences::find($id);
        if(!empty($experiencia)){
            $experiencia->delete();
               
            $data =[
                'code' => 200,
                'status' => 'success',
                'experiencias' => $experiencia
            ];
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'La experiencia no existe.'
            ];
        }
        return response()->json($data, $data['code']);
    }
}
