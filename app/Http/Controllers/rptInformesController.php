<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
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
class rptInformesController extends Controller
{
    public function index(){
        //entrega todo sin que revise a que ciudad pertence
        $informes = Reports::all();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'informes' => $informes
        ]);
        
    }

    public function rptInformes(Request $request){
//json {"id_turno":"1", "semana":"mmYY","mes":"mm","año":"YY"}
        $json = $request->input('json', null);
 //       var_dump($json);
 //       die();
        $params_array = json_decode($json, true);
        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'id_turno'	=> 'required|string',
                'semana'    => 'numeric',	
                'mes'	=> 'numeric',
                'año'	=> 'numeric',                      
            ]);
        
        
            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Los datos enviados contienen errores',
                    'errors' => $validate->errors()
                );
            }else{

                if(empty($params_array['semana'])){
                    if(empty($params_array['mes'])){
                        //x año
                        $informe = Reports::select(['fecha','semana', 'id_turno',  'libros', 'revistas','folletos','videos','revisitas','cursos','tratados','tarjetas','observaciones', ])
                        ->where('semana','like',$params_array['año'] . '__')
                        ->where('id_turno','=',$params_array['id_turno'])
                        ->get();
                    }elseif(empty($params_array['año'])){
                        //sin variables
                        $informe=NULL; 
                    }else{
                        //x mes
                        $informe = Reports::select(['fecha','semana', 'id_turno',  'libros', 'revistas','folletos','videos','revisitas','cursos','tratados','tarjetas','observaciones', ])
                        ->where('semana','like',$params_array['año'] . $params_array['mes'])
                        ->where('id_turno','=',$params_array['id_turno'])
                        ->get();
                    }
    
                }else{
                    //x semana
                    $informe = Reports::select(['fecha','semana', 'id_turno',  'libros', 'revistas','folletos','videos','revisitas','cursos','tratados','tarjetas','observaciones', ])
                    ->where('semana','=',$params_array['semana'])
                    ->where('id_turno','=',$params_array['id_turno'])
                    ->get();     
                }
                                
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'participant' => $informe
                ];

            }
  
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos para el informe'
            ];
        }
        return response()->json($data, $data['code']);
    
    }

    public function rptInforme(Request $request){
        //json {"id_turno":"1", "fecha":"yyyy-mm-dd"}
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'id_turno'	=> 'required|string',
                'fecha'    => 'date',	
            ]);
        
        
            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Los datos enviados contienen errores',
                    'errors' => $validate->errors()
                );
            }else{

                $informe = Reports::select(['fecha','semana', 'id_turno',  'libros', 'revistas','folletos','videos','revisitas','cursos','tratados','tarjetas','observaciones', ])
                ->where('fecha','=',$params_array['fecha'])
                ->where('id_turno','=',$params_array['id_turno'])
                ->where('actividad','=','1')
                ->get();     
                
                                
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'participant' => $informe
                ];

            }
    
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos para el informe'
            ];
        }
        return response()->json($data, $data['code']);
    
    }

    public function rptReportLocation(Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'id_location'  =>  'required|string',
                'semana'    => 'numeric',	
                'mes'	=> 'numeric',
                'año'	=> 'numeric',                      
            ]);
        
        
            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Los datos enviados contienen errores',
                    'errors' => $validate->errors()
                );
            }else{

                if(empty($params_array['semana'])){
                    if(empty($params_array['mes'])){
                        //x año
                        $informe = Reports::select(['fecha','semana', 'id_turno',  'libros', 'revistas','folletos','videos','revisitas','cursos','tratados','tarjetas','observaciones', ])
                        ->where('semana','like',$params_array['año'] . '__')
                        ->where('id_turno','=',$params_array['id_turno'])
                        ->get();
                    }elseif(empty($params_array['año'])){
                        //sin variables
                        $informe=NULL; 
                    }else{
                        //x mes
                        $informe = Reports::select(['fecha','semana', 'id_turno',  'libros', 'revistas','folletos','videos','revisitas','cursos','tratados','tarjetas','observaciones', ])
                        ->where('semana','like',$params_array['año'] . $params_array['mes'])
                        ->where('id_turno','=',$params_array['id_turno'])
                        ->get();
                    }
    
                }else{
                    //x semana

/*

SELECT `ubicaciones`.*, `informes`.*
FROM `ubicaciones` 
	LEFT JOIN `turnos` ON `turnos`.`id_ubicacion` = `ubicaciones`.`id` 
	LEFT JOIN `informes` ON `informes`.`id_turno` = `turnos`.`id`
WHERE `ubicaciones`.`nombre`='Demo' and `ubicaciones`.`id_ciudad`=1
                    ->select(['fecha','semana', 'id_turno',  'libros', 'revistas','folletos','videos','revisitas','cursos','tratados','tarjetas','observaciones', ])
                    ->lefttJoin('informes', 'informes.id_turno','=','turnos.id')                            
                    ->where('ubicaciones.nombre','=','Demo')


*/

                    $informe = DB::table('informes')
                    ->join('turnos', 'turnos.id_ubicacion', '=', 'informes.id_turno')
                    ->join('ubicaciones', 'ubicaciones.id','=','turnos.id_ubicacion')
                    ->select(['fecha','semana', 'id_turno',  'libros', 'revistas','folletos','videos','revisitas','cursos','tratados','tarjetas','observaciones', ])
                    ->where('actividad','=','1')
                    ->where('ubicaciones.id','=','1')
                    ->where('semana','=',$params_array['semana'])
                    ->get();  
 
                }
                                
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'participant' => $informe
                ];

            }
  
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos para el informe'
            ];
        }
        return response()->json($data, $data['code']);
    
    }

}



        /*       
        
SELECT `ubicaciones`.*, `informes`.*
FROM `ubicaciones` 
	LEFT JOIN `turnos` ON `turnos`.`id_ubicacion` = `ubicaciones`.`id` 
	LEFT JOIN `informes` ON `informes`.`id_turno` = `turnos`.`id`
WHERE `ubicaciones`.`nombre`='Demo' and `ubicaciones`.`id_ciudad`=1

           $informe = DB::table('informes')
           
            ->select(['fecha','semana', 'id_turno',  'libros', 'revistas','folletos','videos','revisitas','cursos','tratados','tarjetas','observaciones', ])
            ->where('semana','=',$params_array['semana'])
            ->where('id_turno','=',$params_array['id_turno'])
            ->get();  

            $informe = DB::table('informes')
            ->select(['fecha','semana', 'id_turno',  'libros', 'revistas','folletos','videos','revisitas','cursos','tratados','tarjetas','observaciones', ])
            ->where('semana','=',$params_array['semana'])
            ->where('id_turno','=',$params_array['id_turno'])
            ->get();     

            $informe = Reports::select(['fecha','semana', 'id_turno',  DB::raw('libros+revistas+folletos+tratados+tarjetas as publicaciones'),'libros', 'revistas','folletos','videos','revisitas','cursos','tratados','tarjetas','observaciones', ])
            ->where('actividad','=','1')
            ->where('semana','=',$params_array['semana'])
            ->where('id_turno','=',$params_array['id_turno'])
            ->get();    

        */ 