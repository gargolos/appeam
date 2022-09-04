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


use PhpParser\Node\Stmt\Return_;

class ParticipantController extends Controller
{
 
    public function __construct(){
        //$this->middleware('api.auth', ['except' =>['index', 'show']]);
    }

    public function index(Request $request){
        //$participantes = Participants::all();
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        $participantes = DB::table('participantes') 
        ->join('circuitos','participantes.id_circuito','=', 'circuitos.id')
        ->select(['participantes.*', 'circuitos.nombre as circuito'])
        ->where('participantes.id_ciudad', '=',  $params_array['id_ciudad'])
        ->get();

        if(is_object($participantes)){
            $data =[
                'code' => 200,
                'status' => 'success',
                'participant' => $participantes
            ];
        }else{
            $data =[
                'code' => 404,
                'status' => 'error',
                'message' => 'No se encontraron participantes'
            ];
        }
        return response()->json($data, $data['code']);

       /*  return response()->json([
            'code' => 200,
            'status' => 'successII',
            'participant' => $participantes
        ]); 
        return response()->json($data, $data['code']);*/
    }


    
    public function show($id){
        $participante = Participants::find($id);
        if(is_object($participante)){
            $data =[
                'code' => 200,
                'status' => 'successSS',
                'participant' => $participante
            ];
        }else{
            $data =[
                'code' => 404,
                'status' => 'error',
                'message' => 'El participante no se ha localizado'
            ];
        }
        return response()->json($data, $data['code']);
    }


    public function store(Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'n' => 'required|string',
                'ap' => 'required|string',
                'am' => 'string',
                'ac' => 'string',
                'e' => 'email',
                't' => 'string|max:15',
                'c' => 'string|max:15',
                'congregacion' => 'string|max:40',
                'circuito' => 'required|string',
                'nacimiento' => 'required|date',
                'bautismo' => 'date',
                'sexo' => 'alpha|starts_with:M,F',
                'asignacion' => 'numeric',
                'lun' => 'numeric',
                'mar' => 'numeric',
                'mie' => 'numeric',
                'jue' => 'numeric',
                'vie' => 'numeric',
                'sab' => 'numeric',
                'dom' => 'numeric',
                'foto1' => 'string|max:255',
                'foto2' => 'string|max:255',
                'id_ciudad' => 'required|numeric',
                'estado' => 'numeric',
                'fecha_registro' => 'date',
                'observaciones' => 'string',   
                'ppeamId' => 'numeric'
            ]);
            
      
                           
            $id_ciudad = $params_array['id_ciudad']; //buscar el id
  
            $circuit = new Circuits();                
            $id_circuito = $circuit->ret_ID($params_array['circuito'], $id_ciudad); //buscar el id


            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El participante no se ha creado',
                    'errors' => $validate->errors()
                );
            }elseif($id_circuito==0 || $id_ciudad==0 ) {
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El participante no se ha creado, el circuito o la ciudad no existen en la base de datos.',
                );
            }else{

                $participante = new Participants();
                            
                $participante->id_circuito =$id_circuito; //buscar el id              
                $participante->id_ciudad = $id_ciudad ; //buscar el id

                $participante->n = $params_array['n'];
                $participante->ap = $params_array['ap'];
                $participante->am = $params_array['am'];
                $participante->ac = $this->validaDefault($params_array['ac'],'');
                $participante->e = $this->validaDefault($params_array['e'],'');
                $participante->t = $params_array['t']; 
                $participante->c = $params_array['c'];
                $participante->congregacion = $params_array['congregacion'];
                

                $participante->nacimiento = $params_array['nacimiento']; 
                $participante->bautismo = $params_array['bautismo'];
                $participante->sexo = $params_array['sexo'];
                $participante->asignacion = $this->validaDefault($params_array['asignacion'],0); //Default 0 Publicador 
                $participante->lun = $this->validaDefault($params_array['lun'],0); //Default 0 No Puede
                $participante->mar = $this->validaDefault($params_array['mar'],0); //Default 0 No Puede
                $participante->mie = $this->validaDefault($params_array['mie'],0); //Default 0 No Puede
                $participante->jue = $this->validaDefault($params_array['jue'],0); //Default 0 No Puede
                $participante->vie = $this->validaDefault($params_array['vie'],0); //Default 0 No Puede
                $participante->sab = $this->validaDefault($params_array['sab'],0); //Default 0 No Puede
                $participante->dom = $this->validaDefault($params_array['dom'],0); //Default 0 No Puede
                $participante->foto1 = $this->validaDefault($params_array['foto1'],'');
                $participante->foto2 = $this->validaDefault($params_array['foto2'],'');
                //$participante->id_turno =  $params_array['turno']; // buscar el turno Default 1 Sin Asignar
                $participante->estado = $this->validaDefault($params_array['estado'],1);    // default 1 No asignado
                $participante->fecha_registro = $this->validaDefault($params_array['fecha_registro'],'');
                $participante->observaciones = $this->validaDefault($params_array['observaciones'],'');
               
                $mydate=$params_array['nacimiento'];
                $cuenta=$this->validaRef(strtoupper(substr($params_array['ap'],0,2)) . strtoupper(substr( $params_array['am'],0,1)) . strtoupper(substr($params_array['n'],0,1)) . date("y", strtotime($mydate)) .  date("m", strtotime($mydate)) . date("d", strtotime($mydate)));  
                $participante->referencia = strtoupper(substr($params_array['ap'],0,2)) . strtoupper(substr( $params_array['am'],0,1)) . strtoupper(substr($params_array['n'],0,1)) . date("y", strtotime($mydate)) .  date("m", strtotime($mydate)) . date("d", strtotime($mydate)). $cuenta; //construir la funcion ref
                
                $participante->ppeamId = $params_array['ppeamId']; 
              
                $participante->save();
                
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    'participant' => $participante
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos del participante2'
            ];
        }
        return response()->json($data, $data['code']);

    }

    public function update($id, Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'n' => 'required|string',
                'ap' => 'required|string',
                'am' => 'string',
                'ac' => 'string',
                'e' => 'email',
                't' => 'string|max:15',
                'c' => 'string|max:15',
                'congregacion' => 'string|max:40',
                'circuito' => 'required|string',
                'nacimiento' => 'required|date',
                'bautismo' => 'date',
                'sexo' => 'alpha|starts_with:M,F',
                'asignacion' => 'numeric',
                'lun' => 'numeric',
                'mar' => 'numeric',
                'mie' => 'numeric',
                'jue' => 'numeric',
                'vie' => 'numeric',
                'sab' => 'numeric',
                'dom' => 'numeric',
                'foto1' => 'string|max:255',
                'foto2' => 'string|max:255',
                'id_ciudad' => 'required',
                'estado' => 'numeric',
                'fecha_registro' => 'date',
                'observaciones' => 'string',   
                'ppeamId' => 'numeric'
            ]);
            //$ciudad = new Cities();                
            //$id_ciudad = $ciudad->ret_ID($params_array['ciudad']); //buscar el id


            $circuit = new Circuits();                
            $id_circuito = $circuit->ret_ID($params_array['circuito'], $params_array['id_ciudad']); //buscar el id
           // $ciudad = new Cities();                
           // $id_ciudad = $ciudad->ret_ID($params_array['ciudad']); //buscar el id

           // $circuit = new Circuits();                
           // $id_circuito = $circuit->ret_ID($params_array['circuito'], $id_ciudad); //buscar el id
            $id_ciudad= $params_array['id_ciudad'];
            //$id_circuito=$params_array['id_circuito'];
            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El participante no se ha creado',
                    'errors' => $validate->errors()
                );
            

            }elseif($id_circuito==0 || $id_ciudad==0 ) {
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'id_circuito' => $id_circuito,
                    'message' => 'El participante no se ha creado, el circuito o la ciudad no existen en la base de datos.',
                );
            }else{


  
                $participante =  Participants::firstOrNew (['id'=> $id]);
                unset($params_array['id']);
                
                //unset($params_array['referencia']);
                $participante->id_circuito =$id_circuito;              
                $participante->id_ciudad = $id_ciudad ; 

                $participante->n = $params_array['n'];
                $participante->ap = $params_array['ap'];
                $participante->am = $params_array['am'];
                $participante->ac = $this->validaDefault($params_array['ac'],'');
                $participante->e = $this->validaDefault($params_array['e'],'');
                $participante->t = $params_array['t']; 
                $participante->c = $params_array['c'];
                $participante->congregacion = $params_array['congregacion'];
                

                $participante->nacimiento = $params_array['nacimiento']; 
                $participante->bautismo = $params_array['bautismo'];
                $participante->sexo = $params_array['sexo'];
                $participante->asignacion = $this->validaDefault($params_array['asignacion'],0); //Default 0 Publicador 
                $participante->lun = $this->validaDefault($params_array['lun'],0); //Default 0 No Puede
                $participante->mar = $this->validaDefault($params_array['mar'],0); //Default 0 No Puede
                $participante->mie = $this->validaDefault($params_array['mie'],0); //Default 0 No Puede
                $participante->jue = $this->validaDefault($params_array['jue'],0); //Default 0 No Puede
                $participante->vie = $this->validaDefault($params_array['vie'],0); //Default 0 No Puede
                $participante->sab = $this->validaDefault($params_array['sab'],0); //Default 0 No Puede
                $participante->dom = $this->validaDefault($params_array['dom'],0); //Default 0 No Puede
                $participante->foto1 = $this->validaDefault($params_array['foto1'],'');
                $participante->foto2 = $this->validaDefault($params_array['foto2'],'');
                //$participante->id_turno =  $params_array['turno']; // buscar el turno Default 1 Sin Asignar
                $participante->estado = $this->validaDefault($params_array['estado'],1);    // default 1 No asignado
                $participante->fecha_registro = $this->validaDefault($params_array['fecha_registro'],'');
                $participante->observaciones = $this->validaDefault($params_array['observaciones'],'');
               
                //$mydate=$params_array['nacimiento'];
                //$cuenta=$this->validaRef(strtoupper(substr($params_array['ap'],0,2)) . strtoupper(substr( $params_array['am'],0,1)) . strtoupper(substr($params_array['n'],0,1)) . date("y", strtotime($mydate)) .  date("m", strtotime($mydate)) . date("d", strtotime($mydate)));  
                //$participante->referencia = strtoupper(substr($params_array['ap'],0,2)) . strtoupper(substr( $params_array['am'],0,1)) . strtoupper(substr($params_array['n'],0,1)) . date("y", strtotime($mydate)) .  date("m", strtotime($mydate)) . date("d", strtotime($mydate)). $cuenta; //construir la funcion ref
                
                $participante->ppeamId = $params_array['ppeamId']; 
                //$participante->id_users = $params_array['id_users'];

                $participante->save();
               
                $data =[
                    'code' => 200,
                    'status' => 'successYY',
                    'participant' => $participante
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos del participante2store'
            ];
        }
        return response()->json($data, $data['code']);



    }

    public function destroy($id, Request $request){
        $participante = Participants::find($id);
        if(!empty($participante)){
            $participante->delete();
               
            $data =[
                'code' => 200,
                'status' => 'successDD',
                'participant' => $participante
            ];
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'El participante no existe.'
            ];
        }
        return response()->json($data, $data['code']);
    }



    public function validaDefault($valor, $default){
        if (isset($valor) && !empty($valor)){
            return $valor; 
        }else{
            return $default; //Default 0 Publicador 
        }
    }

    public function validaRef($referencia){
        //Devuelve la cantidad de Referencias encontradas
        $participante =Participants::where('referencia', 'like',  substr($referencia,0,strlen($referencia)-1) . '%' )->get();
        $params_array = json_decode($participante, true); //arreglo
        if(empty($params_array)){
            return 0;
        } else { return $participante->count();}

    }

    public function candidates(Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if (isset($params_array['id_ubicacion'])){  
            $candidates1 = DB::table('participantes') 
            ->whereNotExists(function ($query){
                $query->select(DB::raw(1))
                    ->from('asignadoa')
                ->whereColumn('asignadoa.id_participante', 'participantes.id');
            })          
            ->select(['participantes.id', 'participantes.n','participantes.ap','participantes.am','participantes.ac','participantes.id_circuito', DB::raw('0 as asignados') ])
            ->where('participantes.id_ciudad', '=',  $params_array['id_ciudad']);
           
          $candidates = DB::table('participantes') 
              ->whereExists(function ($query){
                $query->select(DB::raw(1))
                    ->from('asignadoa')
                    ->whereColumn('asignadoa.id_participante', 'participantes.id');
                    })
            ->join('asignadoa','participantes.id','=', 'asignadoa.id_participante')  
            ->join('turnos','asignadoa.id_turno','=', 'turnos.id')
            ->join('ubicaciones','turnos.id_ubicacion','=', 'ubicaciones.id')
                  
            ->select(['participantes.id', 'participantes.n','participantes.ap','participantes.am','participantes.ac','participantes.id_circuito', DB::raw('1 as asignados') ])
            ->where('participantes.id_ciudad', '=',  $params_array['id_ciudad'])
            ->where('ubicaciones.id', '=',  $params_array['id_ubicacion'])
            ->union($candidates1)
            ->get();
    
        }else{

        $candidates1 = DB::table('participantes') 
        ->whereNotExists(function ($query){
            $query->select(DB::raw(1))
                ->from('asignadoa')
            ->whereColumn('asignadoa.id_participante', 'participantes.id');
        })
        ->select(['participantes.id', 'participantes.n','participantes.ap','participantes.am','participantes.ac','participantes.id_circuito', DB::raw('0 as asignados') ])
        ->where('participantes.id_ciudad', '=',  $params_array['id_ciudad']);
       
      $candidates = DB::table('participantes') 
          ->whereExists(function ($query){
            $query->select(DB::raw(1))
                ->from('asignadoa')
                ->whereColumn('asignadoa.id_participante', 'participantes.id');
                })
        ->select(['participantes.id', 'participantes.n','participantes.ap','participantes.am','participantes.ac','participantes.id_circuito', DB::raw('1 as asignados') ])
        ->where('participantes.id_ciudad', '=',  $params_array['id_ciudad'])
        ->union($candidates1)
        ->get();
    }

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'candidatos' => $candidates
        ]);
        
    }
    
    public function updateFotos(Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if(!empty($params_array)){
            $validate = Validator::make($params_array, [
                'REF' => 'required|string',
                'e' => 'email',
                'foto1' => 'string|max:255',
                'foto2' => 'string|max:255',
            ]);

            $ref = $params_array['REF'];
            if($validate->fails()){
                //La validacion a fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Los datos proporcionados no son correctos',
                    'errors' => $validate->errors()
                );
            

            }else{
                /*buscar con la REF*/
                $participante =  Participants::firstOrNew (['referencia'=> $ref]);
                $participante->foto1 = $this->validaDefault($params_array['foto1'],$participante->foto1);                
                $participante->foto1 = $this->validaDefault($params_array['foto2'],$participante->foto1);

                $participante->save();
               
                $data =[
                    'code' => 200,
                    'status' => 'success',
                    //'participant' => $participante
                ];
                
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos del participante2store'
            ];
        }
        return response()->json($data, $data['code']);

    }

}
