<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentroCosto extends Model
{
    use HasFactory;
    protected $fillable = [
		'nombre'
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
}
