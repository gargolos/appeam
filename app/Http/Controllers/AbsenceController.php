<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use  Illuminate\Support\Facades\Validator;

use App\Absences;
use App\Reports;

class AbsenceController extends Controller
{
    public function index(Request $request){
        
        $ausencias = Absences::all(); 
     
      return response()->json([
        'code' => 200,
        'status' => 'success',
        'ausencias' => $ausencias
    ]);


        return response()->json([
            'code' => 200,
            'status' => 'success',
            'ausencias' => $ausencias
        ]);
        
    }

    public function show($id){
        $ausencias = Absences::find($id);
        if(is_object($ausencias)){
            $data =[
                'code' => 200,
                'status' => 'success',
                'ausencias' => $ausencias
            ];
        }else{
            $data =[
                'code' => 404,
                'status' => 'error',
                'message' => 'La ausencia no se ha localizado'
            ];
        }
        return response()->json($data, $data['code']);
    }


    public function store(Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'id_informe' => 'required',
                'id_participante' => 'required',
            ]);
          //  $ciudad = new Cities();                
           // $id_ciudad = $ciudad->ret_ID($params_array['ciudad']); //buscar el id

            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'La ausencia no se ha creado',
                    'errors' => $validate->errors()
                );            
            }else{

                $ausencia = new Absences();
                $ausencia->id_informe = $params_array['id_informe'];
                $ausencia->id_participante = $params_array['id_participante'];
                $ausencia->save();
                
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'ausencia' => $ausencia
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos de la ausencia'
            ];
        }
        return response()->json($data, $data['code']);

    }

    public function update($id, Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'id_informe' => 'required',
                'id_participante' => 'required',

            ]);



            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'La ausencia no se ha creado',
                    'errors' => $validate->errors()
                );            
            }else{


                $ausencia =  Absences::firstOrNew (['id'=> $id]);
                unset($params_array['id']);
                $ausencia->id_informe = $params_array['id_informe'];
                $ausencia->id_participante = $params_array['id_participante'];

                $ausencia->save();
               
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'ausencia' => $ausencia
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos de la ausencia'
            ];
        }
        return response()->json($data, $data['code']);

    }

    public function destroy($id){
        $ausencia = Absences::find($id);
        if(!empty($ausencia)){
            $ausencia->delete();
               
            $data =[
                'code' => 200,
                'status' => 'success',
                'ausencias' => $ausencia
            ];
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'La ausencia no existe.'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function id_informe($id_turno, $sem){
        
          $id = DB::table('informes') 
         ->select(['id as id_inf'])
         ->where('semana', '=',  $sem)
         ->where('id_turno', '=',  $id_turno)
         ->get();

    
         if(!empty($id[0]->id_inf)){               
            $data =[
                'code' => 200,
                'status' => 'success',
                'id_informe' =>$id[0]->id_inf
            ];
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'El id no se encontro.'
            ];
        }
        return response()->json($data, $data['code']);
    }

    
}
