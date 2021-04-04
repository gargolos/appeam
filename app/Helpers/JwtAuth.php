<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;

class JwtAuth{
    public $key;

    public function __construct(){
        $this->key = 'Mr4:11_A_ustedes_se_les_ha_dado_el_secreto';
    }

    public function signup($user, $password, $getToken = null){

        //Buscar si existe el usuario con sus credenciales
        $user = User::where([
            'user' => $user,
            'password' => $password,
        ])->first();
        //Comprobar si son correctas(objeto)
        $signup = false;
        if(is_object($user)){
            $signup = true;
        }

        //Generar el token con los datos del usuario identidicado
        if($signup){
            $token = array(
                'sub' => $user->id,
                'user' => $user->user,
                'email' => $user->email,
                'iat' => time(),
                'exp' => time()+ (10*365*24*60*60),  //10 años de token
            );

            $jwt = JWT::encode($token, $this->key, 'HS256');
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
            
            //Devolber los datos decodificados o el token, en funcion de un parametro
            if(is_null($getToken)){
                $data = $jwt;
            }else{
                $data = $decoded;
            }
            

        }else{
            $data =[
                'status' => 'error',
                'message' => 'Login incorrecto'
                ];
        }

  
        return $data;// response()->json($data, $data['code']);

    }
}



