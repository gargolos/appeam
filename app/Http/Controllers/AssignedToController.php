<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use  Illuminate\Support\Facades\Validator;

use App\Assigned;

class AssignedToController extends Controller
{
   
    public function index(Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
    
      //$assignado = Assigned::all();

      if (isset($params_array['id_ubicacion'])){  

      $assignado = DB::table('asignadoa') 
        ->join('turnos','asignadoa.id_turno','=', 'turnos.id')
        ->join('ubicaciones','turnos.id_ubicacion','=', 'ubicaciones.id')
        ->join('horarios','turnos.id_horario','=', 'horarios.id')
        ->join('participantes','asignadoa.id_participante','=', 'participantes.id')
        ->select(['asignadoa.id as id', 'asignadoa.id_turno' ,'horarios.hora_inicio','horarios.hora_fin', 'ubicaciones.nombre as ubicacion', 'asignadoa.id_participante', 'participantes.n', 'participantes.ap','participantes.am','participantes.ac','participantes.id_circuito'])
        ->where('ubicaciones.id_ciudad', '=',  $params_array['id_ciudad'])
        ->where('ubicaciones.id', '=',  $params_array['id_ubicacion'])
        ->get();
      }else{
        $assignado = DB::table('asignadoa') 
        ->join('turnos','asignadoa.id_turno','=', 'turnos.id')
        ->join('ubicaciones','turnos.id_ubicacion','=', 'ubicaciones.id')
        ->join('horarios','turnos.id_horario','=', 'horarios.id')
        ->join('participantes','asignadoa.id_participante','=', 'participantes.id')
        ->select(['asignadoa.id as id', 'asignadoa.id_turno' ,'horarios.hora_inicio','horarios.hora_fin', 'ubicaciones.nombre as ubicacion', 'asignadoa.id_participante', 'participantes.n', 'participantes.ap','participantes.am','participantes.ac','participantes.id_circuito'])
        ->where('ubicaciones.id_ciudad', '=',  $params_array['id_ciudad'])
        ->get();

      }

      return response()->json([
          'code' => 200,
          'status' => 'success',
          'assignado' => $assignado
      ]);
      
    }

    public function show($id){
        $assignado = Assigned::find($id);
        if(is_object($assignado)){
            $data =[
                'code' => 200,
                'status' => 'success',
                'assignado' => $assignado
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
                'id_turno' => 'numeric',
                'id_participante' => 'numeric',
            ]);
  

            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'La asignacion no se ha creado',
                    'errors' => $validate->errors()
                );            
            }else{

                $assignado = new Assigned();
                $assignado->id_turno = $params_array['id_turno'];
                $assignado->id_participante = $params_array['id_participante'];
                $assignado->save();
                
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'assignado' => $assignado
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos de la asignacion'
            ];
        }
        return response()->json($data, $data['code']);

    }

    public function update($id, Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'id_turno' => 'numeric',
                'id_participante' => 'numeric',
            ]);


            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'La asignacion no se ha creado',
                    'errors' => $validate->errors()
                );            
            }else{


                $assignado =  Assigned::firstOrNew (['id'=> $id]);
                unset($params_array['id']);

                $assignado->id_turno = $params_array['id_turno'];
                $assignado->id_participante = $params_array['id_participante'];
                $assignado->save();

               
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'assignado' => $assignado
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos de la asignacion'
            ];
        }
        return response()->json($data, $data['code']);



    }

    public function destroy($id, Request $request){
        $assignado = Assigned::find($id);
        if(!empty($assignado)){
            $assignado->delete();
               
            $data =[
                'code' => 200,
                'status' => 'success',
                'assignado' => $assignado
            ];
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'La asignacion no existe.'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function shift_index( $id_turno){

      $assignados = DB::table('asignadoa') 
        ->join('participantes','asignadoa.id_participante','=', 'participantes.id')
        ->select(['asignadoa.id as id', 'asignadoa.id_turno' , 'asignadoa.id_participante', 'participantes.n', 'participantes.ap','participantes.am','participantes.ac','participantes.id_circuito'])
        ->where('asignadoa.id_turno', '=',  $id_turno)
        ->get();
   

      if(is_object($assignados)){
        $data =[
            'code' => 200,
            'status' => 'success',
            'asignados' => $assignados
        ];
    }else{
        $data =[
            'code' => 404,
            'status' => 'error',
            'message' => 'No se encontraron participantes para el turno.'
        ];
    }
    return response()->json($data, $data['code']);

    }

}
