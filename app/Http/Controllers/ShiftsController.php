<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use  Illuminate\Support\Facades\Validator;

use App\Cities;
use App\Shifts;
use App\Reports;

class ShiftsController extends Controller
{
    public function __construct(){
        //$this->middleware('api.auth', ['except' =>['index', 'show']]);
    }

    public function index(Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        //entrega todo sin que revise a que ciudad pertence
        //$turnos = Shifts::all();

        if (isset($params_array['id_ubicacion'])){  
        //turnos en base a la ubicacion
        $turnos = DB::table('turnos') 
        ->join('ciudades','turnos.id_ciudad','=', 'ciudades.id')
        ->join('ubicaciones','turnos.id_ubicacion','=', 'ubicaciones.id')
        ->join('horarios','turnos.id_horario','=', 'horarios.id')
        ->select(['turnos.id',  'dia', 'capacidad','horarios.hora_inicio','horarios.hora_fin','ubicaciones.id as id_ubicacion', 'ubicaciones.nombre as ubicacion','turnos.id_ciudad',  'ciudades.nombre as ciudad' ])
        ->where('turnos.id_ciudad', '=',  $params_array['id_ciudad'])
        ->where('turnos.id_ubicacion', '=',  $params_array['id_ubicacion'])
        ->get();
        
        }else{
            //turnos en base a la ubicacion
            $turnos = DB::table('turnos') 
            ->join('ciudades','turnos.id_ciudad','=', 'ciudades.id')
            ->join('ubicaciones','turnos.id_ubicacion','=', 'ubicaciones.id')
            ->join('horarios','turnos.id_horario','=', 'horarios.id')
            ->select(['turnos.id',  'dia', 'capacidad','horarios.hora_inicio','horarios.hora_fin','ubicaciones.id as id_ubicacion', 'ubicaciones.nombre as ubicacion','turnos.id_ciudad',  'ciudades.nombre as ciudad' ])
            ->where('turnos.id_ciudad', '=',  $params_array['id_ciudad'])
            ->get();
        }
        

    

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'turnos' => $turnos
        ]);
        
    }

    public function show($id){
        $turno = Shifts::find($id);
        if(is_object($turno)){
            $data =[
                'code' => 200,
                'status' => 'success',
                'turno' => $turno
            ];
        }else{
            $data =[
                'code' => 404,
                'status' => 'error',
                'message' => 'El turno no se ha localizado'
            ];
        }
        return response()->json($data, $data['code']);
    }


    public function store(Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'id_horario' => 'required',
                'id_ubicacion' => 'required',
                'id_ciudad' => 'required',
                'dia' => 'required|numeric',
                'capacidad' => 'required|numeric',
            ]);
  

            //$ciudad = new Cities();                
            //$id_ciudad = $ciudad->ret_ID($params_array['ciudad']); //buscar el id

            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El turno no se ha creado',
                    'errors' => $validate->errors()
                );
            }else{

                $turno = new Shifts();
                $turno->id_ciudad = $params_array['id_ciudad']; 
                $turno->id_horario = $params_array['id_horario'];
                $turno->id_ubicacion = $params_array['id_ubicacion'];
                $turno->dia = $params_array['dia'];
                $turno->capacidad = $params_array['capacidad'];
                $turno->save();
                
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'turno' => $turno
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos del turno'
            ];
        }
        return response()->json($data, $data['code']);

    }

    public function update($id, Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'id_horario' => 'required',
                'id_ubicacion' => 'required',
                'dia' => 'required|numeric',
                'capacidad' => 'required|numeric',
            ]);


            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El turno no se ha creado',
                    'errors' => $validate->errors()
                );            
            }else{


                $turno =  Shifts::firstOrNew (['id'=> $id]);
                unset($params_array['id']);

                $turno->id_horario = $params_array['id_horario'];
                $turno->id_ubicacion = $params_array['id_ubicacion'];
                $turno->dia = $params_array['dia'];
                $turno->capacidad = $params_array['capacidad'];


                $turno->save();
               
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'turno' => $turno
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos del turno'
            ];
        }
        return response()->json($data, $data['code']);



    }

    public function destroy($id){
        $informe = Reports::where('id_turno', '=', $id)->get();

        if(!empty($informe)){
            $data =[
                'code' => 400,
                'status' => 'error',
                'informes' => empty($informe),
                'mensaje' => 'No se puede eliminar el turno por que ya tiene informes asignados'
            ];
        }else{
            $turno = Shifts::find($id);
            if(is_object($turno)){
                $turno->delete();
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'turno' => $turno
                ];
            }else{
                $data =[
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'El turno no se ha localizado'
                ];
            }
        }
        return response()->json($data, $data['code']);
    }

    public function getShiftsOfCity($idciudad, Request $request){
        $turnos = Shifts::where('id_ciudad', '=', $idciudad)->get();
        if(!empty($turnos)){        
               $data =[
                   'code' => 200,
                   'status' => 'success',
                   'turnos' => $turnos
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