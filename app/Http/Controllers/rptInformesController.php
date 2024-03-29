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

    public function rptReporte1(Request $request){
        // Participantes Totales
/*             
                        $sql=             
            'SELECT count(id) as total  FROM participantes
            UNION
            SELECT count(id)  FROM participantes WHERE sexo = "M" ';
            UNION
            SELECT "Mujeres", count(id) FROM `participantes` WHERE sexo = 'F'
            UNION
            SELECT "publicadores", count(id) FROM `participantes` WHERE asignacion = '0'
            UNION
            SELECT "precursores", count(id) FROM `participantes` WHERE asignacion = '1'
            UNION
            SELECT "especial", count(id) FROM `participantes` WHERE asignacion = '10'
            UNION
            SELECT "misionero", count(id) FROM `participantes` WHERE asignacion = '100'
            UNION
            SELECT "sirvo ministerial", count(id) FROM `participantes` WHERE asignacion = '1000'
            UNION
            SELECT "anciano", count(id) FROM `participantes` WHERE asignacion = '10000'
            UNION
            SELECT "sc", count(id) FROM `participantes` WHERE asignacion = '100000'';
                  $informe = DB::select($sql);
*/
    $json = $request->input('json', null);
    $params_array = json_decode($json, true);
    if(!empty($params_array)){
    $validate = Validator::make($params_array, [
        'id_ciudad' => 'required|numeric'
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
            $id_ciudad=$params_array['id_ciudad'];
            $informe['total'] = DB::table('participantes')->where('id_ciudad', '=', $id_ciudad)->count();
            $informe['thombres'] = DB::table('participantes')->where('sexo', '=', 'M')->where('id_ciudad', '=', $id_ciudad)->count();
            $informe['tmujeres'] = DB::table('participantes')->where('sexo', '=', 'F')->where('id_ciudad', '=', $id_ciudad)->count();
            $informe['tpublicador'] = DB::table('participantes')->where('asignacion', '=', '0')->where('id_ciudad', '=', $id_ciudad)->count();
            $informe['tprecursores'] = DB::table('participantes')->where('asignacion', '=', '1')->where('id_ciudad', '=', $id_ciudad)->count();
            $informe['tespecial'] = DB::table('participantes')->where('asignacion', '=', '10')->where('id_ciudad', '=', $id_ciudad)->count();
            $informe['tmisionero'] = DB::table('participantes')->where('asignacion', '=', '100')->where('id_ciudad', '=', $id_ciudad)->count();
            $informe['tsm'] = DB::table('participantes')->where('asignacion', '=', '1000')->where('id_ciudad', '=', $id_ciudad)->count();
            $informe['tanciano'] = DB::table('participantes')->where('asignacion', '=', '10000')->where('id_ciudad', '=', $id_ciudad)->count();
            $informe['tsc'] = DB::table('participantes')->where('asignacion', '=', '100000')->where('id_ciudad', '=', $id_ciudad)->count();
        
            json_encode($informe);
            if (empty($informe)){
                    $data =[
                        'code' => 400,
                        'status' => 'error',
                        'message' => 'El informe no tiene datos'
                    ];
            }else{
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'informe' => $informe
                ];
            }
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

   

    public function rptReporte2(Request $request){
        // Participantes 
        //param: 1-Asignados y 0-No Asignados

        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'asignados'	=> 'required|numeric',
                'id_ciudad' => 'required|numeric',
                'ubicacion' => 'string'
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


                 
                if($params_array['asignados']==1){
                    //asignados
                    //Select DISTINCT participantes.* from participantes RIGHT Join asignadoa ON participantes.id = asignadoa.id_participante
                    $informe = DB::table('participantes')
                    ->rightJoin('asignadoa', 'asignadoa.id_participante', '=', 'participantes.id')
                    ->join('circuitos', 'circuitos.id', '=', 'participantes.id_circuito')
                    ->select(['participantes.referencia', 'n', 'ap', 'am', 'ac', 'e', 't', 'c', 'congregacion', 'circuitos.nombre as circuito', 'fecha_registro',  'lun', 'mar', 'mie', 'jue', 'vie', 'sab', 'dom' ])                        
                    ->where('participantes.id_ciudad', '=',  $params_array['id_ciudad'])
                    ->distinct()
                    ->get();
                }elseif ($params_array['asignados']==2) {
                     //asignados por turno
                    if(!empty($params_array['ubicacion'])){
                        //con ubicacion
                        $location = new Locations();                
                        $id_ubicacion = $location->ret_ID($params_array['ubicacion'], $params_array['id_ciudad']); 
                        //var_dump($id_ubicacion); die();
                        $informe = DB::table('participantes')
                        ->rightJoin('asignadoa', 'asignadoa.id_participante', '=', 'participantes.id')
                        ->leftJoin('turnos','turnos.id','=', 'asignadoa.id_turno')
                        ->join('ubicaciones','ubicaciones.id','=','turnos.id_ubicacion')
                        ->join('horarios','horarios.id','=','turnos.id_horario')
                        ->join('circuitos', 'circuitos.id', '=', 'participantes.id_circuito')
                        ->select(['participantes.referencia', 'n', 'ap', 'am', 'ac', 'e', 't', 'c', 'congregacion', 'circuitos.nombre as circuito', 'fecha_registro', 'turnos.dia','horarios.hora_inicio', 'horarios.hora_fin'  ])                        
                        ->where('turnos.id_ubicacion','=',$id_ubicacion)
                        ->get();
                    }else{
                        //sin ubicacion
                     $informe = DB::table('participantes')
                     ->rightJoin('asignadoa', 'asignadoa.id_participante', '=', 'participantes.id')
                     ->join('turnos','turnos.id','=', 'asignadoa.id_turno')
                     ->join('ubicaciones','ubicaciones.id','=','turnos.id_ubicacion')
                     ->join('horarios','horarios.id','=','turnos.id_horario')
                     ->join('circuitos', 'circuitos.id', '=', 'participantes.id_circuito')
                     ->select(['participantes.referencia', 'n', 'ap', 'am', 'ac', 'e', 't', 'c', 'congregacion', 'circuitos.nombre as circuito', 'fecha_registro', 'turnos.dia','horarios.hora_inicio', 'horarios.hora_fin', 'ubicaciones.nombre as ubicacion'  ])                        
                     ->where('participantes.id_ciudad', '=',  $params_array['id_ciudad'])
                     ->get();
                    }

                }else{

                    //No asignados
                    //Select DISTINCT participantes.* from participantes left Join asignadoa ON participantes.id = asignadoa.id_participante where asignadoa.id_participante is NULL
                    $informe = DB::table('participantes')
                    ->leftJoin('asignadoa', 'asignadoa.id_participante', '=', 'participantes.id')
                    ->join('circuitos', 'circuitos.id', '=', 'participantes.id_circuito')
                    ->select(['participantes.referencia', 'n', 'ap', 'am', 'ac', 'e', 't', 'c', 'congregacion', 'circuitos.nombre as circuito', 'fecha_registro',  'lun', 'mar', 'mie', 'jue', 'vie', 'sab', 'dom' ])                        
                    ->whereRaw('asignadoa.id_participante is NULL')
                    ->where('participantes.id_ciudad', '=',  $params_array['id_ciudad'])
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

    public function rptReporte4(Request $request){
        // Ausencias //requiere o la semana o el mes y el año ademas de la ubicacion en nombre y el id_ciudad
    $json = $request->input('json', null);
    $params_array = json_decode($json, true);
    if(!empty($params_array)){
    $validate = Validator::make($params_array, [
        'ubicacion' => 'required|string',
        'año' => 'numeric',
        'mes' => 'numeric',
        'semana' => 'numeric',
        'id_ciudad' => 'required|numeric'
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
            $semana=$params_array['semana'];            
            $location = new Locations();                
            $id_ubicacion = $location->ret_ID($params_array['ubicacion'], $params_array['id_ciudad']); 

//            var_dump($ubicacion);
  //          var_dump($fecha2); die();
  /*
$query = DB::table('users')->select('name');
$users = $query->addSelect('age')->get();
*/
            $año=$params_array['año'];
            $mes=$params_array['mes'];
            if(empty($semana)){
                //Mes
                if(empty($mes)){
                    //año de servicio
                    $fecha1 = date(DATE_ATOM, mktime(0, 0, 0, 9, 1, $año)); //priemero del mes
                    $fecha2 = date(DATE_ATOM, mktime(0, 0, 0, 9, 0, $año+1)); //ultimo de mes    
                }else{
                    
                    $fecha1 = date(DATE_ATOM, mktime(0, 0, 0, $mes, 1, $año)); //priemero del mes
                    $fecha2 = date(DATE_ATOM, mktime(0, 0, 0, $mes+1, 0, $año)); //ultimo de mes    
                }
    
                $informe = DB::table('ausencias')
                ->join('informes', 'informes.id', '=' , 'ausencias.id_informe')
                ->join('participantes',  'participantes.id', '=', 'ausencias.id_participante')
                ->Rightjoin('circuitos', 'circuitos.id', '=', 'participantes.id_circuito')
                ->join('turnos', 'turnos.id', '=', 'informes.id_turno')
                ->join('ubicaciones','ubicaciones.id','=','turnos.id_ubicacion')
                ->join('horarios','horarios.id','=','turnos.id_horario')
                ->select(['participantes.id', 'n', 'ap', 'am', 'ac', 'e', 't', 'c', 'congregacion', 'circuitos.nombre as circuito', 'turnos.dia','horarios.hora_inicio', 'horarios.hora_fin', 'ubicaciones.nombre as ubicacion', DB::raw('count(*) as ausencias') ])                        
                ->where('turnos.id_ubicacion','=',$id_ubicacion)
                ->whereBetween('informes.fecha', [$fecha1, $fecha2])
                ->groupBy('participantes.id', 'n', 'ap', 'am', 'ac', 'e', 't', 'c', 'congregacion', 'circuitos.nombre', 'turnos.dia','horarios.hora_inicio', 'horarios.hora_fin', 'ubicaciones.nombre')
                ->get();


            }else{
                //semana
                $informe = DB::table('ausencias')
                ->join('informes', 'informes.id', '=' , 'ausencias.id_informe')
                ->join('participantes',  'participantes.id', '=', 'ausencias.id_participante')
                ->join('circuitos', 'circuitos.id', '=', 'participantes.id_circuito')
                ->join('turnos', 'turnos.id', '=', 'informes.id_turno')
                ->join('ubicaciones','ubicaciones.id','=','turnos.id_ubicacion')
                ->join('horarios','horarios.id','=','turnos.id_horario')
                ->select(['participantes.id', 'n', 'ap', 'am', 'ac', 'e', 't', 'c', 'congregacion', 'circuitos.nombre as circuito', 'turnos.dia','horarios.hora_inicio', 'horarios.hora_fin', 'ubicaciones.nombre as ubicacion', DB::raw('count(*) as ausencias') ])                        
                ->where('turnos.id_ubicacion','=',$id_ubicacion)
                ->where('informes.semana', '=', $semana)
                ->groupBy('participantes.id', 'n', 'ap', 'am', 'ac', 'e', 't', 'c', 'congregacion', 'circuitos.nombre', 'turnos.dia','horarios.hora_inicio', 'horarios.hora_fin', 'ubicaciones.nombre')
                ->get();

            }

            if (empty($informe)){
                    $data =[
                        'code' => 400,
                        'status' => 'error',
                        'message' => 'El informe no tiene datos'
                    ];
            }else{
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'informe' => $informe
                ];
            }
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

    public function rptReporte6(Request $request){
        // Informes //requiere o la semana o el mes y el año ademas de la ubicacion en nombre y el id_ciudad
    $json = $request->input('json', null);
    $params_array = json_decode($json, true);
    if(!empty($params_array)){
    $validate = Validator::make($params_array, [
        'ubicacion' => 'required|string',
        'year' => 'numeric',
        'mes' => 'numeric',
        'id_ciudad' => 'required|numeric'
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
           
            $location = new Locations(); 
       
            $id_ubicacion = $location->ret_ID($params_array['ubicacion'], $params_array['id_ciudad']); 
 
   //         var_dump($id_ubicacion);
  //          var_dump($fecha2); die();
  /*
$query = DB::table('users')->select('name');
$users = $query->addSelect('age')->get();
*/
            $año=$params_array['año'];
            $mes=$params_array['mes'];
        
                //Mes
                    $fecha1 = date(DATE_ATOM, mktime(0, 0, 0, $mes, 1, $año)); //priemero del mes
                    $fecha2 = date(DATE_ATOM, mktime(0, 0, 0, $mes+1, 0, $año)); //ultimo de mes    
              
                    //$fecha2 =  date("Y-m-t", strtotime($fecha1));

                    // var_dump($id_ubicacion);die();
               // $informe['total'] = DB::table('participantes')->where('id_ciudad', '=', $id_ciudad)->count();
               //var_dump($fecha1); var_dump($fecha2);die();
               
               $informe = DB::table('informes')
                ->join('turnos', 'turnos.id', '=', 'informes.id_turno')
                ->join('horarios','horarios.id','=','turnos.id_horario')
                ->select('informes.*', 'horarios.hora_inicio', 'horarios.hora_fin'  )
                ->whereBetween('informes.fecha', [$fecha1, $fecha2])
                ->where('turnos.id_ubicacion','=',$id_ubicacion)
                ->get();
                 //var_dump($id_ubicacion);die();

            /*SELECT `informes`.*, `horarios`.`hora_inicio`, `horarios`.`hora_fin`  FROM `informes` 
INNER JOIN `turnos` ON `informes`.`id_turno` = `turnos`.`id`
INNER JOIN `horarios` ON `turnos`.`id_horario` = `horarios`.`id`
WHERE fecha BETWEEN '2023-01-01' and '2023-01-31'
AND  `turnos`.`id_ubicacion` = 1 */

            if (empty($informe)){
                    $data =[
                        'code' => 400,
                        'status' => 'error',
                        'message' => 'El informe no tiene datos'
                    ];
            }else{
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'informe' => $informe
                ];
            }
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


    public function rptReporte7(Request $request){
        // Informes //requiere o la semana o el mes y el año ademas de la ubicacion en nombre y el id_ciudad
    $json = $request->input('json', null);
    $params_array = json_decode($json, true);
    if(!empty($params_array)){
    $validate = Validator::make($params_array, [
        'ubicacion' => 'required|string',
        'año' => 'numeric',
        'mes' => 'numeric',
        'semana' => 'numeric',
        'id_ciudad' => 'required|numeric'
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
            $semana=$params_array['semana'];            
            $location = new Locations();                
            $id_ubicacion = $location->ret_ID($params_array['ubicacion'], $params_array['id_ciudad']); 

//            var_dump($ubicacion);
  //          var_dump($fecha2); die();
  /*
$query = DB::table('users')->select('name');
$users = $query->addSelect('age')->get();
*/
            $año=$params_array['año'];
            $mes=$params_array['mes'];
            if(empty($semana)){
                //Mes
                if(empty($mes)){
                    //año de servicio
                    $fecha1 = date(DATE_ATOM, mktime(0, 0, 0, 9, 1, $año-1)); //priemero del mes
                    $fecha2 = date(DATE_ATOM, mktime(0, 0, 0, 9, 0, $año)); //ultimo de mes    
                }else{
                    
                    $fecha1 = date(DATE_ATOM, mktime(0, 0, 0, $mes, 1, $año)); //priemero del mes
                    $fecha2 = date(DATE_ATOM, mktime(0, 0, 0, $mes+1, 0, $año)); //ultimo de mes    
                }
               // $informe['total'] = DB::table('participantes')->where('id_ciudad', '=', $id_ciudad)->count();
               //var_dump($fecha1); var_dump($fecha2);die();
               $informe = DB::table('informes')
                ->Rightjoin('turnos', 'turnos.id', '=', 'informes.id_turno')
                ->join('ubicaciones','ubicaciones.id','=','turnos.id_ubicacion')
                ->select(DB::raw('sum(actividad) as actividad, sum(libros) as libros, sum(revistas) as revistas, sum(folletos) as folletos, sum(videos) as videos, sum(revisitas) as revisitas, sum(cursos) as cursos, sum(tratados) as tratados, sum(tarjetas) as tarjetas, sum(biblias) as biblias')  )
                ->where('turnos.id_ubicacion','=',$id_ubicacion)
                ->whereBetween('informes.fecha', [$fecha1, $fecha2])
                ->get();
                // var_dump($id_ubicacion);die();

            }else{
                //semana
                $informe = DB::table('informes')
                ->Rightjoin('turnos', 'turnos.id', '=', 'informes.id_turno')
                ->join('ubicaciones','ubicaciones.id','=','turnos.id_ubicacion')
                ->select(DB::raw('sum(actividad) as actividad, sum(libros) as libros, sum(revistas) as revistas, sum(folletos) as folletos, sum(videos) as videos, sum(revisitas) as revisitas, sum(cursos) as cursos, sum(tratados) as tratados, sum(tarjetas) as tarjetas, sum(biblias) as biblias')  )
                ->where('turnos.id_ubicacion','=',$id_ubicacion)
                ->where('informes.semana', '=', $semana)
                ->get();

            }

            if (empty($informe)){
                    $data =[
                        'code' => 400,
                        'status' => 'error',
                        'message' => 'El informe no tiene datos'
                    ];
            }else{
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'informe' => $informe
                ];
            }
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

    public function rptReporte9(Request $request){
        // reporteX
    $json = $request->input('json', null);
    $params_array = json_decode($json, true);
    if(!empty($params_array)){
    $validate = Validator::make($params_array, [
        'id_ciudad' => 'required|numeric'
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
            $id_ciudad=$params_array['id_ciudad'];
            $informe['total'] = DB::table('participantes')->where('id_ciudad', '=', $id_ciudad)->count();
            $informe['teorica'] = DB::table('capacitaciones')->join('eventos','eventos.id','=','capacitaciones.id_evento')
            ->where('id_ciudad', '=', $id_ciudad)->where('eventos.tipo', '=', 0)->count();
            $informe['practica'] = DB::table('capacitaciones')->join('eventos','eventos.id','=','capacitaciones.id_evento')
            ->where('id_ciudad', '=', $id_ciudad)->where('eventos.tipo', '=', 1)->count();
            json_encode($informe);
            if (empty($informe)){
                    $data =[
                        'code' => 400,
                        'status' => 'error',
                        'message' => 'El informe no tiene datos'
                    ];
            }else{
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'informe' => $informe
                ];
            }
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

    public function rptReporte10(Request $request){
        // reporteX
    $json = $request->input('json', null);
    $params_array = json_decode($json, true);
    if(!empty($params_array)){
    $validate = Validator::make($params_array, [
        'id_ciudad' => 'required|numeric',
        'tipo' => 'numeric',
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
            $id_ciudad=$params_array['id_ciudad'];
            $tipo=$params_array['tipo'];
            $informe = DB::table('participantes')
            ->join('circuitos', 'circuitos.id', '=', 'participantes.id_circuito')
            ->join('capacitaciones','capacitaciones.id_participante','=','participantes.id')
            ->join('eventos','eventos.id','=','capacitaciones.id_evento')
            ->select(['participantes.referencia', 'n', 'ap', 'am', 'ac', 'e', 't', 'c', 'congregacion', 'circuitos.nombre as circuito', 'fecha_registro', 'eventos.fecha as fecha_evento'  ])                        
            ->where('participantes.id_ciudad', '=', $id_ciudad)
            ->where('eventos.tipo', '=', $tipo)
            ->get();
             

            if (empty($informe)){
                    $data =[
                        'code' => 400,
                        'status' => 'error',
                        'message' => 'El informe no tiene datos'
                    ];
            }else{
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'informe' => $informe
                ];
            }
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

   
    public function rptReporte12(Request $request){
        //json {"id_turno":"1", "fecha":"yyyy-mm-dd"}
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'id_ciudad' => 'required|numeric',
                'ubicacion'	=> 'required|string',
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

                $location = new Locations();                
                $id_ubicacion = $location->ret_ID($params_array['ubicacion'], $params_array['id_ciudad']); 

                $informe = DB::table('participantes')
                ->leftjoin('asignadoa', 'asignadoa.id_participante',  '=', 'participantes.id')
                ->join('turnos', 'turnos.id', '=', 'asignadoa.id_turno')
                ->join('horarios', 'horarios.id', '=', 'turnos.id_horario')
                ->join('circuitos', 'circuitos.id', '=', 'participantes.id_circuito')
                ->select(['participantes.referencia', 'n', 'ap', 'am', 'ac', 'e', 't', 'c', 'congregacion', 'circuitos.nombre as circuito','turnos.dia', 'horarios.hora_inicio', 'horarios.hora_fin',  'asignadoa.capitan'  ])                        
                ->where('turnos.id_ubicacion', '=', $id_ubicacion)
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

    public function rptReporte13(Request $request){
        //json {"id_turno":"1", "fecha":"yyyy-mm-dd"}
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'id_ciudad' => 'required|numeric',
                'ubicacion'	=> 'required|string',
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

                $location = new Locations();                
                $id_ubicacion = $location->ret_ID($params_array['ubicacion'], $params_array['id_ciudad']); 

                $informe = DB::table('asignadoa')
                ->join('turnos', 'turnos.id', '=', 'asignadoa.id_turno')
                ->join('horarios', 'horarios.id', '=', 'turnos.id_horario')
                ->select(['turnos.dia', 'horarios.hora_inicio', 'horarios.hora_fin',  'turnos.capacidad', DB::raw('count(*) as participantes')  ])                        
                ->where('turnos.id_ubicacion', '=', $id_ubicacion)
                ->groupBy('turnos.dia', 'horarios.hora_inicio', 'horarios.hora_fin', 'turnos.capacidad')
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


    public function rptReporteX(Request $request){
        // reporteX
    $json = $request->input('json', null);
    $params_array = json_decode($json, true);
    if(!empty($params_array)){
    $validate = Validator::make($params_array, [
        'id_ciudad' => 'required|numeric'
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
            $id_ciudad=$params_array['id_ciudad'];
            $informe['total'] = DB::table('participantes')->where('id_ciudad', '=', $id_ciudad)->count();
             
            json_encode($informe);
            if (empty($informe)){
                    $data =[
                        'code' => 400,
                        'status' => 'error',
                        'message' => 'El informe no tiene datos'
                    ];
            }else{
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'informe' => $informe
                ];
            }
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


    public function rptSinFotos(Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'id_ciudad' => 'required|numeric'
            ]);
         //var_dump($json); 
         //die();
        if($validate->fails()){
            //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Los datos enviados contienen errores',
                    'errors' => $validate->errors()
                );
            }else{
                $id_ciudad=$params_array['id_ciudad'];
                $informe = DB::table('participantes')
                ->select(['referencia as REF' , 'n', 'ap', 'am', 'ac', 'e', 'foto1', 'foto2'])                        
                ->where('id_ciudad','=', $id_ciudad)
                ->where('foto1','=', "")
                ->orwhere('foto2','=', "")
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