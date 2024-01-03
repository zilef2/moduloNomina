<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cedula',
        'telefono',
        'celular',
        'cargo_id',
        //24abril
        'fecha_de_ingreso',
        'sexo',
        'salario',
        'centro_costo_id',
    ];

    /** * The attributes that should be hidden for serialization. * * @var array<int, string> */
    protected $hidden = [ 'password', 'remember_token', ];

    public function getFechaIngreso() {
        return date('d-m-Y', strtotime($this->attributes['fecha_ingreso']));
    }

    public function getCreatedAtAttribute() {
        return date('d-m-Y H:i', strtotime($this->attributes['created_at']));
    }

    public function getUpdatedAtAttribute() {
        return date('d-m-Y H:i', strtotime($this->attributes['updated_at']));
    }

    public function getEmailVerifiedAtAttribute() {
        return $this->attributes['email_verified_at'] == null ? null:date('d-m-Y H:i', strtotime($this->attributes['email_verified_at']));
    }

    public function getPermissionArray() {
        return $this->getAllPermissions()->mapWithKeys(function ($pr) {
            return [$pr['name'] => true];
        });
    }
    //fin permissions


    public function reportes() {
		return $this->hasMany('App\Models\Reporte');
	}
    public function cargo() {
		return $this->belongsTo(Cargo::class);
	}
    public function centros() {
		return $this->belongsToMany(CentroCosto::class,'centro_user');
	}
    public function ArrayCentrosID() {
        $result = [];
        if(!$this->centros->isEmpty()){
            foreach ($this->centros as $index => $centro) {
                $result[] = $centro->id;
            }
        }
        return $result;
    }
    public function ArraycentroName($numeroDeCentros = 0) {
        if(!$this->centros->isEmpty()){
            if($numeroDeCentros != 0){
                $arrayNombres = [];
                for ($i = 0; $i < $numeroDeCentros;$i++){
                    $tempCentro = $this->centros->skip($i)->first();
                    if(isset($tempCentro)){
                        $arrayNombres[] = $this->centros->skip($i)->first()->nombre;
                    }else{
                        break;
                    }
                }
            }else{
                foreach ($this->centros as $index => $centro) {
                    $arrayNombres[] = $centro->nombre;
                }
            }
            $result = $arrayNombres;
        } else{
            $result = 'Sin centros';
        }
        return $result;
	}

    public static function UsersWithRol($rol){
        return User::whereHas('roles', function ($query) use ($rol) {
            return $query->where('name', $rol);
        });
    }
    public function TieneEsteCentro($centroid){
        $susCentros = $this->centros()->pluck('centro_costos.id')->toArray();
        $result = in_array($centroid,$susCentros);
        return $result;
    }
}
