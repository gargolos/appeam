<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use  Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Unique;

use App\Participants;

use App\Locations;
use App\Schedules;
use App\Shifts;

use App\Cities;
use App\Circuits;

use App\Reports;
use Illuminate\Support\Facades\DB;


class ReportController extends Controller
{
    public function __construct(){
        //$this->middleware('api.auth', ['except' =>['index', 'show']]);
    }

    public function index(Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if (isset($params_array['id_ciudad'])){ 
            $informes = DB::table('informes') 
              ->leftjoin('turnos', 'informes.id_turno', '=' , 'turnos.id')
              ->select(['informes.*'])
              ->where('turnos.id_ciudad', '=',  $params_array['id_ciudad'])
              ->get();
            }else{
              $informes = DB::table('informes') 
              ->select(['*'])
              ->get();   
            }
        
       // $informes = Reports::all();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'informes' => $informes
        ]);
        
    }

    public function show($id){
        
        $informe = Reports::find($id);
        if(is_object($informe)){
            $data =[
                'code' => 200,
                'status' => 'success',
                'informe' => $informe
            ];
        }else{
            $data =[
                'code' => 404,
                'status' => 'error',
                'message' => 'El informe no se ha localizado'
            ];
        }
        return response()->json($data, $data['code']);
    }


    public function store(Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        $id_turno =  $params_array['id_turno'];

        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'fecha'	    => 'date',
                //'semana'    => 'required|numeric|unique:informes,semana, id,0,id_turno,' . $id_turno,
                'semana'    => 'required|numeric',	
                'id_turno'	=> 'required|numeric',
                'id_user'	=> 'required|numeric',
                'actividad'	=> 'boolean',
                'libros'	=> 'numeric',
                'revistas'	=> 'numeric',
                'folletos'	=> 'numeric',
                'videos'	=> 'numeric',
                'revisitas'	=> 'numeric',
                'cursos'	=> 'numeric',
                'tratados'	=> 'numeric',
                'tarjetas'	=> 'numeric',
                'biblias'	=> 'numeric'
              //  'observaciones' => 'alpha_num'
            ]);
  
            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El informe no se ha creado store',
                    'errors' => $validate->errors()
                );
            }else{

                $informe = new Reports();

                $informe->fecha = $params_array['fecha'];
                $informe->id_turno = $params_array['id_turno'];
                $informe->id_user = $params_array['id_user'];
                $informe->semana = $params_array['semana'];
                $informe->actividad = $params_array['actividad'];
                $informe->libros = $params_array['libros'];
                $informe->revistas = $params_array['revistas'];
                $informe->folletos = $params_array['folletos'];
                $informe->videos = $params_array['videos'];
                $informe->revisitas = $params_array['revisitas'];
                $informe->cursos = $params_array['cursos'];
                $informe->tratados = $params_array['tratados'];
                $informe->tarjetas = $params_array['tarjetas'];
                $informe->biblias = $params_array['biblias'];                
                $informe->observaciones = $params_array['observaciones'];    

                $informe->save();
                
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'informe' => $informe
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos del informe'
            ];
        }
        return response()->json($data, $data['code']);

    }

    public function update($id, Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

       // $id_turno =  $params_array['id_turno'];

        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'fecha'	    => 'date',
               // 'semana'    => 'required|numeric|unique:informes,semana,' . $id . ',id,id_turno,' . $id_turno,
                'semana'    => 'required|numeric',
                'id_user'	=> 'required|numeric',
                'actividad'	=> 'boolean',
                'libros'	=> 'numeric',
                'revistas'	=> 'numeric',
                'folletos'	=> 'numeric',
                'videos'	=> 'numeric',
                'revisitas'	=> 'numeric',
                'cursos'	=> 'numeric',
                'tratados'	=> 'numeric',
                'tarjetas'	=> 'numeric',
                'biblias'	=> 'numeric'
              //  'observaciones' => 'alpha_num'
            ]);


            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El informe no se ha actualizado',
                    'errors' => $validate->errors()
                );            
            }else{


                $informe =  Reports::firstOrNew (['id'=> $id]);
                unset($params_array['id']);

                $informe->fecha = $params_array['fecha'];
                $informe->id_turno = $params_array['id_turno'];
                $informe->id_user = $params_array['id_user'];
                $informe->semana = $params_array['semana'];
                $informe->actividad = $params_array['actividad'];
                $informe->libros = $params_array['libros'];
                $informe->revistas = $params_array['revistas'];
                $informe->folletos = $params_array['folletos'];
                $informe->videos = $params_array['videos'];
                $informe->revisitas = $params_array['revisitas'];
                $informe->cursos = $params_array['cursos'];
                $informe->tratados = $params_array['tratados'];
                $informe->tarjetas = $params_array['tarjetas'];
                $informe->biblias = $params_array['biblias']; 
                $informe->observaciones = $params_array['observaciones'];    
                $informe->save();
               
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'informe' => $informe
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos del informe'
            ];
        }
        return response()->json($data, $data['code']);



    }

    public function destroy($id, Request $request){
        $informe = Reports::find($id);
        if(!empty($informe)){
            $informe->delete();
               
            $data =[
                'code' => 200,
                'status' => 'success',
                'informe' => $informe
            ];
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'El informe no existe.'
            ];
        }
        return response()->json($data, $data['code']);
    }

   
}
