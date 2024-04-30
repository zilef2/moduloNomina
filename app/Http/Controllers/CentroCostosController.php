<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\Http\Controllers\Controller;

use App\Models\CentroCosto;
use App\Http\Requests\CentroCostoRequest;
use App\Models\Reporte;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Opcodes\LogViewer\Facades\Cache;

class CentroCostosController extends Controller {

    public function MapearClasePP(&$centroCostos, $numberPermissions,$request)
    {
        $AUuser = Myhelp::AuthU();
        $busqueda =false;
        if ($request->has('search')) {
            $busqueda =true;
//            $centroCostos
//                ->orWhere('nombre', 'LIKE', "%" . $request->search . "%")
////                ->orWhere('descripcion', 'LIKE', "%" . $request->search . "%")
//            ;
        }
        if ($request->has(['field', 'order'])) {
            $centroCostos->orderBy($request->field, $request->order);
        }else{
            $centroCostos->orderBy('mano_obra_estimada','DESC');
        }

        $centroCostos = $centroCostos->get()->map(function ($centroCosto) use ($numberPermissions,$AUuser,$busqueda) {
            if ($numberPermissions === 3) {
                $objetoDelUser = $AUuser->ArrayCentrosID();
                if (in_array($centroCosto->id,$objetoDelUser)) return null;
            }

            $centroCosto->cuantoshijos = count($centroCosto->users);
            $centroCosto->supervi = implode(',',$centroCosto->ArrayListaSupervisores($centroCosto->id));
            $centroCosto->supervi1 = $centroCosto->ArrayListaSupervisores($centroCosto->id)[0] ?? '';
            $centroCosto->supervi2 = $centroCosto->ArrayListaSupervisores($centroCosto->id)[1] ?? '';
            $centroCosto->supervi3 = $centroCosto->ArrayListaSupervisores($centroCosto->id)[2] ?? '';
            return $centroCosto;
        })->filter();

        if ($busqueda) {
//            $centroCostos = $centroCostos->Where('supervi1', 'LIKE', "%" . $request->search . "%")
//            ;
//            $centroCostos = array_filter($centroCostos, function($centro) use ($request) {
//                return strpos($centro->supervi1, $request->search) !== false;
//            });
            $centroCostos = $centroCostos->filter(function ($centro) use ($request) {
                return strpos($centro->nombre, $request->search) !== false;
            });
            dd($centroCostos);
        }

        // dd($centroCostos);
    }
    public function index(Request $request) {
        $numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, 'centro costos')); //0:error, 1:estudiante,  2: profesor, 3:++ )

        $cacheKey = 'ultima_llamada_cc_index';
        $ultimaLlamada = Cache::get($cacheKey);
        $tiempoActual = now();
        // Verificar si la función se ha llamado antes y si han pasado al menos 1 minutos
        if (!$ultimaLlamada || $tiempoActual->diffInMinutes($ultimaLlamada) >= 1) {
            $centroCostosAll = CentroCosto::all();
            foreach ($centroCostosAll as $index => $item) {
                $item->actualizarEstimado();
            }
            // Actualizar el tiempo de la última llamada en caché
            Cache::put($cacheKey, $tiempoActual);
        }

        //<editor-fold desc="serach, order, mapear y paginar">
        $centroCostos = centroCosto::query();
        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $this->MapearClasePP($centroCostos, $numberPermissions,$request);

        $permissions = Auth()->user()->roles->pluck('name')[0];
        if($permissions === "empleado") { //admin | administrativo
            $nombresTabla =[//[0]: como se ven //[1] como es la BD
                ["#","nombre"],
                [null,"nombre"]
            ];
        }else{
            $nombresTabla =[//[0]: como se ven //[1] como es la BD
                ["Acciones","#","nombre","Mano obra estimada","usuarios","Supervisor"],
                [null,null,"nombre","mano_obra_estimada",null,null]
            ];
        }

        $page = request('page', 1); // Current page number
        $total = $centroCostos->count();
        $paginated = new LengthAwarePaginator(
            $centroCostos->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url()]
        );
        //</editor-fold>

        return Inertia::render('CentroCostos/Index', [ //carpeta
            'title'          =>  __('app.label.CentroCostos'),
            'filters'        =>  $request->all(['search', 'field', 'order']),
            'perPage'        =>  (int) $perPage,
            'fromController' =>  $paginated,
            'breadcrumbs'    =>  [['label' => __('app.label.CentroCostos'), 'href' => route('CentroCostos.index')]],
            'nombresTabla'   =>  $nombresTabla,
        ]);
    }

    public function create() { }

    public function store(CentroCostoRequest $request) {
        $numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' |centro de Costos| ')); //0:error, 1:estudiante,  2: profesor, 3:++ )
        DB::beginTransaction();
        try {
            $centroCostos = new centroCosto;
            $centroCostos->nombre = $request->nombre;
            $centroCostos->save();
            DB::commit();
            return back()->with('success', __('app.label.created_successfully', ['name' => $centroCostos->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.created_error', ['name' => __('app.label.centroCostos')]) . $th->getMessage());
        }
    }

    public function show($id) {
        $numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' |centro de Costos| ')); //0:error, 1:estudiante,  2: profesor, 3:++ )
        $Reportes = Reporte::query();

        $titulo = __('app.label.Reportes');
        $permissions = Auth()->user()->roles->pluck('name')[0];
        $Reportes->Where('centro_costo_id',$id);
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

        if($numberPermissions === 1) { //1 : empleado | 2 : administrativo | 3 :supervisor
        }else{ // not empleado
            $titulo = $this->CalcularTituloQuincena($permissions);
            $Reportes->orderBy('fecha_ini'); $perPage = 25;

            $nombresTabla =[//0: como se ven //1 como es la BD

                ["Acciones","#","Centro costo","Trabajador", "valido",   "inicio","fin","horas trabajadas",  'diurnas', 'nocturnas', 'extra diurnas', 'extra nocturnas', 'dominical diurno', 'dominical nocturno', 'dominical extra diurno', 'dominical extra nocturno', "observaciones"],
                ["b_valido","t_fecha_ini", "t_fecha_fin", "i_horas_trabajadas", 'i_diurnas', 'i_nocturnas', 'i_extra_diurnas', 'i_extra_nocturnas', 'i_dominical_diurno', 'i_dominical_nocturno', 'i_dominical_extra_diurno', 'i_dominical_extra_nocturno',"s_observaciones"], //m for money || t for datetime || d date || i for integer || s string || b boolean
                [null,null,null,null,"b_valido","t_fecha_ini", "t_fecha_fin", "i_horas_trabajadas", 'i_diurnas', 'i_nocturnas', 'i_extra_diurnas', 'i_extra_nocturnas', 'i_dominical_diurno', 'i_dominical_nocturno', 'i_dominical_extra_diurno', 'i_dominical_extra_nocturno',"s_observaciones"] //m for money || t for datetime || d date || i for integer || s string || b boolean
            ];
        }
        $sumhoras_trabajadas = $Reportes->sum('horas_trabajadas');

//        $reporController = new ReportesController();
//        $showSelect = $reporController->losSelect($valoresSelectConsulta, $showUsers,$valoresSelect,$userFiltro);

        return Inertia::render('Reportes/Index', [ //carpeta
            'title'          =>  $titulo,
            'filters'        =>  null,
            'perPage'        =>  (int) $perPage,
            'fromController' =>  $Reportes->paginate($perPage),
            'breadcrumbs'    =>  [['label' => __('app.label.Reportes'), 'href' => route('Reportes.index')]],
            'nombresTabla'   =>  $nombresTabla,

            'valoresSelect'         =>  $valoresSelect,
            'showSelect'            =>  $showSelect,
            'IntegerDefectoSelect'  =>  $IntegerDefectoSelect,
            'showUsers'             =>  $showUsers,
            'sumhoras_trabajadas'   =>  $sumhoras_trabajadas,
            //18dic2023
            'userFiltro'            =>  null,

            //24abril2024
            'quincena'              =>  0,
            'horasemana'            =>  0,
            'horasPersonal'         =>  0,
            'startDateMostrar'      =>  0,
            'endDateMostrar'        =>  0,
            'numberPermissions'     =>  $numberPermissions,
            'ArrayOrdinarias'       =>  0,

            'sumdiurnas'            =>  0,
            'sumnocturnas'          =>  0, 'sumextra_diurnas'              =>  0, 'sumextra_nocturnas'            =>  0, 'sumdominical_diurno'           =>  0, 'sumdominical_nocturno'         =>  0, 'sumdominical_extra_diurno'     =>  0, 'sumdominical_extra_nocturno'   =>  0,
            'horasTrabajadasHoy'    =>  $horasTrabajadasHoy2 ?? [],
            'HorasDeCadaSemana'     =>  0,
            'ArrayHorasSemanales'   =>  0,
        ]);
    }

    public function CalcularTituloQuincena($permissions) {
        $esteMes = date("m");
        $diaquincena = date("d");
        if($permissions === "empleado") { //NO admin | administrativo
            $userid = Auth::user()->id;
            if($diaquincena <= 15){
                $horasTrabajadas = Reporte::WhereMonth('fecha_ini',$esteMes)->WhereDay('fecha_ini','<=',15)
                    ->where('user_id',$userid)
                    ->sum('horas_trabajadas');
            }else{
                $horasTrabajadas = Reporte::WhereMonth('fecha_ini',$esteMes)->WhereDay('fecha_ini','>',15)
                    ->where('user_id',$userid)
                    ->sum('horas_trabajadas');
            }
        }else{
            if($diaquincena <= 15){
                $horasTrabajadas = Reporte::WhereMonth('fecha_ini',$esteMes)->WhereDay('fecha_ini','<=',15)
                    ->sum('horas_trabajadas');
            }else{
                $horasTrabajadas = Reporte::WhereMonth('fecha_ini',$esteMes)->WhereDay('fecha_ini','>',15)
                    ->sum('horas_trabajadas');
            }
        }

        return 'Horas trabajadas quincena: '.$horasTrabajadas;
    }


    public function edit($id) {
        $numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' |centro de Costos| ')); //0:error, 1:estudiante,  2: profesor, 3:++ )
        $centroCostos = centroCosto::findOrFail($id);
        return Inertia::render('centroCostos.edit',['centroCostos'=>$centroCostos]);
    }

    public function update(CentroCostoRequest $request, $id) {
         DB::beginTransaction();
         $numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' |centro de Costos| ')); //0:error, 1:estudiante,  2: profesor, 3:++ )

        try {
            $centroCostos = centroCosto::findOrFail($id);
            $centroCostos->nombre = $request->input('nombre');
            $centroCostos->save();
            DB::commit();
            return back()->with('success', __('app.label.created_successfully', ['name' => $centroCostos->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.created_error', ['name' => __('app.label.centroCostos')]) . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' |centro de Costos| ')); //0:error, 1:estudiante,  2: profesor, 3:++ )
        DB::beginTransaction();
        try {
            if($numberPermissions > 8){

                $centroCostos = CentroCosto::findOrFail($id);
                $centroCostos->delete();

                DB::commit();
                return back()->with('success', __('app.label.deleted_successfully', ['name' => $centroCostos->nombre]));
            }else{
                return back()->with('error', 'No tiene permisos para borrar un centro de costos');
            }
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.centroCostos')]) . $th->getMessage());
        }
    }
}
