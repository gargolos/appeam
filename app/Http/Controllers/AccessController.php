<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use  Illuminate\Support\Facades\Validator;

use App\Access;

class AccessController extends Controller
{
    public function __construct(){
        //$this->middleware('api.auth', ['except' =>['index', 'show']]);
    }

    public function index(){
        $accesos = Access::all();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'accesos' => $accesos
        ]);
        
    }

    public function show($id){
        $accesos = Access::find($id);
        if(is_object($accesos)){
            $data =[
                'code' => 200,
                'status' => 'success',
                'accesos' => $accesos
            ];
        }else{
            $data =[
                'code' => 404,
                'status' => 'error',
                'message' => 'El acceso no se ha localizado'
            ];
        }
        return response()->json($data, $data['code']);
    }


    public function store(Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'id_roles' => 'numeric',
                'id_componentes' => 'numeric',
                'visible' => 'numeric',
            ]);
  

            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'La acceso no se ha creado',
                    'errors' => $validate->errors()
                );            
            }else{

                $accesos = new Access();
                $accesos->nombre = $params_array['nombre'];

                $accesos->save();
                
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'accesos' => $accesos
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos del acceso'
            ];
        }
        return response()->json($data, $data['code']);

    }

    public function update($id, Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'id_roles' => 'numeric',
                'id_componentes' => 'numeric',
                'visible' => 'numeric',
            ]);


            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El acceso no se ha creado',
                    'errors' => $validate->errors()
                );            
            }else{


                $accesos =  Access::firstOrNew (['id'=> $id]);
                unset($params_array['id']);

                $accesos->nombre = $params_array['nombre'];

                $accesos->save();
               
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'accesos' => $accesos
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos del acceso'
            ];
        }
        return response()->json($data, $data['code']);



    }

    public function destroy($id, Request $request){
        $accesos = Access::find($id);
        if(!empty($accesos)){
            $accesos->delete();
               
            $data =[
                'code' => 200,
                'status' => 'success',
                'accesos' => $accesos
            ];
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'La acceso no existe.'
            ];
        }
        return response()->json($data, $data['code']);
    }
}
