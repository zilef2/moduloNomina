<?php

namespace App\helpers;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class zzloggingcrud {
	
	public static function zilefLogBulk($nameofModel, $numdestruidos, Throwable $th = null): void {
		$permissions = self::AuthU()->roles->pluck('name')[0];
		$ElMensaje = self::AuthU()->name . " se han borrado $numdestruidos $nameofModel";
		Log::channel(MyModels::getPermissiToLog($permissions))->info($ElMensaje);
		
	}
	
	private static function AuthU(): ?User {
		$TheUser = Auth::user();
		if ($TheUser instanceof User) {
			return $TheUser;
		}
		
		//        return redirect()->to('/');
		\abort(403, 'Unauthorized');
	}
	
	public static function zilefLogUpdate($tthis, $theModel = null, $original = null, $nombreUotro = 'nombre', Throwable $th = null): int {
		[$ElMensaje, $permissions] = self::zilefLogTrace(false);
		
		if ($theModel) {
			$changes = $theModel->getChanges();   // Valores después del cambio
			$atributosAntiguos = collect($original)->map(fn($value, $key) => "$key: $value")->implode(', ');
			$atributosModificados = collect($changes)->map(fn($value, $key) => "$key: $value")->implode(', ');
			$SoloColumnasModificadas = implode(', ', array_keys($changes));
			
			Myhelp::EscribirEnLog($tthis, "OLD: $atributosAntiguos NEW: $atributosModificados JUSTCOLUMNS: $SoloColumnasModificadas");
			
			$ElMensaje .= ' el type es: ' . gettype($theModel);
			$ElMensaje .= ' el id: ' . $theModel->id;
			$nombreu = $theModel->{$nombreUotro} ?? '';
			$ElMensaje .= " el $nombreUotro: " . $nombreu;
			
		}
		else {
			$mensajeErrorTH = $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile();
			
			$ElMensaje .= ' La actualizacion fallo: ' . $mensajeErrorTH;
		}
		Log::channel(MyModels::getPermissiToLog($permissions))->info($ElMensaje);
		
		
		return MyModels::getPermissionToNumber($permissions);
	}
	
	// Funcion para guardar los atributos que cambiaron en un proceso de actualización

	public static function zilefLogTrace($escribirenlog = true): array|int {
		$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
		$permissions = self::AuthU()->roles->pluck('name')[0];
		$ElMensaje = 'U:' . self::AuthU()->name;
		
		foreach ($trace[1] as $index => $trac) {
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
	
	public static function zilefStoreArrayLogTrace($paraellog, $escribirenlog = true) {
		$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
		$permissions = self::AuthU()->roles->pluck('name')[0];
		$ElMensaje = self::AuthU()->name;
		
		foreach ($paraellog as $index => $atributes) {
			$ElMensaje .= " $index = $atributes";
		}
		
		foreach ($trace[1] as $index => $trac) {
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
	
	public function NewZilefLogMessage($ElMensaje) : int {
		$permissions = self::AuthU()->roles->pluck('name')[0];
		Log::channel(MyModels::getPermissiToLog($permissions))->info($ElMensaje);
		return MyModels::getPermissionToNumber($permissions);
		
	}
	
} ?>
