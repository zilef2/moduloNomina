<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
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
    public function losSelect(&$valoresSelectConsulta, &$showUsers, &$valoresSelect) {
        $valoresSelectConsulta = CentroCosto::orderBy('nombre')->get();

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

        return $showSelect;

    }

    public function Filtros($request, &$Reportes) {
        
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
                $Reportes->orderByDesc('fecha_ini');
        }
    }

    

    private function CalcularTituloQuincena($numberPermissions,$Authuser) {
        $esteMes = date("m");
        $diaquincena = date("d");
        if($diaquincena >= 15){
            if($numberPermissions == 3){

                $horasTrabajadas = Reporte::Where('centro_costo_id', $Authuser->centro_costo_id)
                    ->WhereMonth('fecha_ini',$esteMes)->WhereDay('fecha_ini','<=',15)->sum('horas_trabajadas');
            }else{
                $horasTrabajadas = Reporte::WhereMonth('fecha_ini',$esteMes)->WhereDay('fecha_ini','<=',15)->sum('horas_trabajadas');

            }
        }else{
            if($numberPermissions == 3){
                $horasTrabajadas = Reporte::Where('centro_costo_id', $Authuser->centro_costo_id)
                    ->WhereMonth('fecha_ini',$esteMes)->WhereDay('fecha_ini','>',15)->sum('horas_trabajadas');
            }else{
                $horasTrabajadas = Reporte::WhereMonth('fecha_ini',$esteMes)->WhereDay('fecha_ini','>',15)->sum('horas_trabajadas');
            }
        }
        return 'Horas trabajadas quincena: '.$horasTrabajadas;
    }

    public function fNombresTabla($numberPermissions ,&$Reportes,$Authuser,$request, &$titulo) {
        
        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();
        $quincena = [];
        if($numberPermissions === 1) { 
            $Reportes->whereUser_id($Authuser->id);

            if ($request->has(['field', 'order'])) {
                $Reportes->orderBy($request->field, $request->order);
            }else{
                $Reportes->orderByDesc('fecha_ini');
            }

            $nombresTabla =[//0: como se ven //1 como es la BD
                ["Acciones","#","Centro costo", "valido",   "inicio","fin","horas trabajadas",  'diurnas', 'nocturnas', 'extra diurnas', 'extra nocturnas', 'dominical diurno', 'dominical nocturno', 'dominical extra diurno', 'dominical extra nocturno', "observaciones"],
                ["b_valido","t_fecha_ini", "t_fecha_fin", "i_horas_trabajadas", 'i_diurnas', 'i_nocturnas', 'i_extra_diurnas', 'i_extra_nocturnas', 'i_dominical_diurno', 'i_dominical_nocturno', 'i_dominical_extra_diurno', 'i_dominical_extra_nocturno',"s_observaciones"], //m for money || t for datetime || d date || i for integer || s string || b boolean 
                [null,null,null,null,"b_valido","t_fecha_ini", "t_fecha_fin", "i_horas_trabajadas", 'i_diurnas', 'i_nocturnas', 'i_extra_diurnas', 'i_extra_nocturnas', 'i_dominical_diurno', 'i_dominical_nocturno', 'i_dominical_extra_diurno', 'i_dominical_extra_nocturno',"s_observaciones"] //m for money || t for datetime || d date || i for integer || s string || b boolean 
            ];

            //# horas de la semana
            $horasemana = Reporte::Where('user_id',$Authuser->id)
                    ->WhereBetween('fecha_ini', [$startDate, $endDate])
                    ->sum('horas_trabajadas');

            //# solo validos
            if ($request->has('soloValidos')) {
                $Reportes->where('valido', true);
            }
            // dd($Reportes);

        }else{ //administrativo, supervisor y admin
            $titulo = $this->CalcularTituloQuincena($numberPermissions,$Authuser);
            $this->Filtros($request,$Reportes);

            $nombresTabla =[//0: como se ven //1 como es la BD
                ["Acciones","#","Centro costo","Trabajador", "valido",   "inicio","fin","horas trabajadas",  'diurnas', 'nocturnas', 'extra diurnas', 'extra nocturnas', 'dominical diurno', 'dominical nocturno', 'dominical extra diurno', 'dominical extra nocturno', "observaciones"],
                ["b_valido","t_fecha_ini", "t_fecha_fin", "i_horas_trabajadas", 'i_diurnas', 'i_nocturnas', 'i_extra_diurnas', 'i_extra_nocturnas', 'i_dominical_diurno', 'i_dominical_nocturno', 'i_dominical_extra_diurno', 'i_dominical_extra_nocturno',"s_observaciones"], //m for money || t for datetime || d date || i for integer || s string || b boolean 
                [null,null,null,null,"b_valido","t_fecha_ini", "t_fecha_fin", "i_horas_trabajadas", 'i_diurnas', 'i_nocturnas', 'i_extra_diurnas', 'i_extra_nocturnas', 'i_dominical_diurno', 'i_dominical_nocturno', 'i_dominical_extra_diurno', 'i_dominical_extra_nocturno',"s_observaciones"] //m for money || t for datetime || d date || i for integer || s string || b boolean 
            ];

            $quincena = [
                'Primera quincena' => Reporte::whereBetween('fecha_ini',[Carbon::now()->startOfMonth(),Carbon::now()->startOfMonth()->addDays(14)])->count(),
                'Segunda quincena' => Reporte::whereBetween('fecha_ini',[Carbon::now()->startOfMonth()->addDays(15),Carbon::now()->LastOfMonth()])->count(),
            ];
            $horasemana = Reporte::WhereBetween('fecha_ini', [$startDate, $endDate])->sum('horas_trabajadas');

            if($numberPermissions == 3){ //supervisor
                $quincena = [
                    'Primera quincena' => Reporte::whereBetween('fecha_ini',[Carbon::now()->startOfMonth(),Carbon::now()->startOfMonth()->addDays(14)])
                        ->Where('centro_costo_id',$Authuser->centro_costo_id)->count(),
                    'Segunda quincena' => Reporte::whereBetween('fecha_ini',[Carbon::now()->startOfMonth()->addDays(15),Carbon::now()->LastOfMonth()])
                        ->Where('centro_costo_id',$Authuser->centro_costo_id)->count(),
                ];
            }else{ //admin y administrativo
                if($numberPermissions == 2){//administrativo
                    $horasemana = Reporte::Where('centro_costo_id', $Authuser->centro_costo_id)
                        ->WhereBetween('fecha_ini', [$startDate, $endDate])
                        ->sum('horas_trabajadas');

                    $Reportes->where('centro_costo_id', $Authuser->centro_costo_id);
                }else{ //admin
                }
            }
        }

        return [$nombresTabla , $horasemana, $quincena];

    }


    public function index(Request $request) {
        $permissions = Myhelp::EscribirEnLog($this, ' |reportes index| ');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions); //0:error, 1:estudiante,  2: profesor, 3:++ )
        Carbon::setLocale('es');

        $titulo = __('app.label.Reportes');
        $Authuser = Auth::user();

        
        $Reportes = Reporte::query();
        $showSelect = $this->losSelect($valoresSelectConsulta, $showUsers,$valoresSelect);
        $IntegerDefectoSelect = $valoresSelectConsulta->first()->id;

        $quincena = [];
        $horasemana = 0;
        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();
        $startDateMostrar = $startDate->isoFormat('dddd D [de] MMMM');
        $endDateMostrar = $endDate->isoFormat('dddd D [de] MMMM');
        
        $perPage = $request->has('perPage') ? $request->perPage : 10;
        
        $fnombresT = $this->fNombresTabla($numberPermissions ,$Reportes,$Authuser,$request,$titulo);
        $nombresTabla = $fnombresT[0];
        $horasemana = $fnombresT[1];
        $quincena = $fnombresT[2];


        $sumhoras_trabajadas = $Reportes->sum('horas_trabajadas'); $sumdiurnas  = $Reportes->sum('diurnas'); $sumnocturnas  = $Reportes->sum('nocturnas'); $sumextra_diurnas  = $Reportes->sum('extra_diurnas'); $sumextra_nocturnas  = $Reportes->sum('extra_nocturnas'); $sumdominical_diurno  = $Reportes->sum('dominical_diurno'); $sumdominical_nocturno  = $Reportes->sum('dominical_nocturno'); $sumdominical_extra_diurno  = $Reportes->sum('dominical_extra_diurno'); $sumdominical_extra_nocturno = $Reportes->sum('dominical_extra_nocturno');

        //# Para saber si tiene horas del dia anterior
        //ultimo reporte que hizo el usuario autenticado
        $ultiReporte = Reporte::Where('user_id',$Authuser->id)->orderBy('created_at','desc')->first();
        $ultimoReporte = 0;
        if($ultiReporte != null){
            // $ultimoReporte = $ultimoReporte->fecha_fin;
            $inihoraymin = intval(Carbon::parse($ultiReporte->fecha_ini)->format('H'));
            $finhoraymin = Carbon::parse($ultiReporte->fecha_fin)->format('H:i');
            if($finhoraymin == '23:59'){
                $ultimoReporte = 24 - $inihoraymin;
                $ultimoReporte = $ultimoReporte > 8 ? $ultimoReporte - 8 : $ultimoReporte;
            }
        }

        return Inertia::render('Reportes/Index', [ //carpeta
            'title'          =>  $titulo,
            'filters'        =>  $request->all(['search', 'field', 'order','soloValidos']),
            'perPage'        =>  (int) $perPage,
            'fromController' =>  $Reportes->paginate($perPage),
            'breadcrumbs'    =>  [['label' => __('app.label.Reportes'), 'href' => route('Reportes.index')]],
            'nombresTabla'   =>  $nombresTabla,

            'valoresSelect'         =>  $valoresSelect,
            'showSelect'            =>  $showSelect,
            'IntegerDefectoSelect'  =>  $IntegerDefectoSelect,
            'showUsers'             =>  $showUsers,
            'quincena'              =>  $quincena,
            'horasemana'            =>  $horasemana,
            'startDateMostrar'      =>  $startDateMostrar,
            'endDateMostrar'        =>  $endDateMostrar,
            'numberPermissions'     =>  $numberPermissions,
            'ultimoReporte'         =>  $ultimoReporte,

            'sumhoras_trabajadas'   =>  intval($sumhoras_trabajadas),
            'sumdiurnas'   =>  $sumdiurnas,
            'sumnocturnas'   =>  $sumnocturnas,
            'sumextra_diurnas'   =>  $sumextra_diurnas,
            'sumextra_nocturnas'   =>  $sumextra_nocturnas,
            'sumdominical_diurno'   =>  $sumdominical_diurno,
            'sumdominical_nocturno'   =>  $sumdominical_nocturno,
            'sumdominical_extra_diurno'   =>  $sumdominical_extra_diurno,
            'sumdominical_extra_nocturno'   =>  $sumdominical_extra_nocturno,

        ]);
    }//fin index

    public function create() { }

    public function updatingDate($date) {
        if($date === null || $date == '1969-12-31'){
            return null;
        }
        return date("Y-m-d H:i:s",strtotime($date));
    }

    public function store(ReporteRequest $request) {
        $numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' |reportes| ')); //0:error, 1:estudiante,  2: profesor, 3:++ )

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

                $Reportes->almuerzo = $request->almuerzo;
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
    public function edit($id) {
        $numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' |reportes| ')); //0:error, 1:estudiante,  2: profesor, 3:++ )

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
    public function update(Request $request, $id) {
        $numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' |reportes| ')); //0:error, 1:estudiante,  2: profesor, 3:++ )

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
            return back()->with('success', __('app.label.updated_successfully', ['name' => 'Reporte']));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.updated_error', ['name' => __('app.label.Reportes')]) . $th->getMessage());
        }
    }

    /**
         * Remove the specified resource from storage.
         *
         * @param  int  $id
         * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' |reportes| ')); //0:error, 1:estudiante,  2: profesor, 3:++ )
        DB::beginTransaction();

        try {
            // si se la rechazaron, tendra que hacer uno nuevo
            $Reportes = Reporte::findOrFail($id);
            if($numberPermissions > 8){
                $Reportes->delete();
                DB::commit();
                return back()->with('success', __('app.label.deleted_successfully', ['name' => 'Reporte']));
            }
            if($Reportes->valido == 0 || $Reportes->valido == 2){
                $Reportes->delete();
                DB::commit();
                return back()->with('success', __('app.label.deleted_successfully', ['name' => 'Reporte']));
            }else{
                DB::commit();
                return back()->with('warning', __('app.label.not_deleted', ['name' => 'Reporte']). '. Ya esta aprobado');
            }
            
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.Reportes')]) . $th->getMessage());
        }
    }
}
