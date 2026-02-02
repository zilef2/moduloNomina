<?php

namespace App\Http\Controllers;

use App\helpers\MyGlobalHelp;
use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\Jobs\EnviarViaticoJob;
use App\Mail\AvisoPagoDesarrollo;
use App\Mail\UsuariosHorasExtrasMail;
use App\Mail\UsuariosLogeadosHoy;
use App\Models\CentroCosto;
use App\Models\desarrollo;
use App\Models\Parametro;
use App\Models\Permission;
use App\Models\Reporte;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class DashboardController extends Controller {
	
	public int $LimiteDiasSinPagar = 15;
	public int $numberPermissions = 15;
	
	public function __construct() {
		
		$this->middleware(function ($request, $next) {
		
//			$permissions = Myhelp::AuthU()->roles->pluck('name')[0];
//			$this->numberPermissions = MyModels::getPermissionToNumber($permissions);
			
//			$nombre = str_replace('Controller', '', class_basename(static::class)); // Obtener el nombre del controlador sin "Controller"
			$this->numberPermissions = MyModels::getPermissionToNumber(
			    Myhelp::EscribirEnLog($this, class_basename(static::class))
			);
			return $next($request);
		});
		
	}
	
	public function Dashboard() {
		$ListaControladoresYnombreClase = (explode('\\', get_class($this)));
		$nombreC = end($ListaControladoresYnombreClase);
		$Authuser = Myhelp::AuthU();
		Myhelp::EscribirEnLog($this, $nombreC, ' U -> ' . $Authuser->name . ' Accedio al dashboard ', false);
		
		$userid = $Authuser->id;
		if ($this->numberPermissions === 1) {
			return redirect()->route('Reportes.index')->with('success', 'Bienvenido');
			// $reportes = (int) Reporte::Where('user_id', $Authuser->id)->count();
		}
		else { // si no eres empleado
			$reportes = Reporte::count();
			$elmespasado = Carbon::now()->startOfWeek()->addMonth(- 1);
			
			if ($this->numberPermissions === 3) {
				$centroMio = $Authuser->ArrayCentrosID();
				$reportes = Reporte::WhereIn('centro_costo_id', $centroMio)->count();
				
				$ultimos5dias = [
					'Mes pasado' => Reporte::WhereIn('centro_costo_id', $centroMio)->whereValido(1)->where('fecha_ini', '<', Carbon::today()->addMonth(- 1)->endOfMonth())->where('fecha_ini', '>=', Carbon::today()->addMonth(- 1)->firstOfMonth())->get()->count(),
					
					'Semana pasada' => Reporte::WhereIn('centro_costo_id', $centroMio)->whereValido(1)->whereBetween('fecha_ini', [
						Carbon::now()->addDays(- 7)->startOfWeek(),
						Carbon::now()->addDays(- 7)->endOfWeek()
					])->get()->count(),
					
					'Semana actual' => Reporte::WhereIn('centro_costo_id', $centroMio)->whereValido(1)->whereBetween('fecha_ini', [
						Carbon::now()->startOfWeek(),
						Carbon::now()->endOfWeek()
					])->get()->count(),
				];
				
				$diasNovalidos = [
					'Mes pasado' => Reporte::WhereIn('centro_costo_id', $centroMio)->whereIn('valido', [
						0,
						2
					])->where('fecha_ini', '>=', $elmespasado)->where('fecha_ini', '<', Carbon::today()->startOfMonth())->get()->count(),
					'Mes actual' => Reporte::WhereIn('centro_costo_id', $centroMio)->whereIn('valido', [
						0,
						2
					])->where('fecha_ini', '>', Carbon::today()->startOfMonth())->get()->count(),
				];
				
			}
			else { //si no eres administrativo
				$ultimos5dias = [
					'Mes pasado' => Reporte::whereValido(1)->where('fecha_ini', '<', Carbon::today()->addMonth(- 1))->get()->count(),
					
					'Semana pasada' => Reporte::whereValido(1)->whereBetween('fecha_ini', [
						Carbon::now()->addDays(- 7)->startOfWeek(),
						Carbon::now()->addDays(- 7)->endOfWeek()
					])->get()->count(),
					
					'Semana actual' => Reporte::whereValido(1)->whereBetween('fecha_ini', [
						Carbon::now()->startOfWeek(),
						Carbon::now()->endOfWeek()
					])->get()->count(),
				];
				$diasNovalidos = [
					'Mes pasado' => Reporte::whereIn('valido', [
						0,
						2
					])->where('fecha_ini', '<', Carbon::today()->startOfMonth())->where('fecha_ini', '>=', $elmespasado)->get()->count(),
					'Mes actual' => Reporte::whereIn('valido', [
						0,
						2
					])->where('fecha_ini', '>', Carbon::today()->startOfMonth())->get()->count(),
				];
			}
			
			$usuariosConRol = User::whereHas('roles', function ($q) {
				$q->where('name', 'empleado');
			})->whereHas('reportes')->withSum('reportes as total_horas', 'horas_trabajadas')->orderByDesc('total_horas')->get()->take(5);
			foreach ($usuariosConRol as $value) {
				$BooleanreportoHoy = Reporte::where('user_id', $value->id)->whereDate('fecha_fin', Carbon::today())->first();
				
				if ($BooleanreportoHoy !== null) {
					$trabajadoresHoy[$value->name] = $BooleanreportoHoy->horas_trabajadas;
				}
				else {
					$trabajadoresHoy[$value->name] = 0;
				}
			}
			
			$centros = CentroCosto::all();
			foreach ($centros as $value) {
				$BooleanReporteCentro = Reporte::where('centro_costo_id', $value->id)->whereDate('fecha_fin', Carbon::today())->sum('horas_trabajadas');
				if ($BooleanReporteCentro !== null) {
					$centrosHoy[$value->nombre] = $BooleanReporteCentro;
				}
				else {
					$centrosHoy[$value->nombre] = 0;
				}
			}
			$conteoPorRol = User::whereNotIn('cargo_id', [1, 2])
			                    ->with('roles')->get()->flatMap->roles->groupBy('name')->map->count();
			
			$topCentros = CentroCosto::select('nombre', 'mano_obra_estimada')
			                         ->where('mano_obra_estimada', '>', 25000)
			                         ->orderByDesc('mano_obra_estimada')
			                         ->limit(10)->get()
			;
			
			$chartLabels = $topCentros->pluck('nombre');
			$chartValues = $topCentros->pluck('mano_obra_estimada');
		} //fin, si no eres empleado
		
		return Inertia::render('Dashboard', [
			'versionZilef'      => '25.6.1',
			'users'             => User::count(),
			'roles'             => Role::count(),
			'permissions'       => Permission::count(),
			'reportes'          => $reportes,
			'ultimos5dias'      => $ultimos5dias,
			'diasNovalidos'     => $diasNovalidos,
			'trabajadoresHoy'   => $trabajadoresHoy ?? [],
			'centrosHoy'        => $centrosHoy ?? [],
			'numberPermissions' => $this->numberPermissions,
			'userid'            => $userid,
			'conteoPorRol'      => $conteoPorRol,
			'topCentros'        => $topCentros,
			'chartLabels'       => $chartLabels,
			'chartValues'       => $chartValues,
		]);
	}
	
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
		
		return '<p>Job Enviado a mi mismo</p>';
	}
	
	public function recordarPago() {
		
		$QueryPendiente = Desarrollo::Where('estado', 'Esperando pago parcial')->whereDoesntHave('pagos')->whereNotNull('fecha_cotizacion_aceptada')->whereDate('fecha_cotizacion_aceptada', '<=', Carbon::now()->subDays($this->LimiteDiasSinPagar));
		
		$dearrollopendiente = clone $QueryPendiente;
		$dearrollopendiente = $dearrollopendiente->first();
		$cuantosDesarrollosPendientes = $QueryPendiente->count();
		if ($dearrollopendiente) {
			$fechacotiza = $dearrollopendiente->fecha_cotizacion_aceptada;
			$diffforhum = MyGlobalHelp::diffCarbonMonthNDays($fechacotiza);
			//			$diffforhum = Carbon::parse($fechacotiza)->diffForHumans();
			$desarrollo = Desarrollo::findOrFail($dearrollopendiente->id);
			
			if (app()->environment('test')) {
				Mail::to([
					         'alejofg2@gmail.com',
					         'docmadridtorres@gmail.com'
				         ])->send(new AvisoPagoDesarrollo($desarrollo, $cuantosDesarrollosPendientes));
			}
			else {
				if (app()->environment('production')) {
					$jefe = User::Where('name', 'Carlos Daniel Anaya Barrios')->first();
					$jefemail = $jefe->email;
					Mail::to([
						         $jefemail,
						         'ajelof2@gmail.com'
					         ])->send(new AvisoPagoDesarrollo($desarrollo, $cuantosDesarrollosPendientes));
				}
			}
			
			return "Aviso enviado. Fecha en que se acepto la cotizacion $fechacotiza, $diffforhum";
		}
		
		return "no hay desarrollos pendientes";
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
			'empleado'       => [],
			'administrativo' => [],
			'otros'          => [],
		];
		
		foreach ($usuarios as $user) {
			if ($user->roles->pluck('name')->contains('empleado')) {
				$agrupados['empleado'][] = $user->name;
			}
			else {
				if ($user->roles->pluck('name')->contains('administrativo')) {
					$agrupados['administrativo'][] = $user->name;
				}
				else {
					$agrupados['otros'][] = $user->name;
				}
			}
		}
		Mail::to('ajelof2@gmail.com')->send(new UsuariosLogeadosHoy($agrupados, Carbon::today()->format('d/m/Y')));
		
		return '<p>Correo enviado correctamente </p>' . '<p>| empleados ' . count($agrupados['empleado']) . '</p><p>' . implode(', ', $agrupados['empleado']) . '</p>' . '<p>| administrativo ' . count($agrupados['administrativo']) . '</p><p>' . implode(', ', $agrupados['administrativo']) . '</p>' . '<p>| otros ' . count($agrupados['otros']) . '</p><p>' . implode(', ', $agrupados['otros']) . '</p>';
	}
	
	public function PersonasExtra() {
		$param = Parametro::all()->first()->HORAS_NECESARIAS_SEMANA;
		$now = Carbon::now('America/Bogota');
		
		if ($now->day <= 15) {
			// Primera quincena del mes actual
			$from = $now->copy()->startOfMonth();   
			$to = $now->copy()->day(15)->endOfDay();
		}
		else {
			// Segunda quincena del mes actual
			$from = $now->copy()->day(16)->startOfDay();
			$to = $now->copy()->endOfMonth()->endOfDay();
		}
		$fechaLabel = $from->locale('es')->isoFormat('D [de] MMMM [de] YYYY')
           . ' - '
           . $to->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
		
		$usuarios = Reporte::select([
			                            'users.name as usuario',
			                            'reportes.user_id',
			                            DB::raw('WEEK(reportes.fecha_ini, 1) as semana_iso'),
			                            DB::raw('SUM(reportes.horas_trabajadas) as total_horas')
		                            ])->join('users', 'users.id', '=', 'reportes.user_id')->whereBetween('reportes.fecha_ini', [
			'2025-10-01 00:00:00',
			'2025-10-15 23:59:59'
		])->whereNull('reportes.deleted_at')
		  ->groupBy('reportes.user_id', 'users.name', DB::raw('WEEK(reportes.fecha_ini, 1)'))
		  ->having('total_horas', '>', $param)
		  ->orderBy('total_horas')
		  ->orderBy('users.name')
		  ->orderBy(DB::raw('semana_iso'))
		  ->get();
		
        if (app()->environment('test')) {
			$destinos = ['alejofg2@gmail.com'];
        }else{
			$destinos = [
				'ajelof2@gmail.com',
				'perezmezajessica@gmail.com'
			];
        }
		Mail::to($destinos)->send(new UsuariosHorasExtrasMail($usuarios, $fechaLabel,$param));
		
		return '
		<p> Correo enviado a </p>
		<p> ' . implode(',',$destinos) . '</p>';
		
	}
	
	
	
}
