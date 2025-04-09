<?php

namespace App\Http\Controllers;

use App\Models\desarrollo;
use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\Models\pagodesarrollo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DesarrolloController extends Controller {
	
	public array $thisAtributos;
	public string $FromController = 'desarrollo';
	public array $estados = [
		'Cotizando',
		'Desarrollando', //1
		'Esperando pago parcial',
		'Pagada totalmente',//3
		'Finalizada'
	];
	
	public $Array_search = ['search', 'field', 'order'];
	public $breadcrumbs;
	
	//<editor-fold desc="Construc | mapea | filtro and dependencia">
	public function __construct() {
		//        $this->middleware('permission:create desarrollo', ['only' => ['create', 'store']]);
		//        $this->middleware('permission:read desarrollo', ['only' => ['index', 'show']]);
		//        $this->middleware('permission:update desarrollo', ['only' => ['edit', 'update']]);
		//        $this->middleware('permission:delete desarrollo', ['only' => ['destroy', 'destroyBulk']]);
		$this->thisAtributos = (new desarrollo())->getFillable(); //not using
		$this->breadcrumbs = [
			[
				'label' => __('app.label.' . $this->FromController),
				'href'  => route($this->FromController . '.index')
			]
		];                                                        //not using
	}
	
	public function index(Request $request): Response {
		
		$divisionPAge = 100;
		$commonController = new CommonAditionsToCrudController();
		$numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' desarrollos '));
		$desarrollos = $commonController->FiltrosDesarrollo($request, $this->estados);
		
		$todoslosdllos = desarrollo::all();
		$saldodllo = $todoslosdllos->sum('valor_inicial') - ($todoslosdllos->sum('totalpagado'));
		$perPage = $request->has('perPage') ? $request->perPage : $divisionPAge;
		
		
		return Inertia::render($this->FromController . '/Index', [
			'fromController'    => $commonController->PagePaginate($request, $page, $divisionPAge, $desarrollos),
			'total'             => $desarrollos->count(),
			'breadcrumbs'       => $this->breadcrumbs,
			'title'             => __('app.label.' . $this->FromController),
			'filters'           => $request->all($this->Array_search),
			'perPage'           => (int)$perPage,
			'numberPermissions' => $numberPermissions,
			'losSelect'         => $losSelect ?? [],
			'saldodllo'         => $saldodllo ?? 0,
		]);
	}
	
	//</editor-fold>
	
	public function store(Request $request): RedirectResponse {
		$permissions = Myhelp::EscribirEnLog($this, ' Begin STORE:desarrollos');
		DB::beginTransaction();
		$request->merge(['estado' => 'Cotizando']);
		$desarrollo = desarrollo::create($request->all());
		
		DB::commit();
		Myhelp::EscribirEnLog($this, 'STORE:desarrollos EXITOSO', 'desarrollo id:' . $desarrollo->id . ' | ' . $desarrollo->nombre, false);
		
		
		return back()->with('success', __('app.label.created_successfully', ['name' => $desarrollo->nombre]));
	}
	
	//! STORE - UPDATE - DELETE
	//! STORE functions
	
	public function create() {}//fin store functions public function show($id) { } public function edit($id) { }
	
	public function updatePago(Request $request, $id): RedirectResponse {
		$permissions = Myhelp::EscribirEnLog($this, ' Begin UPDATE:desarrollos');
		DB::beginTransaction();
		$desarrollo = desarrollo::findOrFail($id);
		if ($request->estado && gettype($request->estado) !== 'string' && $request->estado['value']) {
			$desarrollo->update(['estado' => $request->estado['value']]);
		}
		
		$numCuotas = pagodesarrollo::Where('desarrollo_id', $desarrollo->id)->count();
		
		pagodesarrollo::create([
			                       'valor'         => $request->valor,
			                       'fecha'         => $request->fecha,
			                       'cuota'         => $numCuotas + 1,
			                       'final'         => 0,
			                       'desarrollo_id' => $desarrollo->id,
		                       ]);
		DB::commit();
		Myhelp::EscribirEnLog($this, 'UPDATE2:desarrollos EXITOSO', 'desarrollo id:' . $desarrollo->id . ' | ' . $desarrollo->nombre, false);
		
		
		return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $desarrollo->nombre]));
	}
	
	//paso 2
	
	public function update(Request $request, $id): RedirectResponse {
		$permissions = Myhelp::EscribirEnLog($this, ' Begin UPDATE:desarrollos');
		DB::beginTransaction();
		$desarrollo = desarrollo::findOrFail($id);
		if ($request->estado && gettype($request->estado) !== 'string' && $request->estado['value']) {
			$request->merge(['estado' => $request->estado['value']]);
		}
		$desarrollo->update($request->all());
		
		DB::commit();
		Myhelp::EscribirEnLog($this, 'UPDATE:desarrollos EXITOSO', 'desarrollo id:' . $desarrollo->id . ' | ' . $desarrollo->nombre, false);
		
		
		return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $desarrollo->nombre]));
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	
	public function destroy($desarrolloid) {
		$permissions = Myhelp::EscribirEnLog($this, 'DELETE:desarrollos');
		$desarrollo = desarrollo::find($desarrolloid);
		$elnombre = $desarrollo->nombre;
		$desarrollo->delete();
		Myhelp::EscribirEnLog($this, 'DELETE:desarrollos', 'desarrollo id:' . $desarrollo->id . ' | ' . $desarrollo->nombre . ' borrado', false);
		
		
		return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
	}
	
	public function destroyBulk(Request $request) {
		$desarrollo = desarrollo::whereIn('id', $request->id);
		$desarrollo->delete();
		
		
		return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.user')]));
	}
	//FIN : STORE - UPDATE - DELETE
	
}
