<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\Jobs\EnviarViaticoJob;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller {
	
	public function guardarCiudad(Request $r): void {
		$user = Myhelp::AuthU();
		if (Schema::hasTable('ubicacion')) {
			$ipAddress = $r->ip();
			DB::table('ubicacion')->insert([
				                               'ubicacion'  => $r->ciudad,
				                               'valido'     => 1,
				                               'userid'     => $user->id,
				                               'name'       => $user->name,
				                               'email'      => $ipAddress ?? 'Sin ip',
				                               'created_at' => Carbon::now()
			                               ]);
		}
	}
	
	public function ProbarJob() {
		EnviarViaticoJob::dispatch('ajelof2@gmail.com', [
			'mensaje' => "Se han generado 0 viáticos por un valor de 0. 
                              El solicitante es yo mismo
                              Haga click aqui:   https://modnom.ecnomina.com/solicitud_viatico  si desea ver los pendientes."
		])->delay(now()->addSeconds());
	}
}
