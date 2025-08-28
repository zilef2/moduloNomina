<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\Jobs\EnviarViaticoJob;
use App\Mail\UsuariosLogeadosHoy;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
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
	

	public function logeadoshoy() {
		
		$path = storage_path('logs/laravel.log');
		$usuariosLog = [];
		$hoy = Carbon::today()->format('Y-m-d'); // Ejemplo: 2025-08-27
		// $fecha = Carbon::yesterday()->format('Y-m-d');
		$controller = 'ReportesController';

		if (File::exists($path)) {
			foreach (File::lines($path) as $line) {
				// Filtrar por fecha y por controlador
				if (str_contains($line, $hoy) && str_contains($line, $controller)) {
					if (preg_match('/::(.*?)\s+\|/', $line, $matches)) {
						$usuariosLog[] = trim($matches[1]);
					}
				}
			}
		}

		$usuariosLog = array_unique($usuariosLog);
		
		// Buscar en BD según nombre (ajusta si tu login es por email/username)
		$usuarios = User::whereIn('name', $usuariosLog)->get();
		
		// Agrupar
		$agrupados = [
			'empleado' => [],
			'administrativo' => [],
			'otros'    => [],
		];
		
		foreach ($usuarios as $user) {
			if ($user->roles->pluck('name')->contains('empleado')) {
				$agrupados['empleado'][] = $user->name;
			} else {
				if ($user->roles->pluck('name')->contains('administrativo')) {
					$agrupados['administrativo'][] = $user->name;
				} else {
					$agrupados['otros'][] = $user->name;
				}
			}
		}
		Mail::to('ajelof2@gmail.com')->send(new UsuariosLogeadosHoy($agrupados, Carbon::today()->format('d/m/Y')));
		return   '<p>Correo enviado correctamente </p>'
				.'<p>| empleados '.count($agrupados['empleado']).'</p><p>'. implode(', ', $agrupados['empleado']) . '</p>'
				.'<p>| administrativo '.count($agrupados['administrativo']).'</p><p>'. implode(', ', $agrupados['administrativo']) . '</p>'
				.'<p>| otros '.count($agrupados['otros']).'</p><p>'. implode(', ', $agrupados['otros']) . '</p>'
	;
	}
}
