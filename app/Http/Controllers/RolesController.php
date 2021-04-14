<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use  Illuminate\Support\Facades\Validator;

use App\Roles;

class RolesController extends Controller
{
    public function __construct(){
        //$this->middleware('api.auth', ['except' =>['index', 'show']]);
    }

    public function index(){
        $roles = Roles::all();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'roles' => $roles
        ]);
        
    }

    public function show($id){
        $roles = Roles::find($id);
        if(is_object($roles)){
            $data =[
                'code' => 200,
                'status' => 'success',
                'roles' => $roles
            ];
        }else{
            $data =[
                'code' => 404,
                'status' => 'error',
                'message' => 'El rol no se ha localizado'
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
                    'message' => 'La rol no se ha creado',
                    'errors' => $validate->errors()
                );            
            }else{

                $rol = new Roles();
                $rol->nombre = $params_array['nombre'];
                $rol->ref = $params_array['ref'];
                $rol->save();
                
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'rol' => $rol
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos del rol'
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
                    'message' => 'El rol no se ha creado',
                    'errors' => $validate->errors()
                );            
            }else{


                $rol =  Roles::firstOrNew (['id'=> $id]);
                unset($params_array['id']);

                $rol->nombre = $params_array['nombre'];
                $rol->ref = $params_array['ref'];
                $rol->save();
               
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'rol' => $rol
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos del rol'
            ];
        }
        return response()->json($data, $data['code']);



    }

    public function destroy($id, Request $request){
        $rol = Roles::find($id);
        if(!empty($rol)){
            $rol->delete();
               
            $data =[
                'code' => 200,
                'status' => 'success',
                'roles' => $rol
            ];
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'La rol no existe.'
            ];
        }
        return response()->json($data, $data['code']);
    }
}
