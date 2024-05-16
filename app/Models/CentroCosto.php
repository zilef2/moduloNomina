<?php

namespace App\Models;

use Carbon\Carbon;
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
        $parametros = session('parametros');
        $diffMinutes = Carbon::now()->diffInMinutes($this->update_at);
//        if($diffMinutes > 10){
            $BaseDeReportes = Reporte::Where('centro_costo_id',$this->id)->get();

            if($BaseDeReportes->count() > 0){
                $vardiurnas = 0;
                $varnocturnas = 0;
                $varextra_diurnas = 0;
                $varextra_nocturnas = 0;
                $vardominical_diurno = 0;
                $vardominical_nocturno = 0;
                $vardominical_extra_diurno = 0;
                $vardominical_extra_nocturno = 0;
                foreach ($BaseDeReportes as $index => $baseDeReporte) {
                    $user = User::find($baseDeReporte->user_id);
                    if($user){
                        $sal = $user->salario / 235;

                        $vardiurnas += ((double)$BaseDeReportes->sum('diurnas')) * $parametros->porcentaje_diurno * $sal;
                        $varnocturnas += ((double)$BaseDeReportes->sum('nocturnas')) * $parametros->porcentaje_nocturno * $sal;
                        $varextra_diurnas += ((double)$BaseDeReportes->sum('extra_diurnas')) * $parametros->porcentaje_extra_diurno * $sal;
                        $varextra_nocturnas += ((double)$BaseDeReportes->sum('extra_nocturnas')) * $parametros->porcentaje_extra_nocturno * $sal;
                        $vardominical_diurno += ((double)$BaseDeReportes->sum('dominical_diurno')) * $parametros->porcentaje_dominical_diurno * $sal;
                        $vardominical_nocturno += ((double)$BaseDeReportes->sum('dominical_nocturno')) * $parametros->porcentaje_dominical_nocturno * $sal;
                        $vardominical_extra_diurno += ((double)$BaseDeReportes->sum('dominical_extra_diurno')) * $parametros->porcentaje_dominical_extra_diurno * $sal;
                        $vardominical_extra_nocturno += ((double)$BaseDeReportes->sum('dominical_extra_nocturno')) * $parametros->porcentaje_dominical_extra_nocturno * $sal;
                    }
                }

                $this->mano_obra_estimada = $vardiurnas+ $varnocturnas+ $varextra_diurnas+ $varextra_nocturnas+ $vardominical_diurno+ $vardominical_nocturno+ $vardominical_extra_diurno+ $vardominical_extra_nocturno;
                $this->update();
            }
//        }
    }
}
