<?php

namespace App\Http\Controllers;

use AllowDynamicProperties;
use App\helpers\Myhelp;
use App\helpers\MyhelpQuincena;
use App\helpers\MyModels;
use App\Http\Requests\CentroCostoRequest;
use App\Models\CentroCosto;
use App\Models\Parametro;
use App\Models\Reporte;
use App\Models\solicitud_viatico;
use App\Models\User;
use App\Models\Viatico;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

#[AllowDynamicProperties]
class CentroCostosController extends Controller
{

    public function __construct()
    {
        // Constructor is now clean.
    }

    public function index(Request $request): Response
    {
        $helperSelect = new HelperControllerSelect();
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, 'CentroCostosController.index'));

        $listaSupervisores = User::whereHas('roles', function ($q) {
            $q->where('name', 'supervisor');
        })->get();

        $losSelect = $helperSelect->DependenciasCentro('zona');

        $query = CentroCosto::with(['users', 'zona']);

        // Server-side filtering
        if ($request->has('columnFilters')) {
            $filters = $request->input('columnFilters');
            foreach ($filters as $field => $value) {
                if ($value && strpos(strtolower($value), 'seleccione') === false) {
                    switch ($field) {
                        case 'Zouna':
                            $query->whereHas('zona', function ($q) use ($value) {
                                $q->where('nombre', 'like', '%' . $value . '%');
                            });
                            break;
                        case 'supervisores':
                            $query->whereHas('users', function ($q) use ($value) {
                                $q->where('name', 'like', '%' . $value . '%');
                            });
                            break;
                        case 'activo':
                        case 'ValidoParaFacturar':
                            $query->where($field, strtolower($value) === 'si');
                            break;
                        default:
                            $query->where($field, 'like', '%' . $value . '%');
                            break;
                    }
                }
            }
        }


        if ($request->has('field') && $request->has('order')) {
            if ($request->field === 'Zouna') {
                $query->leftJoin('zonas', 'centro_costos.zona_id', '=', 'zonas.id')
                    ->orderByRaw('CASE WHEN zonas.nombre IS NULL THEN 1 ELSE 0 END, zonas.nombre ' . $request->order)
                    ->select('centro_costos.*');
            } else {
                $query->orderBy($request->field, $request->order);
            }
        } else {
            $query->orderBy('activo', 'desc')->orderBy('mano_obra_estimada', 'desc');
        }

        $perPage = 20;
        $centroCostos = $query->paginate($perPage);

        $popularCenters = CentroCosto::where('mano_obra_estimada', '>', 100)
            ->orderByDesc('mano_obra_estimada')
            ->take(5)
            ->get(['id', 'nombre', 'descripcion'])
            ->map(fn($c) => ['id' => $c->id, 'nombre' => "{$c->nombre} - {$c->descripcion}"]);

        return Inertia::render('CentroCostos/Index', [
            'fromController' => $centroCostos,
            'breadcrumbs' => [['label' => __('app.label.CentroCostos'), 'href' => route('CentroCostos.index')]],
            'title' => __('app.label.CentroCostos'),
            'filters' => $request->all(['field', 'order', 'columnFilters']),
            'nombresTabla' => $this->getNombresTabla(),
            'listaSupervisores' => $listaSupervisores,
            'numberPermissions' => $numberPermissions,
            'losSelect' => $losSelect,
            'popularCenters' => $popularCenters,
        ]);
    }

    public function getNombresTabla(): array
    {
        $permissions = Auth()->user()->roles->pluck('name')[0];
        if ($permissions === 'empleado') {
            return [
                ['#', 'nombre'],
                [null, 'nombre'],
            ];
        } else {
            return [
                ['Acciones', '#', 'nombre', 'Mano obra estimada', 'zona', 'Supervisores', 'activo', 'Facturar', 'descripcion', 'clasificacion', '# usuarios'],
                [null, null, 'nombre', 'mano_obra_estimada', 'Zouna', 'supervisores', 'activo', 'ValidoParaFacturar', 'descripcion', 'clasificacion', null],
            ];
        }
    }

    public function getCostDetails($id): JsonResponse
    {
        $reportes = Reporte::where('centro_costo_id', $id)->get();
        $viaticos = Viatico::where('centro_costo_id', $id)->sum('saldo');

        $ctc = new CentroTableController();
        [$reportsResult, $mano_obra_estimada] = $ctc->MultiplicarPorSalario($reportes);

        return response()->json([
            'mano_de_obra' => $mano_obra_estimada,
            'viaticos' => $viaticos,
            'total' => $mano_obra_estimada + $viaticos,
        ]);
    }

    public function store(CentroCostoRequest $request)
    {
        DB::beginTransaction();
        try {
            $centroCostos = new centroCosto;
            $request->merge(['ValidoParaFacturar' => 1]);
            $centroCostos->fill($request->all());
            $centroCostos->save();
            if ($request->selectedUsers) {
                $centroCostos->users()->sync($request->selectedUsers);
            }
            DB::commit();
            return back()->with('success', __('app.label.created_successfully', ['name' => $centroCostos->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.created_error', ['name' => __('app.label.centroCostos')]) . $th->getMessage());
        }
    }

    public function show($id)
    {
        $helperSelect = new HelperControllerSelect();
        $numberPermissions = \App\helpers\zzloggingcrud::zilefLogTrace();
        $Reportes = Reporte::query();

        $titulo = __('app.label.Reportes');
        $permissions = Auth()->user()->roles->pluck('name')[0];
        $Reportes->Where('centro_costo_id', $id);
        $valoresSelectConsulta = CentroCosto::orderBy('nombre')->get();
        $IntegerDefectoSelect = $valoresSelectConsulta->first()->id;
        foreach ($valoresSelectConsulta as $value) {
            $valoresSelect[] = [
                'label' => $value->nombre,
                'value' => (int)($value->id),
            ];
            $showSelect[(int)($value->id)] = $value->nombre;
        }
        $usuariosSelectConsulta = User::orderBy('name')->get();
        foreach ($usuariosSelectConsulta as $value) {
            $showUsers[(int)($value->id)] = $value->name;
        }

        if ($numberPermissions !== 1) {
            $titulo = MyhelpQuincena::CalcularTituloQuincena($permissions);
            $Reportes->orderBy('fecha_ini');
        }
        $perPage = 100;

        return Inertia::render('Reportes/Index', [
            'title' => $titulo,
            'filters' => null,
            'perPage' => (int)$perPage,
            'fromController' => $Reportes->paginate($perPage),
            'breadcrumbs' => [['label' => __('app.label.Reportes'), 'href' => route('Reportes.index')]],
            'nombresTabla' => $this->getNombresTabla(),
            'valoresSelect' => $valoresSelect,
            'showSelect' => $showSelect,
            'IntegerDefectoSelect' => $IntegerDefectoSelect,
            'showUsers' => $showUsers,
            'sumhoras_trabajadas' => $Reportes->sum('horas_trabajadas'),
            'numberPermissions' => $numberPermissions,
            'losSelect' => $helperSelect->DependenciasCentro('zona'),
        ]);
    }

    public function destroy($id): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $centroCostos = CentroCosto::findOrFail($id);
            $centroCostos->delete();
            DB::commit();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => $centroCostos->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.centroCostos')]) . $th->getMessage());
        }
    }

    /**
     * @throws \Throwable
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => ['required', Rule::unique('centro_costos', 'nombre')->ignore((int)$id)],
        ], [
            'nombre.unique' => 'El nombre ya está en uso.'
        ]);
        DB::beginTransaction();
        try {
            $centroCosto = centroCosto::findOrFail($id);
            $this->SonSelect($request, ['zona_id']);

            $centroCosto->update($request->all());

            $rawSelected = $request->input('selectedUsers', []);
            $CheckedIDs = [];
            foreach ($rawSelected as $key => $val) {
                if ($val === true || $val === 'true' || $val === 1 || $val === "1") {
                    $CheckedIDs[] = $key; // Case: { "ID": true }
                } elseif (is_numeric($val) && is_int($key)) {
                    $CheckedIDs[] = $val; // Case: [ID, ID]
                }
            }

            $listaSupervisores = $request->input('listaSupervisores', []);
            $IDsValidos = array_column($listaSupervisores, 'id');
            $IDsFinales = array_intersect($CheckedIDs, $IDsValidos);

            $supervisoresActuales = $centroCosto->supervisores()->pluck('users.id')->toArray();
            $centroCosto->users()->detach($supervisoresActuales);
            $centroCosto->users()->attach($IDsFinales);
            DB::commit();

            return redirect()->route('CentroCostos.index')->with('success', __('app.label.updated_successfully', ['name' => $centroCosto->nombre]) . ' Espere que la página se refresque');
//            return back()->with('success', __('app.label.updated_successfully', ['name' => $centroCosto->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            $mensajeError = $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile();
            dd($mensajeError);
            return back()->with('error', __('app.label.updated_error', ['name' => __('app.label.centro')]));
        }
    }

    private function SonSelect(Request $request, array $selectinput)
    {
        foreach ($selectinput as $item) {
            $valor = $request->{$item}['value'] ?? null;
            $request->merge([$item => $valor]);
        }
    }
}
