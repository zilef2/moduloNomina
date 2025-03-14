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
use App\Models\User;
use App\Models\zona;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

#[AllowDynamicProperties]
class CentroCostosController extends Controller {
    private int $segundosActualiPresupuesto;
    private int $segundosActuSupervisores;

    public function __construct() {
        session(['parametros' => Parametro::Find(1)]);

//        $this->segundosActualiPresupuesto = 1;
        $this->segundosActualiPresupuesto = 60 * 30;

        $this->segundosActuSupervisores = 60 * 5;
//        $this->segundosActuSupervisores = 1;
        $this->centroCostosAll = CentroCosto::all();
        $this->ZonasAll = zona::all();
        $this->parametros = Parametro::find(1);

    }

    public function index(Request $request): Response {
        $helperSelect = new HelperControllerSelect();
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, 'centro costos')); //0:error, 1:estudiante,  2: profesor, 3:++ )
        $this->ActualizarPresupuesto();
        $centroCostos = $this->MapearClasePP($numberPermissions, $request);

        $listaSupervisores = User::whereHas('roles', function ($q) {
            $q->where('name', 'supervisor');
        })->get();

        $perPage = $request->has('perPage') ? $request->perPage : 50;

        return Inertia::render('CentroCostos/Index', [ //carpeta
            'fromController' => $this->PerPageAndPaginate($request, $centroCostos, $perPage),
            'perPage' => (int)$perPage,
            'breadcrumbs' => [['label' => __('app.label.CentroCostos'), 'href' => route('CentroCostos.index')]],
            'title' => __('app.label.CentroCostos'),
            'filters' => $request->all(['search', 'field', 'order', 'search2', 'searchSCC']),
            'nombresTabla' => $this->getNombresTabla(),
            'listaSupervisores' => $listaSupervisores,
            'numberPermissions' => $numberPermissions,
            'losSelect' => $helperSelect->DependenciasCentro('zona'),

        ]);
    }


    private function ActualizarPresupuesto(): void {
        $cacheKey = 'ultima_llamada_cc_index';
        $ultimaLlamada = Cache::get($cacheKey);
        $tiempoActual = now();
        if ($tiempoActual->diffInSeconds($ultimaLlamada) >= ($this->segundosActualiPresupuesto)) {
            $anio = date('Y');
            $mes = date('m');
            foreach ($this->centroCostosAll as $item) {
                /** @var CentroCosto $item */
                $item->actualizarEstimado($anio, $mes, $this->parametros);
            }
            // Actualizar el tiempo de la última llamada en caché
            Cache::put($cacheKey, $tiempoActual);
        }
    }

    public function MapearClasePP($numberPermissions, $request) {
        $centroCostos = centroCosto::query();
        $AUuser = Myhelp::AuthU();
        $this->limitadorCentrosParaVer = 50;
        $busqueda = false;
        $searchSCC = $request->has('searchSCC');

        if (
            $request->search //nombre o descrip
            || $searchSCC //nombre supervisor
            || $request->search2  //ver_todos
            || $request->search3  //zona
        ) {

            $busqueda = true;
            if (!Cache::has('centro_costos_busqueda')) { //si no ha buscado anteriormente, limpie cache
                $this->limitadorCentrosParaVer = 500;
                $this->actualizarcache();
            }
            $centroCostos->orderBy('created_at', 'DESC');
        } else {
            Cache::forget('centro_costos_busqueda'); // Olvide que hay una búsqueda activa

            if ($request->has(['field', 'order'])) {
                $this->limitadorCentrosParaVer = 500;

                if ($request->field === 'Zouna') {
                    $centroCostos = $centroCostos
                        ->leftJoin('zonas', 'centro_costos.zona_id', '=', 'zonas.id')
                        ->orderByRaw('CASE WHEN zonas.nombre IS NULL THEN 1 ELSE 0 END, zonas.nombre ' . $request->order)
                        ->select('centro_costos.*');
                } else {
                    if ($request->field === 'nombre') {
                        $centroCostos = $centroCostos->orderByRaw('CAST(nombre AS UNSIGNED) ' . $request->order);
                    } else {
                        $centroCostos->orderBy($request->field, $request->order);
                    }
                }
                $this->actualizarcache();

            } else {
                $centroCostos->orderBy('activo', 'DESC')
                    ->orderBy('mano_obra_estimada', 'DESC');
            }
        }


        $supervisores = User::UsersWithRol('supervisor')->get();
        $NotMyCentros = $AUuser->NotMyCentros($numberPermissions);
        $centroCostos = $centroCostos->WhereNotIn('id', $NotMyCentros);
        if ($request->has(['searchSCC']) || $request->has(['search2']) || $request->has(['search'])) {
            $this->actualizarcache();
        }

        if ($busqueda) {
            $centroCostos = $this->getFilter($searchSCC, $request, $centroCostos);
        }
//        dd($centroCostos->get());
//        $centroCostos = Cache::remember('centro_costos', $this->segundosActuSupervisores,
//            function () use ($centroCostos) {
//
//                Log::info('Cache miss: recalculating centro_costos');
//                return $centroCostos->limit($this->limitadorCentrosParaVer)->get()
//                    ->map(callback: function (CentroCosto $centroCosto) {
//                        
//                    $centroCosto->supervi = implode(',', $centroCosto->ArrayListaSupervisores());
//                    return $centroCosto;
//                })->filter();
//            });

        return $centroCostos;
    }

    //deep1

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     */
    public
    function actualizarcache() {
        Cache::forget('centro_costos'); // Borra la caché normal para búsquedas nuevas
        Cache::put('centro_costos_busqueda', true); // Indica que hay una búsqueda activa
        $this->segundosActualiPresupuesto = 0;
    }

    /**
     * @param $searchSCC
     * @param $request
     * @param mixed $centroCostos
     * @return mixed
     */
    public function getFilter($searchSCC, $request, mixed $centroCostos): mixed {
        if ($searchSCC) {
            $PosiblesSupervisores = User::UsersWithRol('supervisor')
                ->Where('name', 'like', '%' . $request->searchSCC . '%')
                ->get();

            //se filtra los cc asociados el authUser
            $centroCostos = $centroCostos->filter(function (CentroCosto $centro) use ($PosiblesSupervisores) {
                $ArrrayCentrosids = $centro->ArraySupervisores($centro->id, $PosiblesSupervisores);
                foreach ($ArrrayCentrosids as $index => $Centroid) {
                    if (in_array($centro->id, $Centroid)) {
                        return true;
                    }
                }
                return false;
            });
        }

        if ($request->search)
            $centroCostos = $centroCostos->Where('nombre', 'like', '%' . $request->search . '%');

        if ($request->search3 && $request->search3['value'])
            $centroCostos = $centroCostos->where('zona_id', $request->search3['value']);

        return $centroCostos;
    }

//    public function PerPageAndPaginate($request, $modelo, $perPage): LengthAwarePaginator {
//        $page = request('page', 1); // Current page number
//        return new LengthAwarePaginator(
//            $modelo->forPage($page, $perPage),
//            $modelo->count(),
//            $perPage,
//            $page,
//            ['path' => request()->url()]
//        );
//    }
    public function PerPageAndPaginate($request, $modelo, $perPage): LengthAwarePaginator {
        return $modelo->paginate($perPage);
    }

    /**
     * @return array
     */
    public function getNombresTabla(): array {
        $permissions = Auth()->user()->roles->pluck('name')[0];
        if ($permissions === 'empleado') { //admin | administrativo
            $nombresTabla = [//[0]: como se ven //[1] como es la BD
                ['#', 'nombre'],
                [null, 'nombre'],
            ];
        } else {
            $nombresTabla = [//[0]: como se ven //[1] como es la BD
                ['Acciones', '#', 'nombre', 'Mano obra estimada', 'zona', 'Supervisores', 'activo', 'Facturar', 'descripcion', 'clasificacion', '# usuarios'],
                [null, null, 'nombre', 'mano_obra_estimada', 'Zouna', null, 'activo', 'ValidoParaFacturar', 'descripcion', 'clasificacion', null],
            ];
        }
        return $nombresTabla;
    }

    public function create() {}

    public function store(CentroCostoRequest $request) {
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' |centro de Costos| ')); //0:error, 1:estudiante,  2: profesor, 3:++ )
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
            $this->actualizarcache();
            return back()->with('success', __('app.label.created_successfully', ['name' => $centroCostos->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();

            return back()->with('error', __('app.label.created_error', ['name' => __('app.label.centroCostos')]) . $th->getMessage());
        }
    }

    public
    function show($id) {
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' |centro de Costos| ')); //0:error, 1:estudiante,  2: profesor, 3:++ )
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

    public
    function edit($id) {
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' |centro de Costos| ')); //0:error, 1:estudiante,  2: profesor, 3:++ )
        $centroCostos = centroCosto::findOrFail($id);

        return Inertia::render('centroCostos.edit', ['centroCostos' => $centroCostos]);
    }

    public
    function destroy($id): RedirectResponse {
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' |centro de Costos| ')); //0:error, 1:estudiante,  2: profesor, 3:++ )
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

    public
    function AproxDestroy() {
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' |AproxDestroy centro de Costos| ')); //0:error, 1:estudiante,  2: profesor, 3:++ )
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

    public
    function update(Request $request, $id) {
        $validatedData = $request->validate([
            'nombre' => [
                'required',
                Rule::unique('centro_costos', 'nombre')->ignore((int)$id),
            ],
        ], [
            'nombre.unique' => 'El nombre ya está en uso.',
        ]);
        
        DB::beginTransaction();
        MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' | centro de Costos | '));

        try {
            $centroCosto = centroCosto::findOrFail($id);
//            if ($centroCosto->nombre == 'Disponibilidad') {
//                Myhelp::EscribirEnLog($this, ' | Disponibilidad no pudo ser cambiada | ');
//
//                return back()->with('error', 'Este centro de costos esta bloqueado');
//            }

            $this->SonSelect($request, ['zona_id']);
            $centroCosto->nombre = $request->nombre;
            $centroCosto->activo = $request->activo;
            $centroCosto->descripcion = $request->descripcion;
            $centroCosto->clasificacion = $request->clasificacion;
            $centroCosto->ValidoParaFacturar = $request->ValidoParaFacturar;
            $centroCosto->zona_id = $request->zona_id;
            $centroCosto->save();

            $IDsSeleccionados = [];
            foreach ($request->listaSupervisores as $index => $supervisor) {
                if (isset($request->selectedUsers[$index]) && $request->selectedUsers[$index]) {
                    $IDsSeleccionados[] = $supervisor['id'];
                }
            }
            $centroCosto->users()->sync($IDsSeleccionados);

            $this->actualizarcache();

            DB::commit();

            return back()->with('success', __('app.label.updated_successfully', ['name' => $centroCosto->nombre]));
        } catch (\Throwable $th) {
            $mensajeErrorTH = $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile();
            DB::rollback();
            Myhelp::EscribirEnLog($this, ' UPDATE centro costos', $mensajeErrorTH, false, 1);

            return back()->with('error', __('app.label.created_error', ['name' => __('app.label.centroCostos')]) . $mensajeErrorTH);
        }
    }

    private
    function SonSelect(Request $request, array $selectinput) {
        foreach ($selectinput as $index => $item) {
            $valor = $request->{$item}['value'] ?? null;
            $request->merge([$item => $valor]);
        }
    }
}
