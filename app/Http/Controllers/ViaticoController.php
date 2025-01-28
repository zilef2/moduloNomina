<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\Models\CentroCosto;
use App\Models\User;
use App\Models\viatico;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ViaticoController extends Controller
{
    public array $thisAtributos;
    public string $FromController = 'viatico';


    //<editor-fold desc="Construc | mapea | filtro and dependencia">
    public function __construct()
    {
//        $this->middleware('permission:create viatico', ['only' => ['create', 'store']]);
//        $this->middleware('permission:read viatico', ['only' => ['index', 'show']]);
//        $this->middleware('permission:update viatico', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:delete viatico', ['only' => ['destroy', 'destroyBulk']]);
        $this->thisAtributos = (new viatico())->getFillable(); //not using
    }


    public function Mapear(): Builder
    {
        //$viaticos = Viatico::with('no_nada');
        $viaticos = Viatico::Where('id', '>', 0);
        return $viaticos;

    }

    public function Filtros(&$viaticos, $request)
    {
        if ($request->has('search')) {
            $viaticos = $viaticos->where(function ($query) use ($request) {
                $query->where('descripcion', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('codigo', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
                ;
            });
        }

        if ($request->has(['field', 'order'])) {
            $viaticos = $viaticos->orderBy($request->field, $request->order);
        } else
            $viaticos = $viaticos->orderBy('updated_at', 'DESC');
    }

    public function Dependencias()
    {
        $Empleados = User::select('id', 'name')->whereHas('roles', function ($query) {
            return $query->WhereIn('name', ['supervisor', 'administrativo', 'ingeniero', 'empleado']);
        })->get()->toArray();
        $centroSelect = CentroCosto::all('id', 'nombre as name')->toArray();
        array_unshift($Empleados, ["name" => "Seleccione una persona", 'id' => 0]);
        array_unshift($centroSelect, ["name" => "Seleccione un centro de costo", 'id' => 0]);
        return [$Empleados, $centroSelect];
    }

    //</editor-fold>

    public function index(Request $request): Response
    {
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' viaticos '));
        $viaticos = $this->Mapear();
        $this->Filtros($viaticos, $request);
        $losSelect = $this->Dependencias();


        $perPage = $request->has('perPage') ? $request->perPage : 10;
        return Inertia::render($this->FromController . '/Index', [
            'fromController' => $viaticos->paginate($perPage),
            'total' => $viaticos->count(),

            'breadcrumbs' => [['label' => __('app.label.' . $this->FromController), 'href' => route($this->FromController . '.index')]],
            'title' => __('app.label.' . $this->FromController),
            'filters' => $request->all(['search', 'field', 'order']),
            'perPage' => (int)$perPage,
            'numberPermissions' => $numberPermissions,
            'losSelect' => $losSelect ?? [],
        ]);
    }

    public function create()
    {
    }

    //! STORE - UPDATE - DELETE
    //! STORE functions

    public function store(Request $request): RedirectResponse
    {
        $permissions = Myhelp::EscribirEnLog($this, ' Begin STORE:viaticos');
        DB::beginTransaction();
        dd($request->centro_costo_id);
//        $no_nada = $request->no_nada['id'];
        $request->merge(['user_id' => $request->user_id]);
        $request->merge(['centro_costo_id' => $request->centro_costo_id]);
        $viatico = viatico::create($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'STORE:viaticos EXITOSO', 'viatico id:' . $viatico->id . ' | ' . $viatico->nombre, false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $viatico->nombre]));
    }

    //fin store functions

    public function show($id)
    {
    }

    public function edit($id)
    {
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $permissions = Myhelp::EscribirEnLog($this, ' Begin UPDATE:viaticos');
        DB::beginTransaction();
        $viatico = viatico::findOrFail($id);
        $request->merge(['user_id' => $request->user_id]);
        $request->merge(['centro_costo_id' => $request->centro_costo_id]);
        $viatico->update($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'UPDATE:viaticos EXITOSO', 'viatico id:' . $viatico->id . ' | ' . $viatico->nombre, false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $viatico->nombre]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */

    public function destroy($viaticoid)
    {
        $permissions = Myhelp::EscribirEnLog($this, 'DELETE:viaticos');
        $viatico = viatico::find($viaticoid);
        $elnombre = $viatico->nombre;
        $viatico->delete();
        Myhelp::EscribirEnLog($this, 'DELETE:viaticos', 'viatico id:' . $viatico->id . ' | ' . $viatico->nombre . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
    }

    public function destroyBulk(Request $request)
    {
        $viatico = viatico::whereIn('id', $request->id);
        $viatico->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.user')]));
    }
    //FIN : STORE - UPDATE - DELETE

}
