<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
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
        //$accesos = Access::all();

        $accesos = DB::table('accesos') 
        ->join('roles','accesos.ref_rol','=', 'roles.ref')
        ->join('componentes','accesos.ref_componente','=', 'componentes.ref') 
        ->select(['roles.id as id','ref_rol', 'roles.nombre as rol', 'ref_componente','componentes.nombre as componente', 'status' ])
        ->get();

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
                'ref_roles' => 'numeric',
                'ref_componentes' => 'numeric',
                'status' => 'numeric',
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
                $accesos->ref_rol = $params_array['ref_rol'];
                $accesos->ref_componente = $params_array['ref_componente'];
                $accesos->status = $params_array['status'];
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
                'ref_roles' => 'numeric',
                'ref_componentes' => 'numeric',
                'status' => 'numeric',
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

                $accesos->ref_rol = $params_array['ref_rol'];
                $accesos->ref_componente = $params_array['ref_componente'];
                $accesos->status = $params_array['status'];

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
