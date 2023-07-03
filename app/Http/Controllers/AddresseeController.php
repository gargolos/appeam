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
            'status' => 'success11',
            'ciudades' => $destinatarios
        ]);
        
    }

    public function show($id_recordatorio) //es el id del recordatorio no el id del destinatario
    {
        $destinatarios = DB::table('destinatarios')->where('id_recordatorio', $id_recordatorio)->get();
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
       $cont = 0;
        foreach($params_array as $p_array){
           
           if(!empty($p_array)){
           
                $validate = Validator::make($p_array, [
                    'id_recordatorio' => 'required|numeric',
                    'id_destinatario' => 'required|numeric',
                    'tipo_destinatario' => 'required|numeric'               
                ]);

           // 
            
                if($validate->fails()){
                    //La validacion a fallado
                    $data = array(
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'El recordatorio no se ha creado',
                        'errors' => $validate->errors()
                    );            
                     
                }else{

                    $destinatario = new Addressees($p_array);
                   // echo $p_array['id_recordatorio'];
                    $destinatario->id_recordatorio = $p_array['id_recordatorio'];
                    $destinatario->id_destinatario = $p_array['id_destinatario'];
                    $destinatario->tipo_destinatario = $p_array['tipo_destinatario'];

                    //$destinatario->save();
                    var_dump($destinatario->id_destinatario);
                    $data =[
                        'code' => 200,
                        'status' => 'success',
                        'destinatarios' => $destinatario
                    ];
                   // unset($destinatario);
                }
              
            }else{
                $data =[
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No se han enviado los datos del destinatario'
                ];
            } 
          $datos[$cont] = $data;
          //  unset($data); unset($destinatario); 
            $cont++;
       }

       return response()->json($data, 200);
    }

    public function update(Request $request, $id) //el id no es necesario
    {
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        $cont = 0;
       
         foreach($params_array as $p_array){
        
            if(!empty($p_array)){
            
                 $validate = Validator::make($p_array, [
                     'id' => 'required|numeric',                    
                     'id_recordatorio' => 'required|numeric',
                     'id_destinatario' => 'required|numeric',
                     'tipo_destinatario' => 'required|numeric'               
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
 
                    $destinatario =  Addressees::firstOrNew (['id'=> $p_array['id']]);
                    //unset($p_array['id']);
                    
                     $destinatario->id_recordatorio = $p_array['id_recordatorio'];
                     $destinatario->id_destinatario = $p_array['id_destinatario'];
                     $destinatario->tipo_destinatario = $p_array['tipo_destinatario'];
 
                     $destinatario->save();
          
                     $data =[
                         'code' => 200,
                         'status' => 'success',
                        'destinatarios' => $destinatario
                     ];
                                  
                 }
           
             }else{
                 $data =[
                     'code' => 400,
                     'status' => 'error',
                     'message' => 'No se han enviado los datos del destinatario'
                 ];

             } 
             
             $datos[$cont] = $data;
             unset($data); unset($destinatario); 
             $cont++;
 
        }

        return response()->json($datos, 200);
     }


    public function destroy($id_recordatorio) //es el id del recordatorio no el id del destinatario
    {

        $destinatarios = DB::table('destinatarios')->where('id_recordatorio', $id)->get();
        if(!empty($destinatarios)){
            DB::table('destinatarios')->where('id_recordatorio', $id)->delete();

            $destinatarios->delete();
               
            $data =[
                'code' => 200,
                'status' => 'success',
                'participant' => $destinatarios
            ];
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'El destinatario no existe.'
            ];
        }
        return response()->json($data, $data['code']);
    }
}
