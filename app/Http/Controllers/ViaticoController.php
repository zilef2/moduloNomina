<?php

namespace App\Http\Controllers;

use App\helpers\zzloggingcrud;
use App\Http\Requests\StoreviaticoRequest;
use App\Jobs\EnviarViaticoJob;
use App\Mail\MailViaticoGenerado;
use App\Models\CentroCosto;
use App\Models\consignarViatico;
use App\Models\solicitud_viatico;
use App\Models\User;
use App\Models\viatico;
use App\helpers\Myhelp;
use App\helpers\MyModels;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class ViaticoController extends Controller {
	
	public array $thisAtributos;
	public int $cotizacionInicial;
	public string $FromController = 'viatico';
	
	//<editor-fold desc="Construc | filtro and dependencia">
	public function __construct() {
		//        $this->middleware('permission:create viatico', ['only' => ['create', 'store']]);
		//        $this->middleware('permission:read viatico', ['only' => ['index', 'show']]);
		//        $this->middleware('permission:update viatico', ['only' => ['edit', 'update']]);
		//        $this->middleware('permission:delete viatico', ['only' => ['destroy', 'destroyBulk']]);
		$this->thisAtributos = (new viatico())->getFillable(); //not using
	}
	
	public function index(Request $request): Response {
		$numberPermissions = zzloggingcrud::zilefLogTrace();
		
		$viaticos = $this->Filtros($request);
		if ($numberPermissions === 9) {
			$viaticos = $this->FiltrosCarlos($request);
		}
		//        else $viaticos = $this->FiltrosAdministrativos($request,$viaticos);
		
		$viaticos = $viaticos->get();
		
		$perPage = $request->has('perPage') ? $request->perPage : 10;
		
		
		return Inertia::render($this->FromController . '/Index', [
			'fromController' => $this->PerPageAndPaginate($request, $viaticos),
			'total'          => $viaticos->count(),
			
			'breadcrumbs'       => [
				[
					'label' => __('app.label.' . $this->FromController),
					'href'  => route($this->FromController . '.index')
				]
			],
			'title'             => __('app.label.' . $this->FromController),
			'filters'           => $request->all([
				                                     'search',
				                                     'field',
				                                     'order'
			                                     ]),
			'perPage'           => (int)$perPage,
			'numberPermissions' => $numberPermissions,
			'losSelect'         => $this->Dependencias() ?? [],
			'totalsaldo'        => $this->totalsaldo() ?? 0,
			'totallegalizado'   => $this->totallegalizado() ?? 0,
		]);
	}
	
	public function Filtros($request): Builder {
		$viaticos = Viatico::query();
		if ($request->has('search')) {
			$viaticos = $viaticos->where(function ($query) use ($request) {
				$query->where('descripcion', 'LIKE', "%" . $request->search . "%")//                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
				;
			});
		}
		
		// Filtro por nombre de usuario (search2)
		if ($request->has('search2') && !empty($request->search2)) {
			$viaticos->whereHas('user', function ($query) use ($request) {
				$query->where('name', 'LIKE', "%" . $request->search2 . "%");
			});
		}
		
		if ($request->has([
			                  'field',
			                  'order'
		                  ])
		) {
			$viaticos = $viaticos->orderBy($request->field, $request->order);
		}
		else {
			$viaticos = $viaticos->orderBy('updated_at', 'DESC');
		}
		
		
		return $viaticos;
	}
	//    public function FiltrosAdministrativos($request,$viaticos): Builder {
	//        
	//        return $viaticos->doesntHave('consignacion');
	//    }
	
	//todo: sync: this should be in all my repos
	
	public function FiltrosCarlos($viaticos): Builder {
		//            $viaticos = $viaticos->WhereNot('saldo', 0);
		$viaticos = $viaticos->orderByRaw('CASE WHEN saldo = 0 THEN 1 ELSE 0 END, created_at DESC');
		
		
		return $viaticos;
	}
	
	public function PerPageAndPaginate($request, $cotizacions) {
		$perPage = $request->has('perPage') ? $request->perPage : 10;
		$page = request('page', 1); // Current page number
		$paginated = new LengthAwarePaginator($cotizacions->forPage($page, $perPage), $cotizacions->count(), $perPage, $page, ['path' => request()->url()]);
		
		
		return $paginated;
	}
	
	//</editor-fold>
	
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
		
		
		return [
			$Empleados,
			$centroSelect,
			$zonas
		];
	}
	
	//todo: tosync
	
	private function totalsaldo(): int {
		return (int)viatico::all()->sum('saldo');
	}
	
	private function totallegalizado(): int {
		$viaticos = viatico::all()->sum('Totallegalizadou');
		
		
		return $viaticos;
	}
	
	//! STORE - UPDATE - DELETE
	//! STORE functions
	
	public function viatico2(Request $request): Response {
		$numberPermissions = zzloggingcrud::zilefLogTrace();
		
		$viaticos = $this->Filtros($request);
		$viaticos = $viaticos->Where('saldo', '>', 0);
		$viaticos = $viaticos->orderByRaw('CASE WHEN saldo = 0 THEN 1 ELSE 0 END, created_at DESC');
		$viaticos = $viaticos->get();
		$losSelect = $this->Dependencias();
		$perPage = $request->has('perPage') ? $request->perPage : 10;
		
		
		return Inertia::render($this->FromController . '/Index2', [
			'fromController'    => $this->PerPageAndPaginate($request, $viaticos),
			'total'             => $viaticos->count(),
			'breadcrumbs'       => [
				[
					'label' => __('app.label.' . $this->FromController),
					'href'  => route($this->FromController . '.index')
				]
			],
			'title'             => __('app.label.' . $this->FromController),
			'filters'           => $request->all(['search', 'field', 'order']),
			'perPage'           => (int)$perPage,
			'numberPermissions' => $numberPermissions,
			'losSelect'         => $losSelect ?? [],
			'totalsaldo'        => $this->totalsaldo() ?? 0,
			'totallegalizado'   => $this->totallegalizado() ?? 0,
		
		]);
	}
	
	//fin store functions
	
	public function store(StoreviaticoRequest $request): RedirectResponse {
		Myhelp::EscribirEnLog($this, ' Begin STORE:viaticos');
		DB::beginTransaction();
		$myuser = Myhelp::AuthU();
		$cuantosViaticos = count($request->centro_costo_id);
		$total = 0;
		$paraellog = [];
		
		foreach ($request->centro_costo_id as $index => $centro) {
			
			$date = new DateTime($request->fecha_inicial[$index][0]);
			$ini = $date->format('Y-m-d');
			$date = new DateTime($request->fecha_inicial[$index][1]);
			$fini = $date->format('Y-m-d');
			
			$thearray = [
				'centro_costo_id' => $centro['id'],
				'user_id'         => $request->user_id[$index]['id'],
				'descripcion'     => $request->descripcion[$index],
				'gasto'           => $request->gasto[$index],
				'fecha_inicial'   => $ini,
				'fecha_final'     => $fini,
				'numerodias'      => $request->numerodias[$index],
				'saldo'           => $request->gasto[$index],
			];
			$paraellog[] = implode(",", $thearray);
			$viatico = viatico::create($thearray);
			
			$total += $request->gasto[$index];
			
		}
		
		
		$mensaje = (new EnvioCorreos())->EnviaralGerente($request, $myuser, $cuantosViaticos, $total);
		if ($mensaje === '-') {
			return back()->with('error', 'El mensaje no fue enviado');
		}
		else {
			DB::commit();
			zzloggingcrud::zilefStoreArrayLogTrace($paraellog);
			
			
			return back()->with('success', $mensaje . __('app.label.created_successfully', ['name' => $viatico->nombre]));
		}
		
	}
	
	public function create() {}
	
	/**
	 * @param Request $request
	 * @param User|null $myuser
	 * @return string
	 */
	public function EnviaralJefe(Request $request, ?User $myuser, $cuantosViaticos, $total): string {
		$jefe = User::Where('name', 'Carlos Daniel Anaya Barrios')->first();
		if ($jefe) {
			$detalle = [
				'mensaje' => "Se han generado $cuantosViaticos viáticos por un valor de $total. 
                              El solicitante es $myuser->name.
                              Haga click aqui:   https://modnom.ecnomina.com/solicitud_viatico   si desea ver los pendientes."
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
	}//fin store
	
	public function show($id) {}
	
	//paso2 cuando el admin APRUEBA
	
	public function edit($id) {}
	
	public function viaticoupdate2(Request $request, $id): RedirectResponse {
		
		zzloggingcrud::zilefLogTrace();
		DB::beginTransaction();
		$solicitud_viatico = solicitud_viatico::findOrFail($id);
		$original = $solicitud_viatico->getOriginal(); // Valores antes de la actualización
		
		$now = Carbon::now();
		$consignarViatico = consignarViatico::
		create([
			       'valor_consig' => $request->valor_consig,
			       'fecha_consig' => $now,
			       'solicitud_viatico_id'   => $solicitud_viatico->id,
			       'user_id'      => Myhelp::AuthUid(),
		       ]);
		
		$solicitud_viatico->update(['saldo_sol' => $this->getSaldo($solicitud_viatico),]);
		
		DB::commit();
		zzloggingcrud::zilefLogUpdate($this, $solicitud_viatico, $original, 'saldo_sol');
		Myhelp::EscribirEnLog($this, 'ADEMAS, se genero un consignarViatico::' . implode(', ', $consignarViatico->getAttributes()));
		
		if ($request->routeadmin == 'index2') {
			return redirect()->route('viatico2')->with('success', __('app.label.updated_successfully2'));
		}
		
		
		return redirect()->route('viatico.index')->with('success', __('app.label.updated_successfully2'));
		
	}
	
	//update1
	public function update(Request $request, $id): RedirectResponse {
		zzloggingcrud::zilefLogTrace();
		
		DB::beginTransaction();
		$viatico = viatico::findOrFail($id);
		$original = $viatico->getOriginal(); // Valores antes de la actualización
		
		//        if(gettype($request->user_id) === 'integer'){
		//            $foreign1 = $request->user_id;
		//        }
		//        else{
		//            $foreign1 = $request->user_id['id'];
		//        }
		//        
		//        if(gettype($request->centro_costo_id) === 'integer'){
		//            $foreign2 = $request->centro_costo_id;
		//        }
		//        else{
		//            $foreign2 = $request->centro_costo_id['id'];
		//        }
		
		//        $request->merge(['user_id' => $foreign1]);
		//        $request->merge(['centro_costo_id' => $foreign2]);
		$viatico->update($request->all());
		
		zzloggingcrud::zilefLogUpdate($this, $viatico, $original);
		
		DB::commit();
		
		
		return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $viatico->nombre]));
	}
	
	//FIN : STORE - UPDATE - DELETE
	
	public function getSaldo($solicitud_viatico): int {
		$Int_consignaciones = (int)consignarViatico::Where('solicitud_viatico_id', $solicitud_viatico->id)->sum('valor_consig');
		$getTotalsolicitadoAttribute = (int)$solicitud_viatico->getTotalsolicitadoAttribute();
		
		
		return $getTotalsolicitadoAttribute - $Int_consignaciones;
	}
	
	//update3
	public function legalizarviatico(Request $request, $id): RedirectResponse {
		zzloggingcrud::zilefLogTrace();
		
		DB::beginTransaction();
		$viatico = viatico::findOrFail($id);
		
		foreach ($request->consignacion_id as $index => $item) {
			$consingacion = consignarViatico::find($request->consignacion_id[$index]);
			$consingacion->update([
				                      'valor_legalizado'         => $request->valor_legalizacion[$index],
				                      'fecha_legalizado'         => $request->fecha_legalizacion[$index],
				                      'descripcion_legalizacion' => $request->descripcion_legalizacion[$index],
			                      ]);
		}
		
		if (!empty($request->valor_legalizacion[0])) {
			$viatico->update(['saldo' => $this->getSaldo($viatico)]);
		}
		
		DB::commit();
		Myhelp::EscribirEnLog($this, 'UPDATE:viaticos EXITOSO', 'viatico id:' . $viatico->id . ' | ' . $viatico->nombre, false);
		
		
		return back()->with('success', __('app.label.updated_successfully2'));
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id
	 * @return RedirectResponse
	 */
	
	public function destroy($viaticoid) {
		zzloggingcrud::zilefLogTrace();
		
		$viatico = viatico::find($viaticoid);
		$elnombre = $viatico->nombre;
		$viatico->delete();
		Myhelp::EscribirEnLog($this, 'DELETE:viaticos', 'viatico id:' . $viatico->id . ' | ' . $viatico->nombre . ' borrado', false);
		
		
		return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
	}
	
	public function destroyBulk(Request $request) {
		zzloggingcrud::zilefLogTrace();
		$permissions = Myhelp::AuthU()->roles->pluck('name')[0];
		$numberpermission = MyModels::getPermissionToNumber($permissions);
		if ($numberpermission > 9) {
			
			$numdestruidos = count($request->id);
			$viatico = viatico::whereIn('id', $request->id);
			$viatico->delete();
			
			zzloggingcrud::zilefLogBulk('viaticos', $numdestruidos);
			
			
			return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.user')]));
		}
		
		
		return back()->with('error', 'Acción desautorizada');
		
	}
	
}
