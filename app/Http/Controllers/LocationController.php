<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use  Illuminate\Support\Facades\Validator;

use App\Cities;
use App\Locations;

class LocationController extends Controller
{
    public function __construct(){
        //$this->middleware('api.auth', ['except' =>['index', 'show']]);
    }

    public function index(Request $request){
        //entrega todo sin que revise a que ciudad pertence
       // $ubicacions = Locations::all();
       $json = $request->input('json', null);
       $params_array = json_decode($json, true);

        $ubicacions = DB::table('ubicaciones') 
        ->join('ciudades','ubicaciones.id_ciudad','=', 'ciudades.id')
        ->select(['ubicaciones.id as id', 'ubicaciones.nombre as nombre', 'id_ciudad',  'ciudades.nombre as ciudad' ])
        ->where('ubicaciones.id_ciudad', '=',  $params_array['id_ciudad'])
        ->get();
        return response()->json([
            'code' => 200,
            'status' => 'success',
            'ubicacions' => $ubicacions
        ]);
        
    }

    public function show($id){
        $ubicacion = Locations::find($id);

        if(is_object($ubicacion)){
            $data =[
                'code' => 200,
                'status' => 'success',
                'ubicacion' => $ubicacion
            ];
        }else{
            $data =[
                'code' => 404,
                'status' => 'error',
                'message' => 'La ubicacion no se ha localizado'
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
                'id_ciudad' => 'required|numeric',
            ]);
  

           // $ciudad = new Cities();                
           // $id_ciudad = $ciudad->ret_ID($params_array['ciudad']); //buscar el id

            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'La ubicacion no se ha creado',
                    'errors' => $validate->errors()
                );
            }
/*            elseif( $id_ciudad==0 ) {
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'La ubicacion no se ha creado, la ciudad no existe en la base de datos.',
                );
            }*/
            else{

                $ubicacion = new Locations();
               
                $ubicacion->id_ciudad = $params_array['id_ciudad']; 
                $ubicacion->nombre = $params_array['nombre'];
                $ubicacion->save();
                
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'ubicacion' => $ubicacion
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos del ubicacion'
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
                'ciudad' => 'required|string',
            ]);


            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El ubicacion no se ha creado',
                    'errors' => $validate->errors()
                );            
            }else{


                $ubicacion =  Locations::firstOrNew (['id'=> $id]);
                unset($params_array['id']);    
                $ubicacion->nombre = $params_array['nombre'];

                $ubicacion->save();
               
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'ubicacion' => $ubicacion
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos de la ubicacion'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function destroy($id, Request $request){
        $ubicacion = Locations::find($id);
        if(!empty($ubicacion)){
            $ubicacion->delete();
               
            $data =[
                'code' => 200,
                'status' => 'success',
                'ubicacion' => $ubicacion
            ];
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'La ubicacion no existe.'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function getLocationsOfCity($idciudad, Request $request){
        $ubicacions = Locations::where('id_ciudad', '=', $idciudad)->get();
        if(!empty($ubicacions)){        
               $data =[
                   'code' => 200,
                   'status' => 'success',
                   'ubicacions' => $ubicacions
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
