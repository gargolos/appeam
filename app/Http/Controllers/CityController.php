<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use  Illuminate\Support\Facades\Validator;

use App\Cities;


class CityController extends Controller
{
    public function __construct(){
        //$this->middleware('api.auth', ['except' =>['index', 'show']]);
    }

    public function index(){
        $ciudades = Cities::all();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'ciudades' => $ciudades
        ]);
        
    }

    public function show($id){
        $ciudades = Cities::find($id);
        if(is_object($ciudades)){
            $data =[
                'code' => 200,
                'status' => 'success',
                'ciudades' => $ciudades
            ];
        }else{
            $data =[
                'code' => 404,
                'status' => 'error',
                'message' => 'El circuito no se ha localizado'
            ];
        }
        return response()->json($data, $data['code']);
    }


    public function store(Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'nombre' => 'required|string',
            ]);
  

            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'La ciudad no se ha creado',
                    'errors' => $validate->errors()
                );            
            }else{

                $ciudad = new Cities();
                $ciudad->nombre = $params_array['nombre'];

                $ciudad->save();
                
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'ciudad' => $ciudad
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos de la ciudad'
            ];
        }
        return response()->json($data, $data['code']);

    }

    public function update($id, Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'nombre' => 'required|string',
            ]);


            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El ciudad no se ha creado',
                    'errors' => $validate->errors()
                );            
            }else{


                $ciudad =  Cities::firstOrNew (['id'=> $id]);
                unset($params_array['id']);

                $ciudad->nombre = $params_array['nombre'];

                $ciudad->save();
               
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'ciudad' => $ciudad
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos de la ciudad'
            ];
        }
        return response()->json($data, $data['code']);



    }

    public function destroy($id, Request $request){
        $ciudad = Cities::find($id);
        if(!empty($ciudad)){
            $ciudad->delete();
               
            $data =[
                'code' => 200,
                'status' => 'success',
                'ciudades' => $ciudad
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
