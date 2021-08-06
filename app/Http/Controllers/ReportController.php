<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use  Illuminate\Support\Facades\Validator;

use App\Participants;

use App\Locations;
use App\Schedules;
use App\Shifts;

use App\Cities;
use App\Circuits;

use App\Reports;



class ReportController extends Controller
{
    public function __construct(){
        //$this->middleware('api.auth', ['except' =>['index', 'show']]);
    }

    public function index(){
        //entrega todo sin que revise a que ciudad pertence
        $informes = Reports::all();

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
        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'fecha'	    => 'date',
                'semana'    => 'required|numeric',	
                'id_turno'	=> 'required|alpha_num',
                'actividad'	=> 'boolean',
                'libros'	=> 'numeric',
                'revistas'	=> 'numeric',
                'folletos'	=> 'numeric',
                'videos'	=> 'numeric',
                'revisitas'	=> 'numeric',
                'cursos'	=> 'numeric',
                'tratados'	=> 'numeric',
                'tarjetas'	=> 'numeric',
                'observaciones' => 'alpha_num'
            ]);
  
            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El informe no se ha creado',
                    'errors' => $validate->errors()
                );
            }else{

                $informe = new Reports();
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

        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'fecha'	    => 'date',
                'semana'    => 'required|numeric',	
                'id_turno'	=> 'required|alpha_num',
                'actividad'	=> 'boolean',
                'libros'	=> 'numeric',
                'revistas'	=> 'numeric',
                'folletos'	=> 'numeric',
                'videos'	=> 'numeric',
                'revisitas'	=> 'numeric',
                'cursos'	=> 'numeric',
                'tratados'	=> 'numeric',
                'tarjetas'	=> 'numeric',
                'observaciones' => 'alpha_num'
            ]);


            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El informe no se ha creado',
                    'errors' => $validate->errors()
                );            
            }else{


                $informe =  Reports::firstOrNew (['id'=> $id]);
                unset($params_array['id']);
     
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
