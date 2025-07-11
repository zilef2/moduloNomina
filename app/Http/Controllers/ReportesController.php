<?php

namespace App\Http\Controllers;

use App\helpers\CalculoReportes;
use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\Http\Requests\ReporteRequest;
use App\Models\CentroCosto;
use App\Models\Parametro;
use App\Models\Reporte;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Spatie\LaravelPdf\Facades\Pdf;

class ReportesController extends Controller {
	
	//<editor-fold desc="Select y filtros">
	public function index(Request $request) {
		$numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' |reportes index| '));
		$Authuser = Myhelp::AuthU();
		Carbon::setLocale('es');
		$titulo = __('app.label.Reportes');
		
		$Reportes = Reporte::query();
		$showSelect = $this->losSelect($valoresSelectConsulta, $showUsers, $valoresSelect, $userFiltro, $numberPermissions);
		$IntegerDefectoSelect = $valoresSelectConsulta->first()->id;
		
		$startDate = Carbon::now()->startOfWeek();
		$endDate = Carbon::now()->endOfWeek();
		$startDateMostrar = $startDate->isoFormat('dddd D [de] MMMM');
		$endDateMostrar = $endDate->isoFormat('dddd D [de] MMMM');
		
		$perPage = $request->has('perPage') ? $request->perPage : 50;
		if(!$request->has('perPage') && $numberPermissions < 2) {
			$perPage = 5; //si es empleado
		}
		
		//  aqui se filtra
		$fnombresT = $this->fNombresTabla($numberPermissions, $Reportes, $Authuser, $request, $titulo);
		
		$nombresTabla = $fnombresT[0];
		$horasemana = $fnombresT[1];
		$quincena = $fnombresT[2];
		$horasPersonal = $fnombresT[3];
		
		//para mostrar la suma de las horas extras
		$ReportesMes = $this->ReportesFunQuicena($numberPermissions, $Authuser);
		$sumhoras_trabajadas = $ReportesMes->sum('horas_trabajadas');
		$sumdiurnas = $ReportesMes->sum('diurnas');
		$sumnocturnas = $ReportesMes->sum('nocturnas');
		$sumextra_diurnas = $ReportesMes->sum('extra_diurnas');
		$sumextra_nocturnas = $ReportesMes->sum('extra_nocturnas');
		$sumdominical_diurno = $ReportesMes->sum('dominical_diurno');
		$sumdominical_nocturno = $ReportesMes->sum('dominical_nocturno');
		$sumdominical_extra_diurno = $ReportesMes->sum('dominical_extra_diurno');
		$sumdominical_extra_nocturno = $ReportesMes->sum('dominical_extra_nocturno');
		
		//5abril2024
		//suma de horas ordinarias[0] &
		$ArrayOrdinarias = Myhelp::CalcularPendientesQuicena($Authuser);
		
		//help: $HorasDeCadaSemana en el [0] esta la semana actual, y de resto son numSemana => SumaHorasOrdinarias
		$HorasDeCadaSemana = CalculoReportes::CalcularHorasDeCadaSemana($Authuser->id);
		
		//20ene2024
		$esteMes = Carbon::now()->format('m');
		$esteAnio = Carbon::now()->format('Y');
		$horasTrabajadasHoy = Reporte::WhereMonth('fecha_ini', $esteMes)->WhereYear('fecha_ini', $esteAnio)->Where('user_id', $Authuser->id)->pluck('horas_trabajadas', 'fecha_ini')->toArray();
		//        dd($horasTrabajadasHoy);
		foreach ($horasTrabajadasHoy as $index => $item) {
			$elIndex = (int)Carbon::parse($index)->format('d');
			if (isset($horasTrabajadasHoy2[$elIndex])) {
				$horasTrabajadasHoy2[$elIndex] += $item;
			}
			else {
				$horasTrabajadasHoy2[$elIndex] = $item;
			}
		}
		
		
		return Inertia::render('Reportes/Index', [ //carpeta
			'title'          => $titulo,
			'filters'        => $request->all(['search', 'field', 'order', 'soloValidos', 'FiltroUser', 'searchDDay', 'searchorasD', 'soloQuincena', 'searchIncongruencias', 'searchQuincena', 'FiltroQuincenita', 'search1', 'search1', 'HorasNoDiurnas',]),
			'perPage'        => (int)$perPage,
			'fromController' => $Reportes->paginate($perPage),
			'breadcrumbs'    => [['label' => __('app.label.Reportes'), 'href'  => route('Reportes.index')]],
			'nombresTabla'   => $nombresTabla,
			
			'valoresSelect'        => $valoresSelect,
			'showSelect'           => $showSelect,
			'IntegerDefectoSelect' => $IntegerDefectoSelect,
			'showUsers'            => $showUsers,
			'quincena'             => $quincena,
			'horasemana'           => $horasemana,
			'horasPersonal'        => $horasPersonal,
			'startDateMostrar'     => $startDateMostrar,
			'endDateMostrar'       => $endDateMostrar,
			'numberPermissions'    => $numberPermissions,
			'ArrayOrdinarias'      => $ArrayOrdinarias,
			'userFiltro'           => $userFiltro,
			
			'sumhoras_trabajadas'         => (int)($sumhoras_trabajadas),
			'sumdiurnas'                  => $sumdiurnas,
			'sumnocturnas'                => $sumnocturnas,
			'sumextra_diurnas'            => $sumextra_diurnas,
			'sumextra_nocturnas'          => $sumextra_nocturnas,
			'sumdominical_diurno'         => $sumdominical_diurno,
			'sumdominical_nocturno'       => $sumdominical_nocturno,
			'sumdominical_extra_diurno'   => $sumdominical_extra_diurno,
			'sumdominical_extra_nocturno' => $sumdominical_extra_nocturno,
			'horasTrabajadasHoy'          => $horasTrabajadasHoy2 ?? [],
			'HorasDeCadaSemana'           => $HorasDeCadaSemana,
			'ArrayHorasSemanales'         => $this->DoArrayHorasSemanales(),
			'ArrayCentrosNoFactura'       => $this->DoArrayCentrosNoFactura(),
			'losSelect'                   => $this->DolosSelect(),
		
		]);
	}
	
	public function losSelect(&$valoresSelectConsulta, &$showUsers, &$valoresSelect, &$userFiltro, $numberPermissions): array {
		$elUser = Myhelp::AuthU();
		$showSelect = [];
		
		$DateEstandar = Carbon::now()->subYears(2);
		
		$valoresSelectConsulta = CentroCosto::Where('activo', 1)->WhereHas('reportes', function ($query) use ($DateEstandar) {
			$query
				->orderBy('created_at', 'asc')->take(1)->where('created_at', '<=', $DateEstandar)
			;
		})->orWhere('created_at', '>=', $DateEstandar)->orWhere('updated_at', '>=', $DateEstandar)->orderBy(DB::raw('CAST(nombre AS UNSIGNED) IS NULL, CAST(nombre AS UNSIGNED), nombre'))->get();
		
		//        $valoresSelectConsulta = CentroCosto::Where('activo', 1)
		//            ->Where('updated_at', '>=', Carbon::now()->addMonths(-8)->toDateString()) //todo: subir un parametro que controle esto
		//            ->orderBy('nombre')->get();
		$userFiltro[0] = ['label' => 'Sin empleado', 'value' => 0];
		foreach ($valoresSelectConsulta as $value) {
			$valoresSelect[] = ['label' => $value->nombre . ' - ' . $value->Zouna, //centro de costos
			                    'value' => (int)($value->id),
			];
			$showSelect[(int)($value->id)] = $value->nombre;
		}
		$usuariosSelectConsulta = User::orderBy('name')->get();
		foreach ($usuariosSelectConsulta as $value) {
			$showUsers[(int)($value->id)] = $value->name;
		}
		
		if ($numberPermissions === 3) { //
			$centrosSupervisor = $elUser->ArrayCentrosID();
			$userEmpleados = User::UsersWithManyRols(['empleado', 'supervisor'])->orderBy('name')->get();
			$userEmpleados = $userEmpleados->map(function ($user) use ($centrosSupervisor) {
				$tieneReportesEnElCentro = $user->reportes()->WhereIn('centro_costo_id', $centrosSupervisor)->count();
				
				
				return $tieneReportesEnElCentro > 0 ? $user : false;
			})->filter();
		}
		else {
			$userEmpleados = User::UsersWithRol('empleado')->orderBy('name')->get();
		}
		foreach ($userEmpleados as $value) {
			$userFiltro[] = ['label' => $value->name,
			                 'value' => (int)($value->id),
			];
		}
		
		
		return $showSelect;
	}
	
	//</editor-fold>
	
	public function fNombresTabla($numberPermissions, &$Reportes, $Authuser, $request, &$titulo) {
		$startDate = Carbon::now()->startOfWeek();
		$endDate = Carbon::now()->endOfWeek();
		$quincena = [];
		$horasPersonal = 0;
		
		if ($request->has(['field', 'order'])) {
			$Reportes->orderBy($request->field, $request->order);
		}
		else {
			$Reportes->orderBy('valido')->orderByDesc('fecha_ini');
		}
		
		$horasemana = Reporte::Where('user_id', $Authuser->id)->WhereBetween('fecha_ini', [$startDate,
			$endDate
		])->sum('horas_trabajadas');
		if ($numberPermissions === 1) { //1 = empleado | 2 administrativo | 3 supervisor | 4 ingeniero
			$Reportes->whereUser_id($Authuser->id);
			
			$nombresTabla = [//0: como se ven //1 como es la BD
				['Acciones',
					'#',
					'Centro costo',
					'valido',
					'inicio',
					'fin',
					'horas trabajadas',
					'🍗',
					'diurnas',
					'nocturnas',
					'extra diurnas',
					'extra nocturnas',
					'dominical diurno',
					'dominical nocturno',
					'dominical extra diurno',
					'dominical extra nocturno',
					'observaciones'
				],
				['b_valido',
					't_fecha_ini',
					't_fecha_fin',
					'i_horas_trabajadas',
					'v_almuerzo',
					'i_diurnas',
					'i_nocturnas',
					'i_extra_diurnas',
					'i_extra_nocturnas',
					'i_dominical_diurno',
					'i_dominical_nocturno',
					'i_dominical_extra_diurno',
					'i_dominical_extra_nocturno',
					's_observaciones'
				], //m for money || t for datetime || d date || i for integer || s string || b boolean
				[null,
					null,
					null,
					null,
					'b_valido',
					't_fecha_ini',
					't_fecha_fin',
					'i_horas_trabajadas',
					'v_almuerzo',
					'i_diurnas',
					'i_nocturnas',
					'i_extra_diurnas',
					'i_extra_nocturnas',
					'i_dominical_diurno',
					'i_dominical_nocturno',
					'i_dominical_extra_diurno',
					'i_dominical_extra_nocturno',
					's_observaciones'
				], //m for money || t for datetime || d date || i for integer || s string || b boolean
			];
			
			//# solo validos 0 aun no | 1 valido | 2 rechazado
			if ($request->has('soloValidos')) {
				$Reportes->where('valido', 1);
			}
			if ($request->has('soloQuincena')) {
				$hoyNow = Carbon::now();
				$hoyDia = clone $hoyNow;
				$hoyDia = $hoyDia->day;
				$primerDia = clone $hoyNow;
				$primerDia = $primerDia->startOfMonth();
				
				$elquince = clone $hoyNow;
				$elquince = $elquince->startOfMonth()->addDays(14);
				
				if ($hoyDia <= 15) {
					$Reportes->whereBetween('fecha_ini', [$primerDia, $elquince]);
				}
				else {
					$Reportes->whereBetween('fecha_ini', [$elquince, Carbon::now()->endOfMonth()]);
				}
			}
			
		}
		else { //administrativo, supervisor y admin
			$this->Filtros($request, $Reportes);
			
			$nombresTabla = [//0: como se ven //1 como es la BD
				['Acciones',
					'#',
					'Centro costo',
					'Trabajador',
					'Valido',
					'Inicio',
					'Fin',
					'Horas trabajadas',
					'🍗',
					'diurnas',
					'nocturnas',
					'extra diurnas',
					'extra nocturnas',
					'dominical diurno',
					'dominical nocturno',
					'dominical extra diurno',
					'dominical extra nocturno',
					'observaciones'
				],
				['b_valido',
					't_fecha_ini',
					't_fecha_fin',
					'i_horas_trabajadas',
					'v_almuerzo',
					'i_diurnas',
					'i_nocturnas',
					'i_extra_diurnas',
					'i_extra_nocturnas',
					'i_dominical_diurno',
					'i_dominical_nocturno',
					'i_dominical_extra_diurno',
					'i_dominical_extra_nocturno',
					's_observaciones'
				], //m for money || t for datetime || d date || i for integer || s string || b boolean
				[null,
					null,
					null,
					null,
					'b_valido',
					't_fecha_ini',
					't_fecha_fin',
					'i_horas_trabajadas',
					'v_almuerzo',
					'i_diurnas',
					'i_nocturnas',
					'i_extra_diurnas',
					'i_extra_nocturnas',
					'i_dominical_diurno',
					'i_dominical_nocturno',
					'i_dominical_extra_diurno',
					'i_dominical_extra_nocturno',
					's_observaciones'
				], //m for money || t for datetime || d date || i for integer || s string || b boolean
			];
			
			$quincena = ['Primera quincena' => Reporte::whereBetween('fecha_ini', [Carbon::now()->startOfMonth(),
				Carbon::now()->startOfMonth()->addDays(14)
			])->count(),
			             'Segunda quincena' => Reporte::whereBetween('fecha_ini', [Carbon::now()->startOfMonth()->addDays(15),
				             Carbon::now()->LastOfMonth()
			             ])->count(),
			];
			$horasPersonal = Reporte::WhereBetween('fecha_ini', [$startDate, $endDate])->sum('horas_trabajadas');
			
			if ($numberPermissions == 3) { //supervisor
				$ArrayCentrosUserID = $Authuser->ArrayCentrosID();
				$horasPersonal = Reporte::WhereBetween('fecha_ini', [$startDate,
					$endDate
				])->WhereIn('centro_costo_id', $ArrayCentrosUserID)->sum('horas_trabajadas');
				
				$Reportes->WhereIn('centro_costo_id', $ArrayCentrosUserID);
				$quincena = ['Primera quincena' => Reporte::whereBetween('fecha_ini', [Carbon::now()->startOfMonth(),
					Carbon::now()->startOfMonth()->addDays(14)
				])->Where('centro_costo_id', $Authuser->centro_costo_id)->count(),
				             'Segunda quincena' => Reporte::whereBetween('fecha_ini', [Carbon::now()->startOfMonth()->addDays(15),
					             Carbon::now()->LastOfMonth()
				             ])->Where('centro_costo_id', $Authuser->centro_costo_id)->count(),
				];
				
			}
			else { //admin y administrativo
				if ($numberPermissions == 2) {//administrativo
					$horasPersonal = Reporte::WhereBetween('fecha_ini', [$startDate, $endDate])->sum('horas_trabajadas');
					
					//                    $Reportes->where('centro_costo_id', $Authuser->centro_costo_id);
				}
			}
			$titulo = 'Mis Horas (esta quincena): ' . $horasPersonal;
		}
		
		
		return [$nombresTabla, $horasemana, $quincena, $horasPersonal];
	}
	
	//  aqui se filtra tambien
	
	public function Filtros(&$request, &$Reportes): void {
		$esteAnio = Carbon::now()->format('Y');
		
		if ($request->has('search5')) { //el anio
			$Reportes->WhereHas('centro',function ($query) use ($request) {
			    return $query->Where('id', $request->search5['id']);
			});
		}
		
		if ($request->has('search4')) { //el anio
			$Reportes->WhereYear('fecha_ini', $request->search4);
		}
		else {
			$Reportes->WhereYear('fecha_ini', $esteAnio);
		}
		if ($request->has('search')) { //el mes
			$months = ['',
				'Enero',
				'Febrero',
				'Marzo',
				'Abril',
				'Mayo',
				'Junio',
				'Julio',
				'Agosto',
				'Septiembre',
				'Octubre',
				'Noviembre',
				'Diciembre'
			];
			if (is_integer($request->search)) {
				$Reportes->whereMonth('fecha_ini', $request->search);
				$request->search = $months[$request->search];
			}
			else {
				$mesMinusculas = ucfirst($request->search);
				$Reportes->whereMonth('fecha_ini', array_search($mesMinusculas, $months));
			}
		}
		if ($request->has('searchDDay')) {
			$Reportes->whereDay('fecha_ini', $request->searchDDay);
		}
		if ($request->has('searchorasD')) {
			$Reportes->where('diurnas', $request->searchorasD);
		}
		if ($request->has('HorasNoDiurnas')) {
			$Reportes->where('diurnas', '!=', $request->HorasNoDiurnas);
		}
		if ($request->has('searchIncongruencias')) {
			$Reportes->whereRaw('diurnas + nocturnas + extra_diurnas + extra_nocturnas + dominical_diurno + dominical_nocturno + dominical_extra_diurno + dominical_extra_nocturno != horas_trabajadas');
		}
		if ($request->has('searchQuincena')) { //2
			$Reportes->whereDay('fecha_ini', '>', $request->searchQuincena);
		}
		
		//20jun2024
		if ($request->has(['FiltroUser']) && $request->FiltroUser != 0) {
			$Reportes->Where('user_id', $request->FiltroUser);
		}
		if ($request->has(['FiltroQuincenita']) && $request->FiltroQuincenita != 0) {
			$hoyNow = Carbon::now();
			$elquince = clone $hoyNow;
			$elquince = $elquince->startOfMonth()->addDays(14);
			if ($request->FiltroQuincenita['value'] === '1') {
				$Reportes->whereDay('fecha_ini', '<=', 15);
			}
			else {
				$Reportes->whereDay('fecha_ini', '>', 15);
			}
		}
		if ($request->has(['search1']) && $request->has(['search2']) && $request->search1 != 0) {
			$h1 = $request->search1;
			$h2 = $request->search2;
			$Reportes->whereDay('fecha_ini', '>=', $h1);
			$Reportes->whereDay('fecha_fin', '<=', $h2);
		}
		//$request->has('search') es el mes
		if ($request->has('searchSiigo')) {
			$Reportes->where(function ($query) {
				$query
					->Where('extra_diurnas', '<>', 0)->orWhere('nocturnas', '<>', 0)->orWhere('extra_nocturnas', '<>', 0)->orWhere('dominical_diurno', '<>', 0)->orWhere('dominical_nocturno', '<>', 0)->orWhere('dominical_extra_diurno', '<>', 0)->orWhere('dominical_extra_nocturno', '<>', 0)
				;
			});
		}
		if ($request->has('search3')) {
			$campos = ['horas_trabajadas',
				'almuerzo',
				'diurnas',
				'nocturnas',
				'extra_diurnas',
				'extra_nocturnas',
				'dominical_diurno',
				'dominical_nocturno',
				'dominical_extra_diurno',
				'dominical_extra_nocturno'
			];
			
			$Reportes->Where(function ($query) use ($campos) {
				foreach ($campos as $campo) {
					$query->orWhere($campo, '<', 0);
				}
			});
		}
		
		//# solo validos //0 aun no | 1 valido | 2 rechazado
		if ($request->has('soloValidos')) {
			$Reportes->where('valido', 1);
		}
		if ($request->has('soloValidos2')) {
			$Reportes->where('valido', 2);
		}
		
		//30abril 11am
		if ($request->has('soloQuincena')) {
			$hoyNow = Carbon::now();
			$hoyDia = clone $hoyNow;
			$hoyDia = $hoyDia->day;
			$primerDia = clone $hoyNow;
			$primerDia = $primerDia->startOfMonth();
			$elquince = clone $hoyNow;
			$elquince = $elquince->startOfMonth()->addDays(14);
			
			if ($hoyDia <= 15) {
				$Reportes->whereBetween('fecha_ini', [$primerDia, $elquince]);
			}
			else {
				$Reportes->whereBetween('fecha_ini', [$elquince, Carbon::now()->endOfMonth()]);
			}
		}
		
		//end of filters, now order
		if ($request->has(['field', 'order'])) {
			$Reportes->orderBy($request->field, $request->order);
		}
		else {
			$Reportes->orderByDesc('fecha_ini');
		}
	}
	
	private function ReportesFunQuicena($numberPermissions, $Authuser) {
		$esteQuicena_ini = Carbon::now()->firstOfMonth();
		$esteQuicena_fin = Carbon::now()->lastOfMonth();
		if ($numberPermissions < 2) {
			$ReportesQuicenaActual = Reporte::Where('user_id', $Authuser->id)->WhereBetween('fecha_ini', [$esteQuicena_ini,
				$esteQuicena_fin
			]);
		}
		else { //si es empleado
			$ReportesQuicenaActual = Reporte::WhereBetween('fecha_ini', [$esteQuicena_ini, $esteQuicena_fin]);
		}
		
		
		return $ReportesQuicenaActual;
	}
	
	private function DoArrayHorasSemanales() {
		$parametros = Parametro::first();
		if ($parametros) {
			return ['HORAS_ORDINARIAS'       => $parametros['HORAS_ORDINARIAS'],
			        'MAXIMO_HORAS_SEMANALES' => $parametros['HORAS_NECESARIAS_SEMANA'],
			        's_Dias_gabela'          => $parametros['s_Dias_gabela'],
			];
		}
		
		
		return [0, 0];
	}//fin index
	
	public function DoArrayCentrosNoFactura() {
		$centros = CentroCosto::
		OrWhere('ValidoParaFacturar', 0)->OrWhere('ValidoParaFacturar')->pluck('id')->toArray();
		
		
		return $centros;
	}
	
	private function DolosSelect() {
		$centroSelect = CentroCosto::select('id', 'nombre as name')
			->WhereHas('reportes')
			->get()->toArray();
		array_unshift($centroSelect, ["name" => "Seleccione un centro de costo",
		                              'id'   => 0
		]);
		
		
		return ['centros' => $centroSelect];
	}
	
	public function create() {}
	
	//fin store
	
	public function store(ReporteRequest $request): \Illuminate\Http\RedirectResponse {
		$numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' |reportes store| ')); //0:error, 1:estudiante,  2: profesor, 3:++ )
		DB::beginTransaction();
		$authUser = Myhelp::AuthU();
		try {
			$fecha_ini = $this->updatingDate($request->fecha_ini);
			$fecha_fin = $this->updatingDate($request->fecha_fin);
			if ($numberPermissions > 9) {
				$mensajeError = '';
			}
			else {
				$mensajeError = $this->NoEsValidoPorFecha($fecha_ini, $fecha_fin);
			}
			
			if ($mensajeError !== '') {
				DB::rollBack();
				myhelp::EscribirEnLog($this, 'Se intento reportar en desorden U:' . $authUser->id, 'Usuario desordenado: ' . $authUser->name);
				
				
				return back()->with('error', $mensajeError);
			}
			
			$Reportes = new Reporte;
			$Reportes->fecha_ini = $fecha_ini;
			$Reportes->fecha_fin = $fecha_fin;
			$Reportes->horas_trabajadas = $request->horas_trabajadas;
			
			$Reportes->almuerzo = $request->almuerzo;
			$Reportes->diurnas = $request->diurnas;
			$Reportes->nocturnas = $request->nocturnas;
			$Reportes->extra_diurnas = $request->extra_diurnas;
			$Reportes->extra_nocturnas = $request->extra_nocturnas;
			
			$Reportes->dominical_diurno = $request->dominical_diurnas;
			$Reportes->dominical_nocturno = $request->dominical_nocturnas;
			$Reportes->dominical_extra_diurno = $request->dominical_extra_diurnas;
			$Reportes->dominical_extra_nocturno = $request->dominical_extra_nocturnas;
			
			$Reportes->valido = 0;
			$Reportes->observaciones = $request->observaciones;
			$Reportes->centro_costo_id = $request->centro_costo_id['value'];
			$Reportes->user_id = Auth::user()->id;
			
			$Reportes->save();
			DB::commit();
			
			
			return back()->with('success', __('app.label.created_successfully', ['name' => 'Reporte']));
			
		} catch (\Throwable $th) {
			DB::rollback();
			myhelp::EscribirEnLog($this, 'reportes fallo store', $th->getMessage(), 0, 1);
			
			
			return back()->with('error', __('app.label.created_error', ['name' => __('app.label.Reportes')]) . $th->getMessage());
		}
	}
	
	public function updatingDate($date) {
		if ($date === null || $date == '1969-12-31') {
			return null;
		}
		
		
		return date('Y-m-d H:i:s', strtotime($date));
	}
	
	private function NoEsValidoPorFecha($fecha_ini, $fecha_fin): string {
		//info: reporte futuro
		$manana = Carbon::tomorrow()->startOfDay();
		$fechain = Carbon::parse($fecha_ini);
		if ($fechain->greaterThanOrEqualTo($manana)) {
			return 'Reporte futuro';
		}
		
		//no solapes
		$thisUserId = Myhelp::AuthUid();
		$fecha_ini2 = Carbon::parse($fecha_ini)->addMinute()->format('Y-m-d H:i:s');
		$fecha_fin2 = Carbon::parse($fecha_fin)->addMinutes(- 1)->format('Y-m-d H:i:s');
		
		$traslapa = Reporte::where('user_id', $thisUserId)->where(function ($query) use ($fecha_ini, $fecha_ini2, $fecha_fin, $fecha_fin2) {
			$query
				->whereBetween('fecha_ini', [$fecha_ini, $fecha_fin2])->orWhereBetween('fecha_fin', [$fecha_ini2, $fecha_fin])
			;
		})->exists();
		
		if ($traslapa) {
			return 'Las fechas se solapan';
		}
		
		//no reportar primero en la noche y luego en el dia
		$fechafi = Carbon::parse($fecha_fin);
		$MismoDia = Reporte::Where('user_id', $thisUserId)->WhereDate('fecha_ini', $fechafi)->WhereTime('fecha_ini', '>=', $fechafi) // Posterior en tiempo
		                   ->exists()
		;
		if ($MismoDia) {
			return 'Los reporten deben ser en orden';
		}
		
		
		return '';
	}
	
	public function show($id) {}
	
	public function edit($id) {
		$numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' |reportes edit| ')); //0:error, 1:estudiante,  2: profesor, 3:++ )
		
		$Reportes = Reporte::findOrFail($id);
		
		
		return Inertia::render('Reportes.edit', ['Reportes' => $Reportes]);
	}
	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param ReportesRequest $request
	 * @param int $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function update(Request $request, $id) {
		
		$numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' |reportes update| ')); //0:error, 1:estudiante,  2: profesor, 3:++ )
		$theuserAprobo = Myhelp::AuthU();
		DB::beginTransaction();
		try {
			$Reportes = Reporte::findOrFail($id);
			if ($request->valido === true) {
				$quehizo = ' aprobó.';
			}
			if ($request->valido === 2) {
				$quehizo = ' rechazó.';
			}
			
			$Reportes->name_aprobo = $theuserAprobo->name . $quehizo;
			$Reportes->id_aprobo = $theuserAprobo->id;
			
			if ($request->valido === 1 || $request->valido === true) {
				$Reportes->valido = $request->valido; //0 creado //1 aceptado //2 rechazado // 3  alterado 
				
			}
			else {
				$Reportes->fecha_ini = $this->updatingDate($request->fecha_ini);
				$Reportes->fecha_fin = $this->updatingDate($request->fecha_fin);
				
				$Reportes->horas_trabajadas = $request->horas_trabajadas;
				
				$Reportes->diurnas = $request->diurnas;
				$Reportes->nocturnas = $request->nocturnas;
				$Reportes->extra_diurnas = $request->extra_diurnas;
				$Reportes->extra_nocturnas = $request->extra_nocturnas;
				$Reportes->dominical_diurno = $request->dominical_diurno;
				$Reportes->dominical_nocturno = $request->dominical_nocturno;
				$Reportes->dominical_extra_diurno = $request->dominical_extra_diurno;
				$Reportes->dominical_extra_nocturno = $request->dominical_extra_nocturno;
				
				$Reportes->valido = $request->valido; //0 creado //1 aceptado //2 rechazado // 3  alterado 
				$Reportes->observaciones = $request->observaciones;
				$Reportes->centro_costo_id = $request->centro_costo_id;
			}
			$Reportes->save();
			DB::commit();
			
			
			return back()->with('success', __('app.label.updated_successfully', ['name' => 'Reporte']));
		} catch (\Throwable $th) {
			DB::rollback();
			myhelp::EscribirEnLog($this, 'reportes update', $th->getMessage(), 0, 1);
			
			
			return back()->with('error', __('app.label.updated_error', ['name' => __('app.label.Reportes')]) . $th->getMessage());
		}
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function destroy($id) {
		$numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' |reportes destroy| ')); //0:error, 1:estudiante,  2: profesor, 3:++ )
		DB::beginTransaction();
		
		try {
			// si se la rechazaron, tendra que hacer uno nuevo
			$Reportes = Reporte::findOrFail($id);
			if ($numberPermissions > 8) {
				$Reportes->delete();
				DB::commit();
				
				
				return back()->with('success', __('app.label.deleted_successfully', ['name' => 'Reporte']));
			}
			if ($Reportes->valido == 0 || $Reportes->valido == 2) {
				$Reportes->delete();
				DB::commit();
				
				
				return back()->with('success', __('app.label.deleted_successfully', ['name' => 'Reporte']));
			}
			DB::commit();
			
			
			return back()->with('warning', __('app.label.not_deleted', ['name' => 'Reporte']) . '. Ya esta aprobado');
			
		} catch (\Throwable $th) {
			DB::rollback();
			myhelp::EscribirEnLog($this, 'reportes destroy', $th->getMessage(), 0, 1);
			
			
			return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.Reportes')]) . $th->getMessage());
		}
	}
	
	public function MassiveReportes(Request $request) {
		$numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' |reportes MassiveReportes| ')); //0:error, 1:estudiante,  2: profesor, 3:++ )
		
		DB::beginTransaction();
		try {
			//            $thisUserId = myhelp::AuthU()->id;
			$fecha_ini = Carbon::parse($request->fecha_ini[0], 'America/Bogota')->setHour(7);
			$fecha_fin = Carbon::parse($request->fecha_ini[1], 'America/Bogota')->setHour(16);
			$diff = $fecha_ini->diffinDays($fecha_fin) + 1;
			$valorQuemado_HorasTrabajadas = 8;
			
			if ($request->user_id) {
				$userExiste = User::find($request->user_id);
				
				if ($userExiste) {
					$UltimoUser = $request->user_id;
				}
				else {
					return back()->with('error', 'Persona inexistente');
				}
				
			}
			else {
				$UltimoUser = Myhelp::AuthU()->id;
			}
			for ($i = 0; $i < $diff; $i ++) {
				$Reportes = new Reporte;
				$fecha_in2 = clone $fecha_ini;
				$Reportes->fecha_ini = $fecha_in2;
				$fecha_fin = clone $fecha_ini;
				$fecha_fin->addHours($valorQuemado_HorasTrabajadas);
				if ($fecha_ini->isSunday()) {
					$fecha_ini->addDays();
					
					continue;
				}
				$fecha_ini->addDays();
				$Reportes->fecha_fin = $fecha_fin;
				
				$Reportes->horas_trabajadas = $valorQuemado_HorasTrabajadas;
				
				$Reportes->almuerzo = 1;
				$Reportes->diurnas = $valorQuemado_HorasTrabajadas;
				$Reportes->nocturnas = 0;
				$Reportes->extra_diurnas = 0;
				$Reportes->extra_nocturnas = 0;
				$Reportes->dominical_diurno = 0;
				$Reportes->dominical_nocturno = 0;
				$Reportes->dominical_extra_diurno = 0;
				$Reportes->dominical_extra_nocturno = 0;
				
				$Reportes->valido = 1;
				$Reportes->observaciones = $request->observaciones;
				$Reportes->centro_costo_id = $request->centro_costo_id;
				
				$Reportes->user_id = $UltimoUser;
				$Reportes->save();
			}
			
			DB::commit();
			
			
			return back()->with('success', __('app.label.created_successfully', ['name' => $Reportes->nombre]));
			
		} catch (\Throwable $th) {
			DB::rollback();
			$mensajeErrorTH = $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile();
			myhelp::EscribirEnLog($this, 'MassiveReportes', $mensajeErrorTH, 0, 1);
			
			
			return back()->with('error', __('app.label.created_error', ['name' => __('app.label.Reportes')]) . $mensajeErrorTH);
		}
	}
	
	public function destroyBulk(Request $request) {
		try {
			$reportes = Reporte::whereIn('id', $request->id);
			$reportes->delete();
			
			
			return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.reporte')]));
		} catch (\Throwable $th) {
			myhelp::EscribirEnLog($this, 'reportes destroybulk', $th->getMessage(), 0, 1);
			
			
			return back()->with('error', __('app.label.deleted_error', ['name' => count($request->id) . ' ' . __('app.label.reporte')]) . $th->getMessage());
		}
	}
	
	public function Reporte_Super_Edit(Request $request, $id) {
		$numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' | reportes Reporte_Super_Edit | ')); //0:error, 1:estudiante,  2: profesor, 3:++ )
		
		DB::beginTransaction();
		try {
			$Reporte = Reporte::findOrFail($id);
			
			$Reporte->horas_trabajadas = $request->horas_trabajadas;
			$Reporte->almuerzo = $request->almuerzo;
			$Reporte->diurnas = $request->diurnas;
			$Reporte->nocturnas = $request->nocturnas;
			$Reporte->extra_diurnas = $request->extra_diurnas;
			$Reporte->extra_nocturnas = $request->extra_nocturnas;
			
			$Reporte->dominical_diurno = $request->dominical_diurnas;
			$Reporte->dominical_nocturno = $request->dominical_nocturnas;
			$Reporte->dominical_extra_diurno = $request->dominical_extra_diurnas;
			$Reporte->dominical_extra_nocturno = $request->dominical_extra_nocturnas;
			
			//            $Reporte->valido = $request->valido;//0 creado //1 aceptado //2 rechazado // 3  alterado 
			//            $Reporte->observaciones = $request->observaciones;
			//            $Reporte->centro_costo_id = $request->centro_costo_id;
			$Reporte->save();
			DB::commit();
			
			
			return back()->with('success', __('app.label.updated_successfully', ['name' => 'Reporte']));
		} catch (\Throwable $th) {
			DB::rollback();
			myhelp::EscribirEnLog($this, 'reportes reporte super edit', $th->getMessage(), 0, 1);
			
			
			return back()->with('error', __('app.label.updated_error', ['name' => __('app.label.Reportes')]) . $th->getMessage());
		}
	}
	
	public function Reporte_Edit_Brus(Request $request, $id) {
		$numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' Reporte_Edit_Brus| ', 'ingreso al index')); //0:error, 1:estudiante,  2: profesor, 3:++ )
		$user = Myhelp::AuthU();
		if($numberPermissions < 2) {
			myhelp::EscribirEnLog($this, 'Reporte_Edit_Brus ', 'Esta persona No esta autorizado '.$user->name, 0, 1);
			return back()->with('error', 'No esta autorizado');
		}
		
		
		DB::beginTransaction();
		try {
			$Reporte = Reporte::findOrFail($id);
			
			$reporteUser = $Reporte->nameUser();
			if(!$this->validacionesBrusco($request)){
				myhelp::EscribirEnLog($this, 'Reporte_Edit_Brus|','reporte no valido '
				                           .' :: Persona logueada '.$user->name
				                           .' :: ID Persona logueada '.$user->id
				                           .' :: Persona del reporte '.$reporteUser
				                           .' :: Era un reporte valido = '.$Reporte->valido . ' || explicacion: 0 creado 1 aceptado 2 rechazado '
					, 0, 1);
				DB::rollback();
				return back()->with('error', 'Reporte no valido');
			}
			
			
			$Reporte->horas_trabajadas = $request->horas_trabajadas;
			$Reporte->almuerzo = $request->almuerzo;
			$Reporte->diurnas = $request->diurnas;
			$Reporte->nocturnas = $request->nocturnas;
			$Reporte->extra_diurnas = $request->extra_diurnas;
			$Reporte->extra_nocturnas = $request->extra_nocturnas;
			
			$Reporte->dominical_diurno = $request->dominical_diurnas;
			$Reporte->dominical_nocturno = $request->dominical_nocturnas;
			$Reporte->dominical_extra_diurno = $request->dominical_extra_diurnas;
			$Reporte->dominical_extra_nocturno = $request->dominical_extra_nocturnas;
			
            $Reporte->valido = 1 ;//0 creado //1 aceptado //2 rechazado
			$Reporte->observaciones = $Reporte->observaciones . ' Alterado por: ' . $user->name . ' el ' . Carbon::now()->format('Y-m-d') . ' -- ';
			
			$Reporte->save();
			DB::commit();
			myhelp::EscribirEnLog($this, 'Reporte_Edit_Brus ', ' Editado por: ' . $user->name 
				                        .' :: Persona del reporte '.$reporteUser
		                                . ' el ' . Carbon::now()->format('Y-m-d H:i:s')
				, 0, 1);
			
			
			return back()->with('success', __('app.label.updated_successfully', ['name' => 'Reporte']));
			
		} catch (\Throwable $th) {
			DB::rollback();
			$mensajeError = $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile();
			myhelp::EscribirEnLog($this, 'Reporte_Edit_Brus', $mensajeError, 0, 1);
	        if (app()->environment('test')) {
				dd(
				    $mensajeError
				);
	        }
			
			return back()->with('error', __('app.label.updated_error', ['name' => __('app.label.Reportes')]) . $th->getMessage());
		}
	}
	
	public function obtenerCentroCostosUltimaQuincena(): array {
		$now = Carbon::now();
		[$fechaInicio, $fechaFin] = $this->inicioYfinQuicena($now);
		$diasArestar = - 15;// establece si seran de 15 o 30 el paso del for
		$ValorArregloParaAnio = ($diasArestar * - 12 * 2) / 15;
		//si son 15, mostrará
		$nombreQuincena1 = $this->obtenerNombreQuincena($fechaInicio);
		$newnowArrray[0] = Carbon::now();
		
		for ($i = 0; $i < $ValorArregloParaAnio; $i ++) {
			$fechaFinArray[$i] = $fechaFin;
			$fechaInicioArray[$i] = $fechaInicio;
			$resultados = Reporte::whereBetween('fecha_ini', [$fechaInicio,
				$fechaFin
			])->join('centro_costos', 'reportes.centro_costo_id', '=', 'centro_costos.id') // Ajusta 'centro_costos' y las columnas si son diferentes
			                     ->selectRaw('centro_costos.nombre as nombre_centro_costo, COUNT(reportes.id) as conteo_reportes') // Ajusta 'centro_costos.nombre' si la columna es diferente
			                     ->groupBy('centro_costos.nombre') // Agrupa por el nombre del centro de costo
			                     ->orderBy('conteo_reportes', 'DESC')->get()->toArray()
			;
			$centroCostos[$i] = ['fechas'          => $fechaInicio->toDateString() . "--" . $fechaFin->toDateString(),
				// Formatea la fecha como string
				                 'Reportes'        => $resultados,
				                 'nombreQuincena1' => $nombreQuincena1,
			];
			
			$newnowArrray[$i + 1] = $newnowArrray[$i]->copy()->addDays($diasArestar);
			[$fechaInicio, $fechaFin] = $this->inicioYfinQuicena($newnowArrray[$i]);
			$nombreQuincena1 = $this->obtenerNombreQuincena($fechaInicio);
			
		}
		
		
		return [$centroCostos,
			//			$fechaFinArray,
			//			$fechaInicioArray,
			//			$newnowArrray,
		];
	}
	
	// Ejemplo de uso:
	
	private function inicioYfinQuicena(Carbon $thedate): array {
		$fechaConsulta = $thedate->copy(); // Creamos una copia para no modificar el original
		$primeraQuicena = $fechaConsulta->day > 15 ? 0 : 1;
		if ($primeraQuicena) {
			$fechaFin = $fechaConsulta->copy()->day(15); // Creamos una copia antes de modificar
			$fechaInicio = $fechaFin->copy()->day(1);    // Creamos una copia antes de modificar
		}
		else {
			$fechaInicio = $fechaConsulta->copy()->day(16);  // Creamos una copia antes de modificar (la segunda quincena empieza el 16)
			$fechaFin = $fechaInicio->copy()->lastOfMonth(); // Creamos una copia antes de modificar
		}
		
		
		return [$fechaInicio, $fechaFin];
	}
	
	function obtenerNombreQuincena(Carbon $fechaInicio): string {
		$mes = $fechaInicio->locale('es_CO')->monthName; // Obtiene el nombre del mes en español colombiano
		if ($fechaInicio->day === 1) {
			return "Quincena 1 del mes $mes";
		}
		else {
			return "Quincena 2 del mes $mes";
		}
	}
	
	function FuncionPruebas() {
		
		(new EnvioCorreos())->EnviaralGerenteProbando(1, 2000);
		//		>format('letter')
		//Legal: ->format('legal')
		//A3: ->format('a3')
		//A5: ->format('a5')
		//A6: ->format('a6')  ->landscape()
		
		dd(User::Where('name', 'like', '%Andrés Alejandro Velásquez Acosta%')->first(), User::Where('name', 'like', '%Santiago Sanchez Mesa%')->first(), User::Where('name', 'like', '%Vladimir Ruiz Morelos%')->first(), User::Where('name', 'like', '%Andrés Tomás Restrepo Garay%')->first(), User::Where('name', 'like', '%Juan Ignacio Duque Lopera%')->first(), User::Where('name', 'like', '%Romario Ahumada Tejera%')->first(), User::Where('name', 'like', '%Kevin Solano Ortiz%')->first());
		
	}
	
	public function EnviaralJefeProbando($cuantosViaticos, $total): string {
		$jefe = User::where('name', 'LIKE', '%Carlos Daniel Anaya Barrios%')->first();
		if ($jefe) {
			$cotizacion = \App\Models\cotizacion::all()->first(); // Asegúrate de tener la lógica correcta para obtener la cotización
			
			$pdfPath = storage_path('app/public/cotizacion.pdf');
			
			// Generar y guardar el PDF
			Pdf::view('pdf.cotizacion', ['cotizacion' => $cotizacion])->format('letter')->margins(20, 15, 10, 15)->save($pdfPath);
			
			// Leer el contenido del PDF para enviarlo en el correo
			$pdfData = file_get_contents($pdfPath);
			// Datos del correo
			$data = ['cuantosViaticos' => $cuantosViaticos,
			         'total'           => $total,
			         'solicitante'     => 'nombre de prueba1',
			];
			
			// Enviar el correo con el archivo adjunto
			
			if (\Illuminate\Support\Facades\App::environment('production')) {
				//				Mail::to($jefe->email)->send(new \App\Mail\NotificacionCotizacionConAdjunto($data, $pdfPath));
				Mail::to('ajelof2@gmail.com')->send(new \App\Mail\NotificacionCotizacionConAdjunto($data, $pdfData));
				$mensaje = "Correo enviado y Viatico ";
			}
			else {
				Mail::to('ajelof2@gmail.com')->send(new \App\Mail\NotificacionCotizacionConAdjunto($data, $pdfData));
				$mensaje = "Correo enviado solo a alejo ";
			}
		}
		else {
			$mensaje = "-";
			Myhelp::EscribirEnLog($this, ' ERROR: no se encontro al jefe');
		}
		
		
		return $mensaje;
	}
	
	function FuncionPruebas2() {
		$primerdia = Carbon::now()->addMonths(- 2)->startOfDay();
		$diafin = Carbon::now()->endOfMonth();
		$reportes = Reporte::whereBetween('fecha_ini', [$primerdia, $diafin])->WhereHas('centro', function ($query) {
			$query->WhereNull('zona_id');
		})->get()->pluck('centro_costo_id')->toArray();
		
		$centrosSinZona = CentroCosto::whereIn('id', $reportes)->get()->pluck('nombre')->toArray();
		dd($centrosSinZona);
	}
	
	/**
	 * @param \Illuminate\Http\Request $request
	 * @return void
	 */
	public function validacionesBrusco(Request $request): bool {
		if($request->almuerzo > 4) return false;
		
		$totalsinAlmuerzo = $request->horas_trabajadas;
		$sumahoras = (int)($request->diurnas) + (int)($request->nocturnas) + (int)($request->extra_diurnas) 
			+ (int)($request->extra_nocturnas) + (int)($request->dominical_diurnas) + (int)($request->dominical_nocturnas) 
			+ (int)($request->dominical_extra_diurnas) + (int)($request->dominical_extra_nocturnas);
		
		if($totalsinAlmuerzo != $sumahoras) {
			return false;
		}
		
		return true;
	}
	
}
