<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentroCosto extends Model
{
    use HasFactory;
    protected $fillable = [
		'nombre',
		'mano_obra_estimada'
	];

    public function reportes() { return $this->hasMany(Reporte::class); }
    public function users() { return $this->BelongstoMany(User::class,'centro_user'); }

    public function ArrayListaSupervisores($centroid):Array {
        $supervisores = User::UsersWithRol('supervisor')->get();
        $result = $supervisores->map(function($user) use($centroid) {
            if($user->TieneEsteCentro($centroid)){
               return $user->name;
            }
            return null;
        })->filter()->toArray();
//		$result = $this->users->flatMap(function($user) use ($centroid){
//			$rol = $user->roles->pluck('name')[0];
//            $tieneAlCentro = $user->centros()->get()->contains('id', $centroid);
//
//			if($tieneAlCentro && $rol === 'supervisor')
//			return collect($user);
//		});
        return $result;
	}

    public function actualizarEstimado(){
            $a = (double) Reporte::Where('centro_costo_id',$this->id)->sum('horas_trabajadas');
//            if($this->id == 23) dd($a);
        $this->mano_obra_estimada =$a;
        $this->update();
    }
}
