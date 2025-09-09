<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\zzloggingcrud;
use App\Jobs\EnviarViaticoJob;
use App\Models\User;
use Illuminate\Http\Request;

class EnvioCorreos extends Controller {
	
	public function EnviaralGerente(Request $request, ?User $myuser, $cuantosViaticos, $total): string {
		$jefe = User::Where('name', 'Carlos Daniel Anaya Barrios')->first();
		if ($jefe) {
			$detalle = [
				'mensaje' => "Se han generado $cuantosViaticos viáticos por un valor de $total. 
                              El solicitante es $myuser->name.
                              Haga click aqui:   https://modnom.ecnomina.com/solicitud_viatico  si desea ver los pendientes."
			];
			if (\Illuminate\Support\Facades\App::environment('production')) {
				EnviarViaticoJob::dispatch($jefe->email, $detalle)->delay(now()->addSeconds(3));
				EnviarViaticoJob::dispatch('ajelof2@gmail.com', $detalle)->delay(now()->addSeconds(3));
			
				$mensaje = "Correo enviado. $cuantosViaticos viáticos por un valor de $total.";
			}
			else {
				EnviarViaticoJob::dispatch('alejofg2@gmail.com', $detalle)->delay(now()->addSeconds(15));
				$mensaje = "Correo enviado solo al desarrollador";
				
			}
			
		}
		else {
			$mensaje = " ERROR: no se encontro al jefe";
			EnviarViaticoJob::dispatch('ajelof2@gmail.com', ['mensaje' => $mensaje])->delay(now()->addSeconds());
			\App\Jobs\LogZilefMessage::dispatch('');
			
		}
		
		
		return $mensaje;
	}
	
	public function EnviarCotizacion($total): string {
		$jefe = User::jefe();
		$isLocal = str_contains(gethostname(), 'AlejoGrandote');
		
		if ($jefe) {
			$detalle = [
				'mensaje' => "Se han generado un requisito de la reunion pasada, el valor del requisito fue aproximado a $total. 
                              Haga click aqui:   https://modnom.ecnomina.com/desarrollo si desea ver los pendientes."
			];
			if (\Illuminate\Support\Facades\App::environment('production')) {
				EnviarViaticoJob::dispatch($jefe->email, $detalle)->delay(now()->addSeconds(2));
				EnviarViaticoJob::dispatch('ajelof2@gmail.com', $detalle)->delay(now()->addSeconds(2));
				$mensaje = "Correo enviado (total: $ $total)";
			}
			else {
				
				EnviarViaticoJob::dispatch('alejofg2@gmail.com', $detalle)->delay(now()->addSeconds(2));
				$mensaje = "Correo enviado solo a alejo. Ambiente: $isLocal. Correo enviado (total: $ $total). El mensaje que iria al jefe es:". $detalle['$mensaje'];
				
			}
			
		}else {
			$mensaje = "-";
			Myhelp::EscribirEnLog($this, ' ERROR: no se encontro al jefe');
		}
		
		
		return $mensaje;
	}
}
