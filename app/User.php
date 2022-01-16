<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
//use Illuminate\Foundation\Auth\User as Authenticatable;
//use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

use App\Roles;
use App\Assigned;
use App\Locations;

class User extends Model
{
    
    //use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    protected $table = 'users';
    protected $fillable = [
        'user', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ]; 
    public  function ref_rol(){
       $rol = Roles::find($this->id_rol);
       
        if(!empty($rol)){
            return $rol['ref'];
        }else{
            return 0;
        }
       
    }

    public function ubicacion(){
        $id_asignado = Assigned::where('id_participante', $this->id_participante)->first();
       // var_dump($id_asignado['id_turno']); die();
         if(!empty($id_asignado)){
            $id_ubicacion = Locations::find($id_asignado['id_turno']);
            return $id_ubicacion['id'];
        }else{
            return 0;
        }

     }


}

/*
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
/*
     protected $fillable = [
    'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
 /* 
     protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
