<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use  Illuminate\Support\Facades\Validator;


use App\User;



class UserController extends Controller
{
    public function __construct(){
        $this->middleware('api.auth', ['except' =>['index', 'show','login']]);
    }

    public function register(Request $request){
        
        //Recoger los datos del usuario por post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        //Valuidar datos
        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'user' => 'required|string',
                'password' => 'required',
            ]);
        //Cifrar la contraseña

        //Comprobar si el usuario existe

        //Crear el usuario
        }else{

            $data =[
                'code' => 404,
                'status' => 'error',
                'message' => 'El usuario no se ha creado '
                ];
        }
        return response()->json($data, $data['code']);
    }

    public function login(Request $request){
        $jwtAuth = new \App\Helpers\JwtAuth();
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);
        $validate = Validator::make($params_array, [
            'user' => 'required|string',
            'password' => 'required',
        ]);

        if($validate->fails()){
            //La validacion a fallado
            $signup = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'El usuario no se ha podido identificar',
                'errors' => $validate->errors()
            );
        }else{
            //cifrar el password
            $pwd = hash('sha256', $params->password); 
            //devolver el token
            //echo $pwd;
            //echo $params->user;
            $signup = $jwtAuth->signup($params->user, $pwd);

            if(!empty($params->gettoken)){
                $signup = $jwtAuth->signup($params->user, $pwd, true);
            }
           // echo $signup;

        }
        //var_dump($pwd); die();
        return response()->json($signup, 200);
    }

    public function accesoaComponentes($id_rol){
        $accesos = DB::table('componentes')
        ->join('accesos','accesos.ref_componente','=', 'componentes.ref')
        ->join('roles','accesos.ref_rol','=', 'roles.ref')
        ->join('users','users.id_rol','=', 'roles.id')
        ->select(['componentes.id as id','componentes.nombre as nombre','componentes.ref as ref', 'accesos.id as id_accesos' ])
        ->where('users.id_rol', '=', $id_rol )
        ->get();
    
  

        if(is_object($accesos)){
            $data =[
                'code' => 200,
                'status' => 'success',
                'accesos' => $accesos
            ];
        }else{
            $data =[
                'code' => 404,
                'status' => 'error',
                'message' => 'El componentes no se ha localizado'
            ];
        }
        return response()->json($data, $data['code']);

    }


    public function datosSesion(Request $request){
      //  $params_array = json_decode($jwtAuth->signup($user, $pwd,true), true);

 //       show();
    }

    public function index(){
        //entrega todo sin que revise a que ciudad pertence
        $usuario = User::all();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'usuario' => $usuario
        ]);
        
    }

    public function show($id){
        $usuario = User::find($id);
        if(is_object($usuario)){
            $data =[
                'code' => 200,
                'status' => 'success',
                'usuario' => $usuario
            ];
        }else{
            $data =[
                'code' => 404,
                'status' => 'error',
                'message' => 'El usuario no se ha localizado'
            ];
        }
        return response()->json($data, $data['code']);
    }


    public function store(Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'user' => 'required|string|unique:users',
                'email' => 'required|string',
                'password' => 'required',
                'image' => 'string',
            ]);
  

            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El usuario no se ha creado',
                    'errors' => $validate->errors()
                );
            }else{

                $usuario = new User();
                $usuario->user = $params_array['user'];
                $usuario->email = $params_array['email'];
                
                //$usuario->password = password_hash($params_array['password'], PASSWORD_BCRYPT, ['cost'=>4]);
                $usuario->password = hash('sha256', $params_array['password']);
                $usuario->image = $params_array['image'];
                $usuario->id_rol = 0;


                $usuario->save();
                
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'usuario' => $usuario
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos del usuario'
            ];
        }
        return response()->json($data, $data['code']);

    }

    public function update($id, Request $request){
        

        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'user' => 'required|string|unique:users',
                'email' => 'required|string',
                'password' => 'required',
            ]);


            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El usuario no se ha creado',
                    'errors' => $validate->errors()
                );            
            }else{


                $usuario =  User::firstOrNew (['id'=> $id]);
                unset($params_array['id']);
     
                $usuario = new User();
                $usuario->user = $params_array['user'];
                $usuario->email = $params_array['email'];
                $usuario->password = hash('sha256', $params_array['password']);
                $usuario->image = $params_array['image'];
                $usuario->id_rol = $params_array['id_rol'];

                $usuario->save();
               
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'usuario' => $usuario
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos del usuario'
            ];
        }
        return response()->json($data, $data['code']);



    }

    public function destroy($id, Request $request){
        $usuario = User::find($id);
        if(!empty($usuario)){
            $usuario->delete();
               
            $data =[
                'code' => 200,
                'status' => 'success',
                'usuario' => $usuario
            ];
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'El usuario no existe.'
            ];
        }
        return response()->json($data, $data['code']);
    }

       
   


}