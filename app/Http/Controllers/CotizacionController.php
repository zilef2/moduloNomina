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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CotizacionController extends Controller {
    public $thisAtributos, $FromController = 'cotizacion';


    //<editor-fold desc="Construc | mapea | filtro, order and dependencia">
    public function __construct() {
        $this->cotizacionInicial = 7334;
        $this->thisAtributos = (new cotizacion())->getFillable(); //not using
    }

    public function index(Request $request) {
        $cotizacionInicial2 = Cotizacion::count();
        $consecutivoCotizacion = $cotizacionInicial2 + $this->cotizacionInicial;


        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' cotizacions '));
        $this->Filtros($cotizacions, $request);
        $cotizacions = $this->Mapear($cotizacions);


        $perPage = $request->has('perPage') ? $request->perPage : 5;
        return Inertia::render($this->FromController . '/Index', [
            'fromController' => $this->PerPageAndPaginate($request, $cotizacions),
            'perPage' => (int)$perPage,
            'total' => $cotizacions->count(),

            'breadcrumbs' => [['label' => __('app.label.' . $this->FromController), 'href' => route($this->FromController . '.index')]],
            'title' => __('app.label.' . $this->FromController),
            'filters' => $request->all(['search', 'field', 'order']),
            'numberPermissions' => $numberPermissions,
            'losSelect' => $this->Dependencias2025(),
            'CentrosRepetidos' => $this->CentrosRepetidos(),
            'consecutivoCotizacion' => $consecutivoCotizacion,
            'cotizacionInicial2' => $cotizacionInicial2,
        ]);
    }

    public function Filtros(&$cotizacions, $request) {
        $cotizacions = cotizacion::query();
        if ($request->has('search')) {
            $cotizacions = $cotizacions->where(function ($query) use ($request) {
                $query->where('numero_cot', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('codigo', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
                ;
            });
        }
        if ($request->has('search2')) {
            $cotizacions = $cotizacions->where(function ($query) use ($request) {
                $query->where('descripcion_cot', 'LIKE', "%" . $request->search2 . "%");
            });
        }
        if ($request->has('search3')) {
            $cotizacions = $cotizacions->where(function ($query) use ($request) {
                $query->where('fecha_aprobacion_cot', 'LIKE', "%" . $request->search3 . "%");
            });
        }
        if ($request->has('search4')) {
            $cotizacions = $cotizacions->where(function ($query) use ($request) {
                $query->whereRaw("numero_cot REGEXP '^[0-9]+$'");
            });
        }

        if ($request->has('search5')) { //todo: esto aun no se implementa, 
            $cotizacions = $cotizacions->where(function ($query) use ($request) {
                $query->whereRaw("numero_cot REGEXP '^[A-Za-z]+-[0-9]+$'");
            });
        }


        $this->Order($cotizacions, $request);
    }

    public function Order(&$cotizacions, $request) {
        if ($request->has(['field', 'order'])) {
            if ($request->field == 'centro_costo') {
                $cotizacions = $cotizacions
                    ->join('centros_costos', 'cotizacions.centro_costo_id', '=', 'centros_costos.id')
                    ->orderBy('centros_costos.nombre', $request->order)
                    ->select('cotizacions.*');
            }
            else {

                $cotizacions = $cotizacions->orderBy($request->field, $request->order);
            }
        }
        else {
            $cotizacions = $cotizacions->orderBy('updated_at', 'DESC');
        }
    }

    public function Mapear($cotizacions) {
        $cotizacions = $cotizacions->get()->map(function ($cotizacion) {
            $cotizacion->centro_costo_id2 = $cotizacion->centro;
            if ($cotizacion->centro_costo_id2) $cotizacion->centro_costo_id2 = $cotizacion->centro_costo_id2->nombre;

            return $cotizacion;
        });
        return $cotizacions;
    }

    public function Dependencias2025(): array { //todo: torescue:
        $dependexsSelect = CentroCosto::all(['id as value', 'nombre', 'descripcion'])
            ->map(function ($item) {
                $descrip = $item->descripcion == '' ? ' - No descripción ' : ' - ' . mb_substr($item->descripcion, 0, 17);
                return [
                    'value' => $item->value,
                    'label' => ($item->nombre ?? '') . $descrip
                ];
            })->toArray();

        array_unshift($dependexsSelect, ["label" => "Seleccione un centro de costo", 'value' => 0]);
        return [
            'centros' => $dependexsSelect,
        ];
    }

    public function PerPageAndPaginate($request, $modelo): LengthAwarePaginator {
        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $page = request('page', 1); // Current page number
        return new LengthAwarePaginator(
            $modelo->forPage($page, $perPage),
            $modelo->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );
    }

    //</editor-fold>

    public function CentrosRepetidos(): array {
        return CentroCosto::pluck('nombre')->toArray();
    }

    public function store(Request $request): RedirectResponse {
        $permissions = Myhelp::EscribirEnLog($this, ' Begin STORE:cotizacions');
        DB::beginTransaction();
        $request->merge(['aprobado_cot' => false]);
        $request->merge(['user_id' => Myhelp::AuthUid()]);
        $request->merge(['precio_cot' => str_replace(".", "", $request->precio_cot)]);
        $this->SonSelect($request, [
            'centro_costo_id',
            'estado_cliente',
            'estado',
            'mes_pedido',
            'tipo',
            'tipo_de_mantenimiento',
        ]);
        $cotizacion = cotizacion::create($request->all());
        Cache::forget('centro_costos'); // Borra la caché normal para búsquedas nuevas

        DB::commit();
        Myhelp::EscribirEnLog($this, 'STORE:cotizacions EXITOSO', 'permissions = ' . $permissions . ' | cotizacion id:' . $cotizacion->id . ' | ' . $cotizacion->numero_cot, false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $cotizacion->numero_cot]));
    }

    //! STORE - UPDATE - DELETE
    //! STORE functions

    private function SonSelect(Request $request, array $selectinput) {
        foreach ($selectinput as $index => $item) {
            $valor = $request->{$item}['value'] ?? null;
            $request->merge([$item => $valor]);
        }
    }

    //fin store functions

    public function create() {
    }

    public function show($id) {
    }

    public function edit($id) {
    }

    public function update2(Request $request, $id) {
        Myhelp::EscribirEnLog($this, ' Begin update2:cotizacions');
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
        $request->merge(['fecha_aprobacion_cot' => Carbon::now()]);
        $cotizacion->update($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'UPDATE:cotizacions EXITOSO', 'cotizacion id:' . $cotizacion->id . ' | ' . $cotizacion->numero_cot, false);
        return back()->with('success', __('app.label.updated_successfully', ['name' => $cotizacion->numero_cot]));
    }

    //generar centro de costo

    public function update(Request $request, $id) {
        Myhelp::EscribirEnLog($this, ' Begin UPDATE:cotizacions');
        DB::beginTransaction();
        $cotizacion = cotizacion::findOrFail($id);

        $request->merge(['precio_cot' => str_replace(".", "", $request->precio_cot)]);
        $this->SonSelect($request, [
            'centro_costo_id',
            'estado_cliente',
            'estado',
            'mes_pedido',
            'tipo',
            'tipo_de_mantenimiento',
        ]);
        $cotizacion->update($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'UPDATE:cotizacions EXITOSO', 'cotizacion id:' . $cotizacion->id . ' | ' . $cotizacion->numero_cot, false);
        return back()->with('success', __('app.label.updated_successfully2', ['numero_cot' => $cotizacion->numero_cot]));
    }

    public function update3(Request $request, $id): RedirectResponse {
        Myhelp::EscribirEnLog($this, ' Begin update3:cotizacions se ha facturao chaval');
        DB::beginTransaction();
        $cotizacion = cotizacion::findOrFail($id);
        $centro = centrocosto::findOrFail($cotizacion->centro_costo_id);
        $centro->update(['activo' => 0]);
        $cotizacion->update([
            'factura' => $request->factura,
            'fecha_factura' => $request->fecha_factura,
        ]);

        DB::commit();
        Myhelp::EscribirEnLog($this, 'UPDATE:EXITOSO', 'cotizacion id:' . $cotizacion->id . ' | centro id: ' . $centro->id, false);
        return back()->with('success', __('app.label.updated_successfully', ['name' => $cotizacion->numero_cot]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $cotizacionid
     * @return RedirectResponse
     */

    public function destroy($cotizacionid) {
        $permissions = Myhelp::EscribirEnLog($this, 'DELETE:cotizacions');
        $cotizacion = cotizacion::find($cotizacionid);
        $elnumero_cot = $cotizacion->numero_cot;
        $cotizacion->delete();
        Myhelp::EscribirEnLog($this, 'DELETE:cotizacions', 'cotizacion id:' . $cotizacion->id . ' | ' . $cotizacion->numero_cot . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnumero_cot]));
    }
    //FIN : STORE - UPDATE - DELETE

    //tosync: en cada controlador

    public function destroyBulk(Request $request) {
        $cotizacion = cotizacion::whereIn('id', $request->id);
        $cotizacion->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.user')]));
    }

}
