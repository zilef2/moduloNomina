<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Param;

class CentroCosto extends Model
{
    use HasFactory;
    protected $fillable = [
		'nombre',
		'mano_obra_estimada',
		'activo', //finish_at 27mayo2024
		'descripcion',
		'clasificacion',
		'ValidoParaFacturar',
	];

    public function reportes() { return $this->hasMany(Reporte::class); }
    public function users() { return $this->BelongstoMany(User::class,'centro_user'); }

    public function ArrayListaSupervisores():Array {
        $centroid = $this->id;
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

    public function ArraySupervisores($centroid,$PosiblesSupervisores): array{
        $result = [];
        if($PosiblesSupervisores->count()){
            foreach ($PosiblesSupervisores as $supervisor) {
                $result[] = $supervisor->ArrayCentrosID();
            }
        }
        return array_unique($result);
	}

    public function ArraySupervIDs():Array {
        $centroid = $this->id;
        $supervisores = User::UsersWithRol('supervisor')->get();
        $result = $supervisores->map(function($user) use($centroid) {
            if($user->TieneEsteCentro($centroid)){
               return $user->id;
            }
            return null;
        })->filter()->toArray();
        return $result;
	}

    //deep2 | todo: mejorar y clarificar
    public function actualizarEstimado(){
        $parametros = session('parametros');
        if(!$parametros){
            $parametros = Parametro::find(1);
        }
        $diffMinutes = Carbon::now()->diffInMinutes($this->update_at);
        if($diffMinutes >= 0) {  //todo: torepair //12mins
            $BaseDeReportes = Reporte::Where('centro_costo_id', $this->id)->get();
        }
        else{
            $diaquincena = date("d");
            $dayMedio = 15;
            $operacion = $diaquincena > $dayMedio ? '>' : '<=';
            $BaseDeReportes = Reporte::Where('centro_costo_id', $this->id)
                ->WhereYear('fecha_ini',date("Y"))
                ->WhereMonth('fecha_ini',date("m"))->WhereDay('fecha_ini',$operacion,$dayMedio)
                ->get();
        }

        if($BaseDeReportes->count() > 0){
            $vardiurnas = 0;
            $varnocturnas = 0;
            $varextra_diurnas = 0;
            $varextra_nocturnas = 0;
            $vardominical_diurno = 0;
            $vardominical_nocturno = 0;
            $vardominical_extra_diurno = 0;
            $vardominical_extra_nocturno = 0;

            //todo: colocar $parametros->porcentaje_diurno * $sal como variable
            foreach ($BaseDeReportes as $index => $baseDeReporte) {
                $user = User::find($baseDeReporte->user_id);
                if($user){
                    $sal = $user->salario / 235;

                    $vardiurnas += ((double)$baseDeReporte->sum('diurnas')) * $parametros->porcentaje_diurno * $sal;
                    $varnocturnas += ((double)$baseDeReporte->sum('nocturnas')) * $parametros->porcentaje_nocturno * $sal;
                    $varextra_diurnas += ((double)$baseDeReporte->sum('extra_diurnas')) * $parametros->porcentaje_extra_diurno * $sal;
                    $varextra_nocturnas += ((double)$baseDeReporte->sum('extra_nocturnas')) * $parametros->porcentaje_extra_nocturno * $sal;
                    $vardominical_diurno += ((double)$baseDeReporte->sum('dominical_diurno')) * $parametros->porcentaje_dominical_diurno * $sal;
                    $vardominical_nocturno += ((double)$baseDeReporte->sum('dominical_nocturno')) * $parametros->porcentaje_dominical_nocturno * $sal;
                    $vardominical_extra_diurno += ((double)$baseDeReporte->sum('dominical_extra_diurno')) * $parametros->porcentaje_dominical_extra_diurno * $sal;
                    $vardominical_extra_nocturno += ((double)$baseDeReporte->sum('dominical_extra_nocturno')) * $parametros->porcentaje_dominical_extra_nocturno * $sal;
                }
            }

            $this->mano_obra_estimada = $vardiurnas+ $varnocturnas+ $varextra_diurnas+ $varextra_nocturnas+ $vardominical_diurno+ $vardominical_nocturno+ $vardominical_extra_diurno+ $vardominical_extra_nocturno;
            $this->update();
        }
    }
}
