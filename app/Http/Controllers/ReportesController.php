<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\Reporte;
use App\Http\Requests\ReporteRequest;
use App\Models\CentroCosto;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReportesController extends Controller
{
 /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * valido states = {0 defect | 1 validada | 2 rechazada}
     */
    public function index(Request $request)
    {
        $titulo = __('app.label.Reportes');
        $Authuser = Auth::user();
        $permissions = auth()->user()->roles->pluck('name')[0];
        $Reportes = Reporte::query();
        $valoresSelectConsulta = CentroCosto::orderBy('nombre')->get();
        $IntegerDefectoSelect = $valoresSelectConsulta->first()->id;
        foreach ($valoresSelectConsulta as $value) {
            $valoresSelect[] = [
                'label' => $value->nombre, //centro de costos
                'value' => intval($value->id),
            ];
            $showSelect[intval($value->id)] = $value->nombre;
        }
        $usuariosSelectConsulta = User::orderBy('name')->get();
        foreach ($usuariosSelectConsulta as $value) {
            $showUsers[intval($value->id)] = $value->name;
        }

        $quincena = [];
        
        if($permissions === "operator") { //admin | validador
            $Reportes->whereUser_id($Authuser->id);

            if ($request->has(['field', 'order'])) {
                $Reportes->orderBy($request->field, $request->order);
            }else{
                $Reportes->orderBy('fecha_ini');
            }
            $perPage = $request->has('perPage') ? $request->perPage : 10;

            $nombresTabla =[//0: como se ven //1 como es la BD //2??
                ["Acciones","#","Centro costo","inicio", "fin", "horas trabajadas", "valido", "observaciones"],
                ["t_fecha_ini", "t_fecha_fin", "i_horas_trabajadas", "b_valido", "s_observaciones"], //m for money || t for datetime || d date || i for integer || s string || b boolean 
                [null,null,null,"t_fecha_ini", "t_fecha_fin", "i_horas_trabajadas", "b_valido", "s_observaciones"] //campos ordenables
            ];

        }else{ // not operator
            $quincena = [

                'Primera quincena' => Reporte::whereBetween('fecha_ini',[Carbon::now()->startOfMonth(),Carbon::now()->startOfMonth()->addDays(15)])->count(),
                'Segunda quincena' => Reporte::whereBetween('fecha_ini',[Carbon::now()->startOfMonth()->addDays(15),Carbon::now()->LastOfMonth()])->count(),
            ];
            // dd($quincena);
            // $ReportesEsteMes = Reporte::WhereMonth('fecha_ini',$esteMes)->get()->count();
            $titulo = $this->CalcularTituloQuincena();
            
            if ($request->has('search')) {
                $Reportes->whereMonth('fecha_ini', $request->search);
                $Reportes->OrwhereMonth('fecha_fin', $request->search);
                $Reportes->OrwhereYear('fecha_ini', $request->search);
                $Reportes->OrwhereYear('fecha_fin', $request->search);
                $Reportes->OrwhereDay('fecha_ini', $request->search);
                $Reportes->OrwhereDay('fecha_fin', $request->search);
                // $Reportes->orWhere('fecha_fin', 'LIKE', "%" . $request->search . "%");
            }
            if ($request->has(['field', 'order'])) {
                $Reportes->orderBy($request->field, $request->order);
            }else{
                $Reportes->orderBy('fecha_ini');
            }
            $perPage = $request->has('perPage') ? $request->perPage : 10;

            $nombresTabla =[//0: como se ven //1 como es la BD
                ["Acciones","#","Centro costo","Trabajador", "valido",   "inicio",       "fin",        "horas trabajadas",   "observaciones"],
                ["b_valido","t_fecha_ini", "t_fecha_fin", "i_horas_trabajadas", "s_observaciones"], //m for money || t for datetime || d date || i for integer || s string || b boolean 
                [null,null,null,null,"b_valido","t_fecha_ini", "t_fecha_fin", "i_horas_trabajadas", "s_observaciones"] //m for money || t for datetime || d date || i for integer || s string || b boolean 
            ];
        }


        return Inertia::render('Reportes/Index', [ //carpeta
            'title'          =>  $titulo,
            'filters'        =>  $request->all(['search', 'field', 'order']),
            'perPage'        =>  (int) $perPage,
            'fromController' =>  $Reportes->paginate($perPage),
            'breadcrumbs'    =>  [['label' => __('app.label.Reportes'), 'href' => route('Reportes.index')]],
            'nombresTabla'   =>  $nombresTabla,

            'valoresSelect'   =>  $valoresSelect,
            'showSelect'   =>  $showSelect,
            'IntegerDefectoSelect'   =>  $IntegerDefectoSelect,
            'showUsers'   =>  $showUsers,
            'quincena'   =>  $quincena,
        ]);
    }//fin index

    public function CalcularTituloQuincena() {
        $esteMes = date("m");
        $diaquincena = date("d");
        if($diaquincena >= 15){ //toask: el dia 15 se toma en cuenta para la quincena ? 
            $horasTrabajadas = Reporte::WhereMonth('fecha_ini',$esteMes)->WhereDay('fecha_ini','<=',15)->sum('horas_trabajadas');
        }else{
            $horasTrabajadas = Reporte::WhereMonth('fecha_ini',$esteMes)->WhereDay('fecha_ini','>',15)->sum('horas_trabajadas');
        }
        return 'Horas trabajadas quincena: '.$horasTrabajadas;
    }


    public function create() { }

    public function updatingDate($date) {
        if($date === null || $date == '1969-12-31'){
            return null;
        }
        return date("Y-m-d H:i:s",strtotime($date));
    }

    public function store(ReporteRequest $request)
    {
        DB::beginTransaction();
        try {
            $thisUserId = Auth::User()->id;
            $traslapa = false;
            $traslapa2 = false;

            $fecha_ini = $this->updatingDate($request->fecha_ini);
            $fecha_fin = $this->updatingDate($request->fecha_fin);

            

            $traslapa =  Reporte::WhereBetween('fecha_ini',[$fecha_ini,$fecha_fin])->Where('user_id',$thisUserId)->count();
            $traslapa2 = Reporte::WhereBetween('fecha_fin',[$fecha_ini,$fecha_fin])->Where('user_id',$thisUserId)->count();

            if ($traslapa > 0 || $traslapa2 > 0) {
                return back()->with('error', __('app.label.created_error', ['name' => __('app.label.Reportes')]) . ' Las fechas se solapan');
            } else {
                
                $Reportes = new Reporte;
                $Reportes->fecha_ini = $fecha_ini;
                $Reportes->fecha_fin = $fecha_fin;
                $Reportes->horas_trabajadas = $request->horas_trabajadas;

                $Reportes->diurnas = $request->diurnas;
                $Reportes->nocturnas = $request->nocturnas;
                $Reportes->extra_diurnas = $request->extra_diurnas;
                $Reportes->extra_nocturnas = $request->extra_nocturnas;
                $Reportes->dominical_diurno = $request->dominical_diurno;
                $Reportes->dominical_nocturno = $request->dominical_nocturno;
                $Reportes->dominical_extra_diurno = $request->dominical_extra_diurno;
                $Reportes->dominical_extra_nocturno = $request->dominical_extra_nocturno;

                $Reportes->valido = 0;
                $Reportes->observaciones = $request->observaciones;
                $Reportes->centro_costo_id = $request->centro_costo_id;
                $Reportes->user_id = Auth::user()->id;

                $Reportes->save();
                DB::commit();
                return back()->with('success', __('app.label.created_successfully', ['name' => $Reportes->nombre]));
            }

        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.created_error', ['name' => __('app.label.Reportes')]) . $th->getMessage());
        }
    }//fin store

    public function show($id) { }
    public function edit($id)
    {
        $Reportes = Reporte::findOrFail($id);
        return Inertia::render('Reportes.edit',['Reportes'=>$Reportes]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ReportesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $Reportes = Reporte::findOrFail($id);
            if($request->valido === 1 || $request->valido === true){
                $Reportes->valido = $request->valido;//0 creado //1 aceptado //2 rechazado
            }else{
                $Reportes->fecha_ini = $this->updatingDate($request->fecha_ini);
                $Reportes->fecha_fin = $this->updatingDate($request->fecha_fin);

                $Reportes->horas_trabajadas = $request->horas_trabajadas;

                $Reportes->diurnas = $request->diurnas;
                $Reportes->nocturnas = $request->nocturnas;
                $Reportes->extra_diurnas = $request->extra_diurnas;
                $Reportes->extra_nocturnas = $request->extra_nocturnas;
                $Reportes->dominical_diurno = $request->dominical_diurno;
                $Reportes->dominical_nocturno = $request->dominical_nocturno;
                $Reportes->dominical_extra_diurno = $request->dominical_extra_diurno;
                $Reportes->dominical_extra_nocturno = $request->dominical_extra_nocturno;
                
                $Reportes->valido = $request->valido;//0 creado //1 aceptado //2 rechazado
                $Reportes->observaciones = $request->observaciones;
                $Reportes->centro_costo_id = $request->centro_costo_id;
            }
            $Reportes->save();
            DB::commit();
            return back()->with('success', __('app.label.created_successfully', ['name' => 'Reporte']));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.created_error', ['name' => __('app.label.Reportes')]) . $th->getMessage());
        }
    }

    /**
         * Remove the specified resource from storage.
         *
         * @param  int  $id
         * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        DB::beginTransaction();

        try {
            // si se la rechazaron, tendra que hacer uno nuevo
            $Reportes = Reporte::findOrFail($id);
            if($Reportes->valido == 0){
                $Reportes->delete();
                DB::commit();
                return back()->with('success', __('app.label.deleted_successfully'));
            }else{
                DB::commit();
                return back()->with('warning', __('app.label.not_deleted', ['name' => $Reportes->observaciones]));
            }
            
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.Reportes')]) . $th->getMessage());
        }
    }
}
