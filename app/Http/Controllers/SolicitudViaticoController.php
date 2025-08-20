<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\Models\solicitud_viatico;
use App\Http\Requests\Storesolicitud_viaticoRequest;
use App\Http\Requests\Updatesolicitud_viaticoRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\helpers\zzloggingcrud;
use App\Http\Requests\StoreviaticoRequest;
use App\Jobs\EnviarViaticoJob;
use App\Mail\MailViaticoGenerado;
use App\Models\CentroCosto;
use App\Models\consignarViatico;
use App\Models\User;
use App\Models\viatico;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Inertia\Response;

class SolicitudViaticoController extends Controller {
	
	public array $thisAtributos;
	public string $FromController = 'solicitud_viatico';
	
	//<editor-fold desc="Construc | filtro and dependencia">
	public function __construct() {
		//        $this->middleware('permission:create solicitud_viatico', ['only' => ['create', 'store']]);
		//        $this->middleware('permission:read solicitud_viatico', ['only' => ['index', 'show']]);
		//        $this->middleware('permission:update solicitud_viatico', ['only' => ['edit', 'update']]);
		//        $this->middleware('permission:delete solicitud_viatico', ['only' => ['destroy', 'destroyBulk']]);
		$this->thisAtributos = (new solicitud_viatico())->getFillable(); //not using
	}
	
	public function index(Request $request) {
		$numberPermissions = zzloggingcrud::zilefLogTrace();
		$solicitud_viaticos = $this->Filtros($request)->get();
		
		if ($request->has('search3')) {
			$solicitud_viaticos->filter(function ($item) {
				return $item->centrou == 'valor';
			});
		}
		
		$perPage = $request->has('perPage') ? $request->perPage : 10;
		
		return Inertia::render($this->FromController . '/Index', [
			'fromController'    => $this->PerPageAndPaginate($request, $solicitud_viaticos),
			'total'             => $solicitud_viaticos->count(),
			'breadcrumbs'       => [
				[
					'label' => __('app.label.' . $this->FromController),
					'href'  => route($this->FromController . '.index')
				]
			],
			'title'             => __('app.label.' . $this->FromController),
			'filters'           => $request->all(['search', 'field', 'order', 'search2', 'search3']),
			'perPage'           => (int)$perPage,
			'numberPermissions' => $numberPermissions,
			'losSelect'         => $this->Dependencias() ?? [],
			'totalsaldo'        => $this->totalsaldo() ?? 0,
			'totallegalizado'   => $this->totallegalizado() ?? 0,
		]);
	}
	
	public function Filtros($request): Builder {
		$solicitud_viaticos = solicitud_viatico::query();
		if ($request->has('search')) {
			$solicitud_viaticos = $solicitud_viaticos->where(function ($query) use ($request) {
				$query
					->where('Ciudad', 'LIKE', "%" . $request->search . "%")->orWhere('ObraServicio', 'LIKE', "%" . $request->search . "%")//                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
				;
			});
		}
		if ($request->has('search2')) {
			$solicitud_viaticos = $solicitud_viaticos->whereHas('user', function ($query) use ($request) {
				$query
					->where('name', 'LIKE', "%" . $request->search2 . "%")->orWhere('cedula', 'LIKE', "%" . $request->search2 . "%")
				;
			});
		}
		if ($request->has('search3') && $request->search3['id'] != 0) {
			$solicitud_viaticos = $solicitud_viaticos->whereHas('viaticos', function ($query) use ($request) {
				$query->where('centro_costo_id', 'LIKE', "%" . $request->search3['id'] . "%");
			});
		}
		
		if ($request->has(['field', 'order'])) {
			$solicitud_viaticos = $solicitud_viaticos->orderBy($request->field, $request->order);
		}
		else {
			$solicitud_viaticos = $solicitud_viaticos->orderByRaw('CASE WHEN saldo_sol != 0 THEN 0 ELSE 1 END ASC, created_at DESC');
		}
		
		return $solicitud_viaticos;
	}
	
	public function PerPageAndPaginate($request, $solicitud_viaticos) {
		$perPage = $request->has('perPage') ? $request->perPage : 10;
		$page = request('page', 1); // Current page number
		
		return new LengthAwarePaginator($solicitud_viaticos->forPage($page, $perPage), $solicitud_viaticos->count(), $perPage, $page, ['path' => request()->url()]);
	}
	
	public function Dependencias() {
		$Empleados = User::select('id', 'name')->whereHas('roles', function ($query) {
			return $query->WhereIn('name', [
				'supervisor',
				'administrativo',
				'ingeniero',
				'empleado'
			]);
		})->get()->toArray();
		$centroSelect = CentroCosto::all('id', 'nombre as name')->toArray();
		array_unshift($Empleados, [
			"name" => "Seleccione una persona",
			'id'   => 0
		]);
		array_unshift($centroSelect, [
			"name" => "Seleccione un centro de costo",
			'id'   => 0
		]);
		
		$zonas = \App\Models\zona::all('id', 'nombre as name')->toArray();
		array_unshift($zonas, [
			"name" => "Seleccione una zona",
			'id'   => 0
		]);
		$usercontroller = new UserController();
		
		return [
			$Empleados,
			$centroSelect,
			$zonas, //2
			$usercontroller->AutorizadosParaGenerarViaticos()
		];
	}
	
	private function totalsaldo(): int {
		return (int)viatico::all()->sum('saldo');
	}
	
	//    public function Dependencias() {
	//        $no_nadasSelect = No_nada::all('id','nombre as name')->toArray();
	//        array_unshift($no_nadasSelect,["name"=>"Seleccione un no_nada",'id'=>0]);
	
	//        $ejemploSelec = CentroCosto::all('id', 'nombre as name')->toArray();
	//        array_unshift($ejemploSelec, ["name" => "Seleccione un ejemploSelec", 'id' => 0]);
	//        return [$no_nadasSelect];
	//        return [$no_nadasSelect,$ejemploSelec];
	//    }
	
	//</editor-fold>
	
	private function totallegalizado(): int { return viatico::all()->sum('Totallegalizadou'); }
	
	public function store(Request $request): RedirectResponse {
		Myhelp::EscribirEnLog($this, ' Begin STORE:viaticos');
		DB::beginTransaction();
		$myuser = Myhelp::AuthU();
		$cuantosViaticos = count($request->descripcion);
		$elsolicitante = User::find($request->Solicitante['id']);
		$solViatico = solicitud_viatico::create([
			                                        'Solicitante'  => $elsolicitante->name,
			                                        'Fechasol'     => $request->Fechasol,
			                                        'Ciudad'       => $request->Ciudad,
			                                        'ObraServicio' => $request->ObraServicio,
			                                        'user_id'      => $elsolicitante->id,
			                                        'saldo_sol'    => 0,
		                                        ]);
		
		$total = 0;
		$paraellog = [];
		
		$IntCentroid = $request->centro_costo_id['id'];
		foreach ($request->descripcion as $index => $descrip) {
			$date = new DateTime($request->fecha_inicial[$index][0]);
			$ini = $date->format('Y-m-d');
			$date = new DateTime($request->fecha_inicial[$index][1]);
			$fini = $date->format('Y-m-d');
			
			$thearray = [
				'centro_costo_id'      => $IntCentroid,
				'user_id'              => $request->user_id[$index]['id'],
				'descripcion'          => $request->descripcion[$index],
				'gasto'                => $request->gasto[$index],
				'fecha_inicial'        => $ini,
				'fecha_final'          => $fini,
				'numerodias'           => $request->numerodias[$index],
				'saldo'                => $request->gasto[$index],
				'solicitud_viatico_id' => $solViatico->id,
			
			];
			
			$paraellog[] = implode(",", $thearray);
			viatico::create($thearray);
			$total += $request->gasto[$index];
			
		}
		
		$mensaje = (new EnvioCorreos())->EnviaralGerente($request, $myuser, $cuantosViaticos, $total);
		$solViatico->update(['saldo_sol' => $total]);
		
		if ($mensaje === '-') {
			return back()->with('error', 'El mensaje no fue enviado');
		}
		else {
			DB::commit();
			zzloggingcrud::zilefStoreArrayLogTrace($paraellog);
			
			return back()->with('success', 'Solicitud generada de manera exitosa');
		}
	}
	
	public function create() {}//fin store
	
	//! STORE - UPDATE - DELETE
	//! STORE functions
	
	public function update(Request $request, $id): RedirectResponse {
		zzloggingcrud::zilefLogTrace();
		
		DB::beginTransaction();
		$viatico = solicitud_viatico::findOrFail($id);
		$original = $viatico->getOriginal(); // Valores antes de la actualización
		$viatico->update($request->all());
		
		zzloggingcrud::zilefLogUpdate($this, $viatico, $original);
		
		DB::commit();
		
		return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $viatico->nombre]));
	}
	
	public function EnviaralJefe(Request $request, ?User $myuser, $cuantosViaticos, $total): string {
		$jefe = User::Where('name', 'Carlos Daniel Anaya Barrios')->first();
		if ($jefe) {
			$detalle = [
				'mensaje' => "Se han generado $cuantosViaticos viáticos por un valor de $total. 
                              El solicitante es $myuser->name.
                              Haga click aqui:   https://modnom.ecnomina.com/solicitud_viatico  si desea ver los pendientes."
			];
			if (\Illuminate\Support\Facades\App::environment('production')) {
				EnviarViaticoJob::dispatch($jefe->email, $detalle)->delay(now()->addSeconds(5));
				EnviarViaticoJob::dispatch('ajelof2@gmail.com', $detalle)->delay(now()->addSeconds(5));
				$mensaje = "Correo enviado y Viatico ";
			}
			else {
				EnviarViaticoJob::dispatch('alejofg2@gmail.com', $detalle)->delay(now()->addSeconds(5));
				$mensaje = "Correo enviado solo a alejo ";
				
			}
			
		}
		else {
			$mensaje = "-";
			Myhelp::EscribirEnLog($this, ' ERROR: no se encontro al jefe');
		}
		
		return $mensaje;
	}
	
	//update3
	
	public function legalizarviatico(Request $request, $id): RedirectResponse {
		zzloggingcrud::zilefLogTrace();
		
		DB::beginTransaction();
		$solviatico = solicitud_viatico::findOrFail($id);
		
		foreach ($request->consignacion_id as $index => $item) {
			if (isset($request->valor_legalizacion[$index])) {
				
				$consingacion = consignarViatico::find($request->consignacion_id[$index]);
				$consingacion->update([
					                      'valor_legalizado'         => $request->valor_legalizacion[$index],
					                      'fecha_legalizado'         => $request->fecha_legalizacion[$index],
					                      'descripcion_legalizacion' => $request->descripcion_legalizacion[$index],
				                      ]);
			}
		}
		
		//		if (!empty($request->valor_legalizacion[0])) {
		//			$solviatico->update(['saldo_sol' => $this->getSaldo($solviatico)]);
		//		}
		
		DB::commit();
		Myhelp::EscribirEnLog($this, 'UPDATE3:sol_viaticos EXITOSO', 'sol_viatico id:' . $solviatico->id . ' |solicitado por ' . $solviatico->Solicitante, false);
		
		return back()->with('success', __('app.label.updated_successfully2'));
	}
	
	public function show($id) {}
	
	//consignar
	
	public function edit($id) {}
	
	//update1
	
	public function viaticoupdate2(Request $request, $id): RedirectResponse {
		
		zzloggingcrud::zilefLogTrace();
		DB::beginTransaction();
		$sol_viatico = solicitud_viatico::findOrFail($id);
		$original = $sol_viatico->getOriginal(); // Valores antes de la actualización
		
		$now = Carbon::now();
		$viaticos = $sol_viatico->viaticos;
//		foreach ($request->valor_consig as $index => $valor) {
		foreach ($viaticos as $index => $viatic) {
			$valorconsig = isset($request->valor_consig[$index]) ? (int)$request->valor_consig[$index] : 0;
			if($valorconsig === 0) {
				continue;
			}
			
			$consignarViatico = consignarViatico::create([
                 'valor_consig'          => $valorconsig,
                 'fecha_consig'          => $now,
                 'solicitud_viatico_id'  => $sol_viatico->id,
                 'remitente_user_id'     => Myhelp::AuthUid(),
                 'destinatiario_user_id' => $sol_viatico->viaticos[$index]->user_id,
             ]);
			$viatic->update(['saldo' => $viatic->saldo - $valorconsig]);
			
			Myhelp::EscribirEnLog($this, 'se genero un consignarViatico::' . implode(', ', $consignarViatico->getAttributes()));
		}
		
		$sol_viatico->update(['saldo_sol' => $this->getSaldo($sol_viatico)]);
		
		DB::commit();
		zzloggingcrud::zilefLogUpdate($this, $sol_viatico, $original, 'saldo');
		
		//		if ($request->routeadmin == 'index2') {
		//			return redirect()->route('viatico2')->with('success', __('app.label.updated_successfully2'));
		//		}
		
		return redirect()->route('solicitud_viatico.index')->with('success', __('app.label.updated_successfully2'));
		
	}
	
	//FIN : STORE - UPDATE - DELETE
	
	//update2
	
	public function getSaldo($sol_viatico): int {
		$Int_consignaciones = (int)consignarViatico::Where('solicitud_viatico_id', $sol_viatico->id)->sum('valor_consig');
		$saldo = (int)$sol_viatico->Totalsolicitado - $Int_consignaciones;
		
		return $saldo;
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	
	public function destroy($solicitud_viaticoid) {
		$solicitud_viatico = solicitud_viatico::find($solicitud_viaticoid);
		$elSolicitante = $solicitud_viatico->Solicitante;
		$elFechasol = $solicitud_viatico->Fechasol;
		$elCiudad = $solicitud_viatico->Ciudad;
		$solicitud_viatico->delete();
		Myhelp::EscribirEnLog($this, 'DELETE:solicitud_viaticos', 'solicitud_viatico id:' . $solicitud_viatico->id . ' | el Solicitante: ' . $elSolicitante . ' | el Fechasol: ' . $elFechasol . ' | el Ciudad: ' . $elCiudad . ' ha sido borrado', false);
		
		return back()->with('success', __('app.label.deleted_successfully', ['name' => $elSolicitante]));
	}
	
	public function destroyBulk(Request $request) {
		Myhelp::EscribirEnLog($this, 'DELETE Bulk:solicitud_viaticos');
		
		$solicitud_viaticos = solicitud_viatico::whereIn('id', $request->id)->get();
		
		foreach ($solicitud_viaticos as $sol) {
			
			
					$sol->delete();
			if(!$sol->consignacion()->exists()) {
				if($sol->created_at->isToday()) {
					$sol->delete();
				}else {
					return back()->with('error', 'Ya paso el tiempo para eliminar viáticos');
				}
			}else{
				return back()->with('error', 'Ya existen consignaciones para estos viáticos, no se pueden eliminar');
			}
		}
		Myhelp::EscribirEnLog($this, 'DELETE:solicitud_viaticos', 'solicitud_viatico ids:: '. implode($request->id) .' han sido borrados', false);
		
		
		return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' viaticos ']));
	}
	//FIN : STORE - UPDATE - DELETE
	
}
