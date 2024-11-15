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

    //deep2 |
    public function actualizarEstimado($anio, $mes): void
    {
        $parametros = session('parametros');
        if(!$parametros){
            $parametros = Parametro::find(1);
        }

//        $diffMinutes = Carbon::now()->diffInMinutes($this->update_at);
//        if($diffMinutes >= 5) {  //todo: torepair //12mins
            $BaseDeReportes = Reporte::Where('centro_costo_id', $this->id)
                ->WhereYear('fecha_ini', $anio)
                ->WhereMonth('fecha_ini', $mes)
                ->get();

//        }
//        else{
//            $diaquincena = date("d");
//            $dayMedio = 15;
//            $operacion = $diaquincena > $dayMedio ? '>' : '<=';
//            $BaseDeReportes = Reporte::Where('centro_costo_id', $this->id)
//                ->WhereYear('fecha_ini',date("Y"))
//                ->WhereMonth('fecha_ini',date("m"))
//                ->WhereDay('fecha_ini',$operacion,$dayMedio)
//                ->get();
//        }

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
                    $porcentaje_diurno = $parametros->porcentaje_diurno * $sal;
                    $porcentaje_nocturno = $parametros->porcentaje_nocturno * $sal;
                    $porcentaje_extra_diurno = $parametros->porcentaje_extra_diurno * $sal;
                    $porcentaje_extra_nocturno = $parametros->porcentaje_extra_nocturno * $sal;
                    $porcentaje_dominical_diurno = $parametros->porcentaje_dominical_diurno * $sal;
                    $porcentaje_dominical_nocturno = $parametros->porcentaje_dominical_nocturno * $sal;
                    $porcentaje_dominical_extra_diurno = $parametros->porcentaje_dominical_extra_diurno * $sal;
                    $porcentaje_dominical_extra_nocturno = $parametros->porcentaje_dominical_extra_nocturno * $sal;

                    $vardiurnas += ((double)$baseDeReporte->diurnas) * $porcentaje_diurno;
                    $varnocturnas += ((double)$baseDeReporte->nocturnas) * $porcentaje_nocturno;
                    $varextra_diurnas += ((double)$baseDeReporte->extra_diurnas) * $porcentaje_extra_diurno;
                    $varextra_nocturnas += ((double)$baseDeReporte->extra_nocturnas) * $porcentaje_extra_nocturno;
                    $vardominical_diurno += ((double)$baseDeReporte->dominical_diurno) * $porcentaje_dominical_diurno;
                    $vardominical_nocturno += ((double)$baseDeReporte->dominical_nocturno) * $porcentaje_dominical_nocturno;
                    $vardominical_extra_diurno += ((double)$baseDeReporte->dominical_extra_diurno) * $porcentaje_dominical_extra_diurno;
                    $vardominical_extra_nocturno += ((double)$baseDeReporte->dominical_extra_nocturno) * $porcentaje_dominical_extra_nocturno;
                }
            }

            $this->mano_obra_estimada = $vardiurnas+ $varnocturnas+ $varextra_diurnas+ $varextra_nocturnas+ $vardominical_diurno+ $vardominical_nocturno+ $vardominical_extra_diurno+ $vardominical_extra_nocturno;
            $this->update();
//        }
    }
}
