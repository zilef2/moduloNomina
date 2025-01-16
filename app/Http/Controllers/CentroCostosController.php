<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\MyhelpQuincena;
use App\Http\Requests\CentroCostoRequest;
use App\Models\CentroCosto;
use App\Models\Parametro;
use App\Models\Reporte;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CentroCostosController extends Controller
{
    private int $minutosActualizarPresupueto;

    public function __construct()
    {
        session(['parametros' => Parametro::Find(1)]);
        $this->minutosActualizarPresupueto = 15;
    }

    public function MapearClasePP($numberPermissions, $request)
    {
        $centroCostos = centroCosto::query();

        $AUuser = Myhelp::AuthU();
        $busqueda = false;
        if ($request->has('search')) {
            $busqueda = true;
        }
        $searchSCC = $request->has('searchSCC');

        if ($request->has(['field', 'order'])) {
            $centroCostos->orderBy($request->field, $request->order);
        } else {
            $centroCostos
                ->orderBy('activo', 'DESC')
                ->orderBy('mano_obra_estimada', 'DESC');
        }
        $supervisores = User::UsersWithRol('supervisor')->get();

        $centroCostos = Cache::remember('centro_costos', $this->minutosActualizarPresupueto, function () use ($centroCostos, $numberPermissions, $AUuser, $supervisores) {
            Log::info('Cache miss: recalculating centro_costos');
            return $centroCostos->get()->map(function ($centroCosto) use ($supervisores, $numberPermissions, $AUuser) {
                if ($numberPermissions === 3) { //todo: que es 3
                    $objetoDelUser = $AUuser->ArrayCentrosID();
                    if (in_array($centroCosto->id, $objetoDelUser)) {
                        return null;
                    }
                }

                $ArrayListaSupervi = Cache::remember("ArrayListaSupervisores_{$centroCosto->id}", ($this->minutosActualizarPresupueto*4), function () use ($centroCosto, $supervisores) {
                    return $centroCosto->ArrayListaSupervisores($supervisores);
                });
                $centroCosto->cuantoshijos = count($centroCosto->users);
                $centroCosto->todos = $centroCosto->ArraySupervIDs();
                $centroCosto->supervi = implode(',', $ArrayListaSupervi);

                return $centroCosto;
            })->filter();
        });

        if ($busqueda) {
            $centroCostos = $centroCostos->filter(function ($centro) use ($request) {
                return str_contains(strtolower($centro->nombre), strtolower($request->search));
            });
        }
        if ($searchSCC) {
            $PosiblesSupervisores = User::UsersWithRol('supervisor')
                ->Where('name', 'like', '%' . $request->searchSCC . '%')
                ->get();
            $centroCostos = $centroCostos->filter(function ($centro) use ($PosiblesSupervisores) {
                $ArrrayCentrosids = $centro->ArraySupervisores($centro->id, $PosiblesSupervisores);
                foreach ($ArrrayCentrosids as $index => $Centroid) {
                    if (in_array($centro->id, $Centroid)) {
                        return true;
                    }
                }

                return false;
            });
        }
        
        return $centroCostos;
    }

    //deep1
    private function ActualizarPresupuesto(): void
    {
        $cacheKey = 'ultima_llamada_cc_index';
        $ultimaLlamada = Cache::get($cacheKey);
        $tiempoActual = now();
        $centroCostosAll = CentroCosto::all();
        if ($tiempoActual->diffInMinutes($ultimaLlamada) >= ($this->minutosActualizarPresupueto)) {
            $anio = date('Y');
            $mes = date('m');
            foreach ($centroCostosAll as $item) {
                $item->actualizarEstimado($anio, $mes);
            }
        }
        // Actualizar el tiempo de la última llamada en caché
        Cache::put($cacheKey, $tiempoActual);
    }

    public function index(Request $request): \Inertia\Response
    {
        $numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, 'centro costos')); //0:error, 1:estudiante,  2: profesor, 3:++ )
        $this->ActualizarPresupuesto();
        //<editor-fold desc="serach, order, mapear y paginar">
        $perPage = $request->has('perPage') ? $request->perPage : 50;
        $centroCostos = $this->MapearClasePP($numberPermissions, $request);
        $nombresTabla = $this->getNombresTabla();

        $page = request('page', 1); // Current page number
        $paginated = new LengthAwarePaginator(
            $centroCostos->forPage($page, $perPage),
            $centroCostos->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );
        //</editor-fold>
        $listaSupervisores = User::whereHas('roles', function ($q) {
            $q->where('name', 'supervisor');
        })->get();

        return Inertia::render('CentroCostos/Index', [ //carpeta
            'breadcrumbs' => [['label' => __('app.label.CentroCostos'), 'href' => route('CentroCostos.index')]],
            'title' => __('app.label.CentroCostos'),
            'filters' => $request->all(['search', 'field', 'order']),
            'perPage' => (int)$perPage,
            'fromController' => $paginated,
            'nombresTabla' => $nombresTabla,
            'listaSupervisores' => $listaSupervisores,
        ]);
    }

    public function create()
    {
    }

    public function store(CentroCostoRequest $request)
    {
        $numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' |centro de Costos| ')); //0:error, 1:estudiante,  2: profesor, 3:++ )
        DB::beginTransaction();
        try {
            $centroCostos = new centroCosto;
            //            $centroCostos->nombre = $request->nombre;
            //            $request->merge(['tipo_gasto_id' => $request->tipo_gasto['id']]);
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

    //todo: hacer una vista nueva
    public function show($id)
    {
        $numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' |centro de Costos| ')); //0:error, 1:estudiante,  2: profesor, 3:++ )
        $Reportes = Reporte::query();

        $titulo = __('app.label.Reportes');
        $permissions = Auth()->user()->roles->pluck('name')[0];
        $Reportes->Where('centro_costo_id', $id);
        $valoresSelectConsulta = CentroCosto::orderBy('nombre')->get();
        $IntegerDefectoSelect = $valoresSelectConsulta->first()->id;
        foreach ($valoresSelectConsulta as $value) {
            $valoresSelect[] = [
                'label' => $value->nombre, //centro de costos
                'value' => (int)($value->id),
            ];
            $showSelect[(int)($value->id)] = $value->nombre;
        }
        $usuariosSelectConsulta = User::orderBy('name')->get();
        foreach ($usuariosSelectConsulta as $value) {
            $showUsers[(int)($value->id)] = $value->name;
        }

        if ($numberPermissions === 1) { //1 : empleado | 2 : administrativo | 3 :supervisor

        } else { // not empleado
            $titulo = MyhelpQuincena::CalcularTituloQuincena($permissions);
            $Reportes->orderBy('fecha_ini');
            $perPage = 100;

            $nombresTabla = [//0: como se ven //1 como es la BD

                ['Acciones', '#', 'Centro costo', 'Trabajador', 'valido', 'inicio', 'fin', 'horas trabajadas', 'diurnas', 'nocturnas', 'extra diurnas', 'extra nocturnas', 'dominical diurno', 'dominical nocturno', 'dominical extra diurno', 'dominical extra nocturno', 'observaciones'],
                ['b_valido', 't_fecha_ini', 't_fecha_fin', 'i_horas_trabajadas', 'i_diurnas', 'i_nocturnas', 'i_extra_diurnas', 'i_extra_nocturnas', 'i_dominical_diurno', 'i_dominical_nocturno', 'i_dominical_extra_diurno', 'i_dominical_extra_nocturno', 's_observaciones'], //m for money || t for datetime || d date || i for integer || s string || b boolean
                [null, null, null, null, 'b_valido', 't_fecha_ini', 't_fecha_fin', 'i_horas_trabajadas', 'i_diurnas', 'i_nocturnas', 'i_extra_diurnas', 'i_extra_nocturnas', 'i_dominical_diurno', 'i_dominical_nocturno', 'i_dominical_extra_diurno', 'i_dominical_extra_nocturno', 's_observaciones'], //m for money || t for datetime || d date || i for integer || s string || b boolean
            ];
        }
        $sumhoras_trabajadas = $Reportes->sum('horas_trabajadas');

        //        $reporController = new ReportesController();
        //        $showSelect = $reporController->losSelect($valoresSelectConsulta, $showUsers,$valoresSelect,$userFiltro);
        return Inertia::render('Reportes/Index', [ //carpeta
            'title' => $titulo,
            'filters' => null,
            'perPage' => (int)$perPage,
            'fromController' => $Reportes->paginate($perPage),
            'breadcrumbs' => [['label' => __('app.label.Reportes'), 'href' => route('Reportes.index')]],
            'nombresTabla' => $nombresTabla,

            'valoresSelect' => $valoresSelect,
            'showSelect' => $showSelect,
            'IntegerDefectoSelect' => $IntegerDefectoSelect,
            'showUsers' => $showUsers,
            'sumhoras_trabajadas' => $sumhoras_trabajadas,
            //18dic2023
            'userFiltro' => -1,

            //24abril2024
            'quincena' => 0,
            'horasemana' => 0,
            'horasPersonal' => 0,
            'startDateMostrar' => 0,
            'endDateMostrar' => 0,
            'numberPermissions' => $numberPermissions,
            'ArrayOrdinarias' => 0,

            'sumdiurnas' => 0,
            'sumnocturnas' => 0, 'sumextra_diurnas' => 0, 'sumextra_nocturnas' => 0, 'sumdominical_diurno' => 0, 'sumdominical_nocturno' => 0, 'sumdominical_extra_diurno' => 0, 'sumdominical_extra_nocturno' => 0,
            'horasTrabajadasHoy' => $horasTrabajadasHoy2 ?? [],
            'HorasDeCadaSemana' => 0,
            'ArrayHorasSemanales' => 0,
        ]);
    }

    public function edit($id)
    {
        $numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' |centro de Costos| ')); //0:error, 1:estudiante,  2: profesor, 3:++ )
        $centroCostos = centroCosto::findOrFail($id);

        return Inertia::render('centroCostos.edit', ['centroCostos' => $centroCostos]);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nombre' => [
                'required',
                Rule::unique('centro_costos', 'nombre')->ignore((int)$id),
            ],
        ], [
            'nombre.unique' => 'El nombre ya está en uso.',
        ]);
        DB::beginTransaction();
        Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' | centro de Costos | '));

        try {
            $centroCosto = centroCosto::findOrFail($id);
            if ($centroCosto->nombre == 'Disponibilidad') {
                Myhelp::EscribirEnLog($this, ' | Disponibilidad no pudo ser cambiada | ');

                return back()->with('error', 'Este centro de costos esta bloqueado');
            }

            $centroCosto->nombre = $request->nombre;
            $centroCosto->activo = $request->activo;
            $centroCosto->descripcion = $request->descripcion;
            $centroCosto->clasificacion = $request->clasificacion;
            $centroCosto->ValidoParaFacturar = $request->ValidoParaFacturar;
            $centroCosto->save();
            $IDsSeleccionados = [];
            foreach ($request->listaSupervisores as $index => $supervisor) {
                if (isset($request->selectedUsers[$index]) && $request->selectedUsers[$index]) {
                    $IDsSeleccionados[] = $supervisor['id'];
                }
            }
            $centroCosto->users()->sync($IDsSeleccionados);

            DB::commit();

            return back()->with('success', __('app.label.updated_successfully', ['name' => $centroCosto->nombre]));
        } catch (\Throwable $th) {
            $mensajeErrorTH = $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile();
            DB::rollback();
            Myhelp::EscribirEnLog($this, ' UPDATE centro costos', $mensajeErrorTH, false, 1);

            return back()->with('error', __('app.label.created_error', ['name' => __('app.label.centroCostos')]) . $mensajeErrorTH);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     */
    public function destroy($id): RedirectResponse
    {
        $numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' |centro de Costos| ')); //0:error, 1:estudiante,  2: profesor, 3:++ )
        DB::beginTransaction();
        try {
            if ($numberPermissions > 8) {

                $centroCostos = CentroCosto::findOrFail($id);
                $centroCostos->delete();

                DB::commit();

                return back()->with('success', __('app.label.deleted_successfully', ['name' => $centroCostos->nombre]));
            } else {
                return back()->with('error', 'No tiene permisos para borrar un centro de costos');
            }
        } catch (\Throwable $th) {
            DB::rollback();
            Myhelp::EscribirEnLog($this, ' destroy centro costos', $th->getMessage(), false, 1);

            return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.centroCostos')]) . $th->getMessage());
        }
    }

    public function AproxDestroy()
    {
        $numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' |AproxDestroy centro de Costos| ')); //0:error, 1:estudiante,  2: profesor, 3:++ )
        DB::beginTransaction();
        try {
            if ($numberPermissions > 8) {

                $centroCostos = CentroCosto::all();
                foreach ($centroCostos as $index => $centroCosto) {

                    $centroCosto->update([
                        'mano_obra_estimada' => 0
                    ]);
                }

                DB::commit();
                $contar = $centroCostos->count();
                echo "modificamos $contar centros";
            } else {
                echo "modificamos nada";
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $thmessa = $th->getMessage();
            Myhelp::EscribirEnLog($this, ' destroy centro costos', $thmessa, false, 1);
            echo "catch:  $thmessa";
        }
    }

    /**
     * @return array
     */
    public function getNombresTabla(): array
    {
        $permissions = Auth()->user()->roles->pluck('name')[0];
        if ($permissions === 'empleado') { //admin | administrativo
            $nombresTabla = [//[0]: como se ven //[1] como es la BD
                ['#', 'nombre'],
                [null, 'nombre'],
            ];
        } else {
            $nombresTabla = [//[0]: como se ven //[1] como es la BD
                ['Acciones', '#', 'nombre', 'Mano obra estimada', 'usuarios', 'Supervisor', 'activo', 'Facturar', 'descripcion', 'clasificacion'],
                [null, null, 'nombre', 'mano_obra_estimada', null, null, 'activo', 'ValidoParaFacturar', 'descripcion', 'clasificacion'],
            ];
        }
        return $nombresTabla;
    }
}
