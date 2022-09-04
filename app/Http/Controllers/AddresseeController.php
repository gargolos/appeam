<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use  Illuminate\Support\Facades\Validator;

use App\Addressees;

class AddresseeController extends Controller
{

    public function index()
    {
        $destinatarios = Addressees::all();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'ciudades' => $destinatarios
        ]);
        
    }

    public function show($id)
    {
        $destinatarios = DB::table('destinatarios')->where('id_recordatorio', $id)->get();
        if(is_object($destinatarios)){
            $data =[
                'code' => 200,
                'status' => 'success',
                'destinatarios' => $destinatarios
            ];
        }else{
            $data =[
                'code' => 404,
                'status' => 'error',
                'message' => 'El destinatario no se ha localizado'
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

                $destinatarios = new Reminders();
                $destinatarios->id_ciudad = $params_array['id_ciudad'];
                $destinatarios->titulo = $params_array['titulo'];
                $destinatarios->mensaje = $params_array['mensaje'];
                $destinatarios->fecha_inicio = $params_array['fecha_inicio'];
                $destinatarios->fecha_fin = $params_array['fecha_fin'];
                $destinatarios->prioridad = $params_array['prioridad'];
                $destinatarios->id_usuario_remitente = $params_array['id_usuario_remitente'];

               // $destinatarios->save();
                
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'destinatarios' => $destinatarios
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos del destinatario'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
