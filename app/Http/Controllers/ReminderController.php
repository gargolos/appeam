<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use  Illuminate\Support\Facades\Validator;

use App\Reminders;

class ReminderController extends Controller
{
    public function index(Request $request){

       // $recordatorios = Reminders::all();
       $json = $request->input('json', null);
       $params_array = json_decode($json, true);
       date_default_timezone_set("America/Mexico_City");
       
       $fecha = date('Y-m-d H:i:s');
        //echo $fecha;
        
        $recordatorios_rol = DB::table('recordatorios') 
        ->join('destinatarios','destinatarios.id_recordatorio', '=', 'recordatorios.id' )
        ->join('users', 'users.id', '=', 'recordatorios.id_usuario_remitente')
        ->select(['recordatorios.id as id', 'titulo', 'mensaje', 'fecha_inicio','fecha_fin','prioridad','recordatorios.id_ciudad', 'users.user as remitente', 'destinatarios.id_destinatario', 'destinatarios.tipo_destinatario' ])
        ->where('recordatorios.id_ciudad', '=',  $params_array['id_ciudad'])
        ->where('destinatarios.id_destinatario', '<=',  $params_array['id_rol'])
        ->where( 'recordatorios.fecha_inicio', '<=' , $fecha)
        ->where( 'recordatorios.fecha_fin', '>=' , $fecha)
        ->where('destinatarios.tipo_destinatario', '=',  '1'); //tipo 1 Rol

       $recordatorios = DB::table('recordatorios') 
        ->join('destinatarios','recordatorios.id','=', 'destinatarios.id_recordatorio')
        ->join('users', 'users.id', '=', 'recordatorios.id_usuario_remitente')
        ->select(['recordatorios.id as id', 'titulo', 'mensaje', 'fecha_inicio','fecha_fin','prioridad','recordatorios.id_ciudad', 'users.user  as remitente', 'destinatarios.id_destinatario', 'destinatarios.tipo_destinatario' ])
        ->where('recordatorios.id_ciudad', '=',  $params_array['id_ciudad'])
        ->where('destinatarios.id_destinatario', '=',  $params_array['id_usuario'])
        ->where( 'recordatorios.fecha_inicio', '<=' , $fecha)
        ->where( 'recordatorios.fecha_fin', '>=' , $fecha)
        ->where('destinatarios.tipo_destinatario', '=',  '2') //tipo 2 usuarios
        ->union($recordatorios_rol)
        ->orderBy('prioridad', 'asc')
        ->get();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'recordatorios' => $recordatorios
        ]);
        
    }
  
    
    public function show($id)
    {
        $recordatorios = DB::table('recordatorios') 
        ->join('destinatarios','recordatorios.id','=', 'destinatarios.id_recordatorio')
        ->join('users', 'users.id', '=', 'recordatorios.id_usuario_remitente')
        ->select(['recordatorios.id as id', 'titulo', 'mensaje', 'fecha_inicio','fecha_fin','prioridad','recordatorios.id_ciudad', 'users.user  as remitente', 'destinatarios.id_destinatario', 'destinatarios.tipo_destinatario' ])
        ->where('recordatorios.id', '=',  $id)
        ->get();

        if(is_object($recordatorios)){
            $data =[
                'code' => 200,
                'status' => 'success',
                'recordatorios' => $recordatorios
            ];
        }else{
            $data =[
                'code' => 404,
                'status' => 'error',
                'message' => 'El recordatorios no se ha localizado'
            ];
        }
        return response()->json($data, $data['code']);
    }
 

    public function store(Request $request)
    {
        
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'id_ciudad' => 'required|numeric',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date',
                'id_usuario_remitente' => 'required|numeric',                
            ]);
  

            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El recordatorio no se ha creado',
                    'errors' => $validate->errors()
                );            
            }else{

                $recordatorios = new Reminders();
                $recordatorios->id_ciudad = $params_array['id_ciudad'];
                $recordatorios->titulo = $params_array['titulo'];
                $recordatorios->mensaje = $params_array['mensaje'];
                $recordatorios->fecha_inicio = $params_array['fecha_inicio'];
                $recordatorios->fecha_fin = $params_array['fecha_fin'];
                $recordatorios->prioridad = $params_array['prioridad'];
                $recordatorios->id_usuario_remitente = $params_array['id_usuario_remitente'];

                $recordatorios->save();
                
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'recordatorios' => $recordatorios
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos del recordatorio'
            ];
        }
        return response()->json($data, $data['code']);

    }

    public function update(Request $request, $id)
    {
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'id_ciudad' => 'required|numeric',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date',
                'id_usuario_remitente' => 'required|numeric',                
            ]);
  

            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El recordatorio no se ha creado',
                    'errors' => $validate->errors()
                );            
            }else{


                $recordatorios =  Reminders::firstOrNew (['id'=> $id]);
                unset($params_array['id']);

                $recordatorios->id_ciudad = $params_array['id_ciudad'];
                $recordatorios->titulo = $params_array['titulo'];
                $recordatorios->mensaje = $params_array['mensaje'];
                $recordatorios->fecha_inicio = $params_array['fecha_inicio'];
                $recordatorios->fecha_fin = $params_array['fecha_fin'];
                $recordatorios->prioridad = $params_array['prioridad'];
                $recordatorios->id_usuario_remitente = $params_array['id_usuario_remitente'];

                $recordatorios->save();
                
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'recordatorios' => $recordatorios
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos del recordatorio'
            ];
        }
        return response()->json($data, $data['code']);
    }


    public function destroy($id)
    {
        $recordatorios = Reminders::find($id);
        if(!empty($recordatorios)){
            DB::table('destinatarios')->where('id_recordatorio', $id)->delete();

            $recordatorios->delete();
               
            $data =[
                'code' => 200,
                'status' => 'success',
                'participant' => $recordatorios
            ];
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'El recordatorios no existe.'
            ];
        }
        return response()->json($data, $data['code']);
    }
}
