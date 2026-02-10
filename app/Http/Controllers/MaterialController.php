<?php

namespace App\Http\Controllers;

use App\Jobs\LogZilefMessage;
use App\Models\material;
use App\helpers\Myhelp;
use App\helpers\MyModels;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class MaterialController extends Controller {
	
	public array $thisAtributos;
	public string $FromController = 'material';
	
	//<editor-fold desc="Construc | mapea | filtro and dependencia">
	public function __construct() {
		//        $this->middleware('permission:create material', ['only' => ['create', 'store']]);
		//        $this->middleware('permission:read material', ['only' => ['index', 'show']]);
		//        $this->middleware('permission:update material', ['only' => ['edit', 'update']]);
		//        $this->middleware('permission:delete material', ['only' => ['destroy', 'destroyBulk']]);
		$this->thisAtributos = (new material())->getFillable(); //not using
	}
	
	public function index(Request $request) {
		$numberPermissions = (LogZilefMessage::dispatch(' index materials '));
		$materials = $this->Filtros($request)->get();
		//        $losSelect = $this->Dependencias();
		
		$perPage = $request->has('perPage') ? $request->perPage : 10;
		
		return Inertia::render($this->FromController . '/Index', [
			'fromController' => $this->PerPageAndPaginate($request, $materials),
			'total'          => $materials->count(),
			
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
				                                     'order',
				                                     'filter_nombre',
				                                     'filter_unidad_de_medida',
				                                     'filter_cantidad_min',
				                                     'filter_cantidad_max',
				                                     'filter_precio_unitario_min',
				                                     'filter_precio_unitario_max',
				                                     'filter_fecha_adquisicion_from',
				                                     'filter_fecha_adquisicion_to',
				                                     'filter_stock_minimo_min',
				                                     'filter_stock_minimo_max',
				                                     'filter_ubicacion'
			                                     ]),
			'perPage'           => (int)$perPage,
			'numberPermissions' => $numberPermissions,
			'losSelect'         => $losSelect ?? [],
		]);
	}
	
	public function Filtros($request): Builder {
		$materials = material::query();
		
		// Filtro general de búsqueda (existente)
		if ($request->has('search')) {
			$materials = $materials->where(function ($query) use ($request) {
				$query->where('nombre', 'LIKE', "%" . $request->search . "%")
					//                    ->orWhere('codigo', 'LIKE', "%" . $request->search . "%")
					//                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
				;
			});
		}
		
		// Filtrs por columna específica
		if ($request->filled('filter_nombre')) {
			$materials = $materials->where('nombre', 'LIKE', "%" . $request->filter_nombre . "%");
		}
		
		if ($request->filled('filter_unidad_de_medida')) {
			$materials = $materials->where('unidad_de_medida', 'LIKE', "%" . $request->filter_unidad_de_medida . "%");
		}
		
		if ($request->filled('filter_cantidad_min')) {
			$materials = $materials->where('cantidad', '>=', $request->filter_cantidad_min);
		}
		
		if ($request->filled('filter_cantidad_max')) {
			$materials = $materials->where('cantidad', '<=', $request->filter_cantidad_max);
		}
		
		if ($request->filled('filter_precio_unitario_min')) {
			$materials = $materials->where('precio_unitario', '>=', $request->filter_precio_unitario_min);
		}
		
		if ($request->filled('filter_precio_unitario_max')) {
			$materials = $materials->where('precio_unitario', '<=', $request->filter_precio_unitario_max);
		}
		
		if ($request->filled('filter_fecha_adquisicion_from')) {
			$materials = $materials->where('fecha_adquisicion', '>=', $request->filter_fecha_adquisicion_from);
		}
		
		if ($request->filled('filter_fecha_adquisicion_to')) {
			$materials = $materials->where('fecha_adquisicion', '<=', $request->filter_fecha_adquisicion_to);
		}
		
		if ($request->filled('filter_stock_minimo_min')) {
			$materials = $materials->where('stock_minimo', '>=', $request->filter_stock_minimo_min);
		}
		
		if ($request->filled('filter_stock_minimo_max')) {
			$materials = $materials->where('stock_minimo', '<=', $request->filter_stock_minimo_max);
		}
		
		if ($request->filled('filter_ubicacion')) {
			$materials = $materials->where('ubicacion', 'LIKE', "%" . $request->filter_ubicacion . "%");
		}
		
		if ($request->has(['field', 'order'])) {
			$materials = $materials->orderBy($request->field, $request->order);
		}
		else {
			$materials = $materials->orderBy('updated_at', 'DESC');
		}
		
		return $materials;
	}
	//    public function Dependencias()
	//    {
	//        $no_nadasSelect = No_nada::all('id','nombre as name')->toArray();
	//        array_unshift($no_nadasSelect,["name"=>"Seleccione un no_nada",'id'=>0]);
	
	//        $ejemploSelec = CentroCosto::all('id', 'nombre as name')->toArray();
	//        array_unshift($ejemploSelec, ["name" => "Seleccione un ejemploSelec", 'id' => 0]);
	//        return [$no_nadasSelect];
	//        return [$no_nadasSelect,$ejemploSelec];
	//    }
	
	//</editor-fold>
	
	public function PerPageAndPaginate($request, $cotizacions) {
		$perPage = $request->has('perPage') ? $request->perPage : 10;
		$page = request('page', 1); // Current page number
		$paginated = new LengthAwarePaginator($cotizacions->forPage($page, $perPage), $cotizacions->count(), $perPage, $page, ['path' => request()->url()]);
		
		return $paginated;
	}
	
	public function store(Request $request): RedirectResponse {
		LogZilefMessage::dispatch(' Begin STORE:materials');
		LogZilefMessage::dispatch('');
		
		DB::beginTransaction();
		//        $no_nada = $request->no_nada['id'];
		//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
		
		$fecha_adquisicion = (new \App\helpers\Myhelp)->updatingDate($request->fecha_adquisicion);
		$request->merge(['fecha_adquisicion' => $fecha_adquisicion]);
		$material = material::create($request->all());
		
		DB::commit();
		LogZilefMessage::dispatch('STORE:materials EXITOSO' . ' | material id:' . $material->id . ' | ' . $material->nombre);
		
		return back()->with('success', __('app.label.created_successfully', ['name' => $material->nombre]));
	}
	
	//! STORE - UPDATE - DELETE
	//! STORE functions
	
	public function create() {}
	
	//fin store functions
	
	public function show($id) {}
	
	public function edit($id) {}
	
	public function update(Request $request, $id): RedirectResponse {
		LogZilefMessage::dispatch(' Begin UPDATE:materials');
		DB::beginTransaction();
		$material = material::findOrFail($id);
		$fecha_adquisicion = (new \App\helpers\Myhelp)->updatingDate($request->fecha_adquisicion);
		$request->merge(['fecha_adquisicion' => $fecha_adquisicion]);
		$material->update($request->all());
		
		DB::commit();
		LogZilefMessage::dispatch('UPDATE:materials EXITOSO' . ' | material id:' . $material->id . ' | ' . $material->nombre);
		
		return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $material->nombre]));
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	
	public function destroy($materialid) {
		LogZilefMessage::dispatch('DELETE:materials');
		$material = material::find($materialid);
		$elnombre = $material->nombre;
		$material->delete();
		LogZilefMessage::dispatch('DELETE:materials' . 'material id:' . $material->id . ' | ' . $material->nombre . ' borrado');
		
		return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
	}
	
	public function destroyBulk(Request $request) {
		$material = material::whereIn('id', $request->id);
		$material->delete();
		
		return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.user')]));
	}
	//FIN : STORE - UPDATE - DELETE
	
}
