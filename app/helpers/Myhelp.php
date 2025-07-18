<?php

namespace App\helpers;

/*
 * updatingDate
 */

use App\Models\Reporte;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Normalizer;

if (!function_exists('getJefe')) {
	function getJefe(): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder|User|null {
		return \App\Models\User::where('name', 'Carlos Daniel Anaya Barrios')->first();
	}
}

class Myhelp {
	
	const MyRoles = [ //not using
	                  'empleado' => 1,
	                  'administrativo' => 2,
	                  'supervisor' => 3,
	                  'admin' => 9,
	                  'superadmin' => 10
	];
	
	public static function AuthUid(): ?int {
		$TheUser = Auth::user();
		if ($TheUser instanceof User) {
			return $TheUser->id;
		}
		abort(403, 'Unauthorized');
	}
	
	public static function zilefLog($escribirenlog = true) {
		$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
		$permissions = self::AuthU()->roles->pluck('name')[0];
		$ElMensaje = 'U:' . self::AuthU()->name;
		
		foreach ($trace[0] as $index => $trac) {
			$ElMensaje .= " $index = $trac";
		}
		
		if ($escribirenlog) {
			Log::channel(MyModels::getPermissiToLog($permissions))->info($ElMensaje);
			
			
			return MyModels::getPermissionToNumber($permissions);
		}
		else {
			return [$ElMensaje, $permissions];
		}
		
	}
	
	//************************logs************************\\
	
	public static function AuthU(): ?User {
		$TheUser = Auth::user();
		if ($TheUser instanceof User) {
			return $TheUser;
		}
		
		//        return redirect()->to('/');
		abort(403, 'Unauthorized');
	}
	
	public static function EscribirEnLog($thiis, $clase = '', $mensaje = '', $returnPermission = true, $critico = false) {
		$permissions = $returnPermission ? self::AuthU()->roles->pluck('name')[0] : null;
		$ListaControladoresYnombreClase = (explode('\\', get_class($thiis)));
		$nombreC = end($ListaControladoresYnombreClase);
		$esCritico = !$critico;
		if (!$critico) {
			$ElMensaje = $nombreC . ' ::' . self::AuthU()->name . ' | clase: ' . $clase;
			if ($permissions == 'admin' || $permissions == 'superadmin') {
				Log::channel('soloadmin')->info($ElMensaje);
			}
			else {
				if ($permissions == 'isadministrativo') {
					Log::channel('soloadministrativo')->info($ElMensaje);
				}
				else {
					if ($permissions == 'issupervisor') {
						Log::channel('issupervisor')->info($ElMensaje);
					}
					else {
						Log::info($ElMensaje);
					}
				}
			}
			
			
			return $permissions;
		}
		else {
			Log::critical('Es critico ' . $esCritico .'Vista: ' . ($nombreC ?? ' sin vista ') . ' U:' . self::AuthU()->name . ' ||' . ($clase ?? ' sin clase') . '||  Mensaje: ' . ($mensaje ?? ' sin mensaje'));
		}
	}
	
	//************************laravel************************\\
	
	public static function quitarTildes($palabras) {
		$normalizedString = Normalizer::normalize($palabras, Normalizer::FORM_D);
		$cleanString = preg_replace('/\p{Mn}/u', '', $normalizedString);
		
		
		return $cleanString;
	}
	
	//************************string************************\\

	public static function CalcularPendientesQuicena($Authuser) { //calcula primer y ultimo dia de las semanas
		$hoy = Carbon::now();
		$primerDiaSemana = $hoy->startOfWeek();
		$primerClone = clone $primerDiaSemana;
		$primerDiaSemanaPasada = $primerClone->subWeek()->startOfWeek();
		$hoy = Carbon::now();
		$ultimoDiaSemana = $hoy->addWeek()->endOfWeek();
		
		$ArrayOrdinarias = Reporte::Where('user_id', $Authuser->id)->WhereBetween('fecha_ini', [
				$primerDiaSemanaPasada,
				$ultimoDiaSemana
			])->whereTime('fecha_fin', '23:59:00')->selectRaw('DATE(fecha_fin) as fecha_fn, (dominical_nocturno + dominical_extra_nocturno + nocturnas) as horasDiaAnterior')->pluck('horasDiaAnterior', 'fecha_fn')
		;
		
		$ArrayOrdinarias[0] = Reporte::Where('user_id', $Authuser->id)->WhereBetween('fecha_ini', [
				$primerDiaSemana,
				$ultimoDiaSemana
			])->selectRaw('fecha_ini, (diurnas + nocturnas) as ordinarias')->get()->sum('ordinarias')
		;
		
		
		return $ArrayOrdinarias;
	}
	
	public function redirect($ruta, $seconds = 4) {
		sleep($seconds);
		
		
		return redirect()->to($ruta);
	}
	
	function cortarFrase($frase, $maxPalabras = 3) {
		$noTerminales = [
			"de",
			"a",
			"para",
			"of",
			"by",
			"for"
		];
		
		$palabras = explode(" ", $frase);
		$numPalabras = count($palabras);
		if ($numPalabras > $maxPalabras) {
			$offset = $maxPalabras - 1;
			while (in_array($palabras[$offset], $noTerminales) && $offset < $numPalabras) {
				$offset ++;
			}
			$ultimaPalabra = $palabras[$offset];
			if ((intval($ultimaPalabra)) != 0) {
				session(['ultimaPalabra' => $ultimaPalabra]);
			}
			
			
			return implode(" ", array_slice($palabras, 0, $offset + 1));
		}
		
		
		return $frase;
	}
	
	public function erroresExcel($errorFeo) {
		// $fila = session('ultimaPalabra');
		$error1 = "PDOException: SQLSTATE[22007]: Invalid datetime format: 1292 Incorrect date";
		if ($errorFeo == $error1) {
			return 'Existe una fecha invalida';
		}
		
		
		return 'Error desconocido';
	}
	
	//:? calcular cuantas horas ha trabajado en esta semana y la pasada

	public function ValidarFecha($laFecha) {
		if (strtotime($laFecha)) {
			return $laFecha;
		}
		
		
		return '';
	}
	
	public function updatingDate($date) {
		if ($date === null || $date == '1969-12-31') {
			return null;
		}
		
		
		return date('Y-m-d H:i:s', strtotime($date));
	}
	
} ?>
