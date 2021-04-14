<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use  Illuminate\Support\Facades\Validator;

use App\Components;

class ComponentController extends Controller
{

    public function __construct(){
        //$this->middleware('api.auth', ['except' =>['index', 'show']]);
    }

    public function index(){
        $componentes = Components::all();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'componentes' => $componentes
        ]);
        
    }

    public function show($id){
        $componentes = Components::find($id);
        if(is_object($componentes)){
            $data =[
                'code' => 200,
                'status' => 'success',
                'componentes' => $componentes
            ];
        }else{
            $data =[
                'code' => 404,
                'status' => 'error',
                'message' => 'El componente no se ha localizado'
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
                'ref' => 'required',
            ]);
  

            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'La componente no se ha creado',
                    'errors' => $validate->errors()
                );            
            }else{

                $componente = new Components();
                $componente->nombre = $params_array['nombre'];
                $componente->ref = $params_array['ref'];
                $componente->save();
                
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'componente' => $componente
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos del componente'
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
                'ref' => 'required',
            ]);


            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El componente no se ha creado',
                    'errors' => $validate->errors()
                );            
            }else{


                $componente =  Components::firstOrNew (['id'=> $id]);
                unset($params_array['id']);

                $componente->nombre = $params_array['nombre'];
                $componente->ref = $params_array['ref'];
                $componente->save();
               
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'componente' => $componente
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos del componente'
            ];
        }
        return response()->json($data, $data['code']);



    }

    public function destroy($id, Request $request){
        $componente = Components::find($id);
        if(!empty($componente)){
            $componente->delete();
               
            $data =[
                'code' => 200,
                'status' => 'success',
                'componentes' => $componente
            ];
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'La componente no existe.'
            ];
        }
        return response()->json($data, $data['code']);
    }




}
