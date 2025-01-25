<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\Models\CentroCosto;
use App\Models\cotizacion;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CotizacionController extends Controller
{
    public $thisAtributos, $FromController = 'cotizacion';


    //<editor-fold desc="Construc | mapea | filtro and dependencia">
    public function __construct()
    {
//        $this->middleware('permission:create cotizacion', ['only' => ['create', 'store']]);
//        $this->middleware('permission:read cotizacion', ['only' => ['index', 'show']]);
//        $this->middleware('permission:update cotizacion', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:delete cotizacion', ['only' => ['destroy', 'destroyBulk']]);
        $this->thisAtributos = (new cotizacion())->getFillable(); //not using
    }


    public function Mapear($cotizacions)
    {
        $cotizacions = $cotizacions->get()->map(function ($cotizacion) {
            $cotizacion->centro_costo_id2 = $cotizacion->centro;
            if ($cotizacion->centro_costo_id2) $cotizacion->centro_costo_id2 = $cotizacion->centro_costo_id2->nombre;

            return $cotizacion;
        });
        return $cotizacions;
    }

    public function Filtros(&$cotizacions, $request)
    {
        $cotizacions = cotizacion::query();
        if ($request->has('search')) {
            $cotizacions = $cotizacions->where(function ($query) use ($request) {
                $query->where('numero_cot', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('codigo', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
                ;
            });
        }

        if ($request->has(['field', 'order'])) {
            $cotizacions = $cotizacions->orderBy($request->field, $request->order);
        } else
            $cotizacions = $cotizacions->orderBy('updated_at', 'DESC');
    }

    public function Dependencias()
    {
//        $dependexsSelect = CentroCosto::all('id as value','nombre as label')->toArray();
        $dependexsSelect = CentroCosto::all(['id as value', 'nombre', 'descripcion'])
            ->map(function ($item) {
                return [
                    'value' => $item->value,
                    'label' => ($item->nombre ?? '') . ' - ' . mb_substr($item->descripcion ?? '', 0, 17),
                ];
            })
            ->toArray();


        array_unshift($dependexsSelect, ["label" => "Seleccione un centro de costo", 'value' => 0]);
        return $dependexsSelect;
    }

    //</editor-fold>

    public function index(Request $request)
    {
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' cotizacions '));
        $this->Filtros($cotizacions, $request);
        $cotizacions = $this->Mapear($cotizacions);
        $losSelect = $this->Dependencias();


        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $page = request('page', 1); // Current page number
        $paginated = new LengthAwarePaginator(
            $cotizacions->forPage($page, $perPage),
            $cotizacions->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );
        return Inertia::render($this->FromController . '/Index', [
            'fromController' => $paginated,
            'total' => $cotizacions->count(),

            'breadcrumbs' => [['label' => __('app.label.' . $this->FromController), 'href' => route($this->FromController . '.index')]],
            'title' => __('app.label.' . $this->FromController),
            'filters' => $request->all(['search', 'field', 'order']),
            'perPage' => (int)$perPage,
            'numberPermissions' => $numberPermissions,
            'losSelect' => $losSelect,
        ]);
    }

    public function create()
    {
    }

    //! STORE - UPDATE - DELETE
    //! STORE functions

    public function store(Request $request): RedirectResponse
    {
        $permissions = Myhelp::EscribirEnLog($this, ' Begin STORE:cotizacions');
        DB::beginTransaction();
//        $dependex = $request->dependex['id'];
//        $request->merge(['dependex_id' => $request->dependex['id']]);

//        $request->merge(['centro_costo_id' => null]);
        $request->merge(['aprobado_cot' => false]);
        $request->merge(['fecha_aprobacion_cot' => Carbon::now()]);
        $request->merge(['user_id' => Myhelp::AuthUid()]);
        $request->merge(['precio_cot' => str_replace(".", "", $request->precio_cot)]);
        $cotizacion = cotizacion::create($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'STORE:cotizacions EXITOSO', 'cotizacion id:' . $cotizacion->id . ' | ' . $cotizacion->numero_cot, false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $cotizacion->numero_cot]));
    }

    //fin store functions

    public function show($id){}public function edit($id){}

    public function update(Request $request, $id)
    {
        Myhelp::EscribirEnLog($this, ' Begin UPDATE:cotizacions');
        DB::beginTransaction();
        $cotizacion = cotizacion::findOrFail($id);
//        $request->merge(['dependex_id' => $request->dependex['id']]);
        $cotizacion->update($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'UPDATE:cotizacions EXITOSO', 'cotizacion id:' . $cotizacion->id . ' | ' . $cotizacion->numero_cot, false);
        return back()->with('success', __('app.label.updated_successfully2', ['numero_cot' => $cotizacion->numero_cot]));
    }
    
    public function update2(Request $request, $id)
    {
        Myhelp::EscribirEnLog($this, ' Begin UPDATE:cotizacions');
        DB::beginTransaction();
        $cotizacion = cotizacion::findOrFail($id);
        $centro = centrocosto::create([
            'nombre' => $cotizacion->numero_cot,
            'mano_obra_estimada' => 0,
            'activo' => 1,
            'descripcion' => $cotizacion->descripcion_cot,
            'clasificacion' => '',
            'ValidoParaFacturar' => 1,
        ]);
        $request->merge(['centro_costo_id' => $centro->id]);
        $cotizacion->update($request->all());
        
        DB::commit();
        Myhelp::EscribirEnLog($this, 'UPDATE:cotizacions EXITOSO', 'cotizacion id:' . $cotizacion->id . ' | ' . $cotizacion->numero_cot, false);
        return back()->with('success', __('app.label.updated_successfully', ['name' => $cotizacion->numero_cot]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $cotizacionid
     * @return RedirectResponse
     */

    public function destroy($cotizacionid)
    {
        $permissions = Myhelp::EscribirEnLog($this, 'DELETE:cotizacions');
        $cotizacion = cotizacion::find($cotizacionid);
        $elnumero_cot = $cotizacion->numero_cot;
        $cotizacion->delete();
        Myhelp::EscribirEnLog($this, 'DELETE:cotizacions', 'cotizacion id:' . $cotizacion->id . ' | ' . $cotizacion->numero_cot . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnumero_cot]));
    }

    public function destroyBulk(Request $request)
    {
        $cotizacion = cotizacion::whereIn('id', $request->id);
        $cotizacion->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.user')]));
    }
    //FIN : STORE - UPDATE - DELETE

}
