<?php



namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use  Illuminate\Support\Facades\Validator;



use App\Participants;
use App\Cities;
use App\Circuits;



class CircuitController extends Controller
{

    public function __construct(){
        //$this->middleware('api.auth', ['except' =>['index', 'show']]);
    }



    public function index(Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
    
      //$assignado = Assigned::all();
      if (isset($params_array['id_ciudad'])){ 
      $circuitos = DB::table('circuitos') 
        ->select(['*'])
        ->where('circuitos.id_ciudad', '=',  $params_array['id_ciudad'])
        ->get();
      }else{
        $circuitos = DB::table('circuitos') 
        ->select(['*'])
        ->get();   
      }
      return response()->json([
        'code' => 200,
        'status' => 'success',
        'circuitos' => $circuitos
    ]);
    }



    public function show($id){
        $circuito = Circuits::find($id);
        if(is_object($circuito)){
            $data =[
                'code' => 200,
                'status' => 'success',
                'circuito' => $circuito
            ];
        }else{
            $data =[
                'code' => 404,
                'status' => 'error',
                'message' => 'El circuito no se ha localizado'
            ];
        }
        return response()->json($data, $data['code']);
    }





    public function store(Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'nombre' => 'required|string',
            ]);

            if (isset($params_array['ciudad'])){ 
                $ciudad = new Cities();   
              $id_ciudad = $ciudad->ret_ID($params_array['ciudad']);
          }else {
                 $id_ciudad = $params_array['id_ciudad'];
          }


            if($validate->fails()){

                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El ciruito no se ha creado',
                    'errors' => $validate->errors()
                );

            }elseif( $id_ciudad==0 ) {
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El ciruito no se ha creado, la ciudad no existe en la base de datos.',
                );
            }else{
                $circuito = new Circuits();
                $circuito->id_ciudad = $id_ciudad ; //buscar el id
                $circuito->nombre = $params_array['nombre'];
                $circuito->observaciones = $params_array['observaciones'];
                $circuito->save();
                
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'circuito' => $circuito
                ];

            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos del circuito'
            ];
        }

        return response()->json($data, $data['code']);
    }

  public function update($id, Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'nombre' => 'required|string',
                'id_ciudad' => 'numeric',
                'ciudad' => 'required_without:id_ciudad|string',
            ]);

            if (isset($params_array['ciudad'])){ 
                $ciudad = new Cities();   
              $id_ciudad = $ciudad->ret_ID($params_array['ciudad']);
          }else {
                 $id_ciudad = $params_array['id_ciudad'];
          }

 
            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El circuito no se ha creado',
                    'errors' => $validate->errors()
                );            

            }else{
                $circuito =  Circuits::firstOrNew (['id'=> $id]);
                unset($params_array['id']);
                $circuito->id_ciudad = $id_ciudad ; //buscar el id
                $circuito->nombre = $params_array['nombre'];
                $circuito->observaciones = $params_array['observaciones'];
                $circuito->save();
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'circuito' => $circuito
                ];

            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos del circuito'
            ];
        }

        return response()->json($data, $data['code']);
    }



    public function destroy($id, Request $request){
        $circuito = Circuits::find($id);
        if(!empty($circuito)){
            $circuito->delete();
            $data =[
                'code' => 200,
                'status' => 'success',
                'circuito' => $circuito
            ];
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'El circuito no existe.'
            ];

        }
        return response()->json($data, $data['code']);
    }


    public function getCircuitsOfCity($idciudad, Request $request){
        $circuitos = Circuits::where('id_ciudad', '=', $idciudad)->get();
        if(!empty($circuitos)){        
               $data =[
                   'code' => 200,
                   'status' => 'success',
                   'circuitos' => $circuitos
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

