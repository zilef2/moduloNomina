<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\Http\Controllers\Controller;
use App\Models\Parametro;
use App\Models\Permission;
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
    public function losSelect(&$valoresSelectConsulta, &$showUsers, &$valoresSelect, &$userFiltro,$numberPermissions) {
        $elUser = Myhelp::AuthU();
        $valoresSelectConsulta = CentroCosto::orderBy('nombre')->get();
        $userFiltro = [];
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

        if($numberPermissions === 3){
            $centrosSupervisor = $elUser->ArrayCentrosID();
            $userEmpleados = User::UsersWithManyRols(['empleado','supervisor'])
                ->orderBy('name')->get();
            $userEmpleados = $userEmpleados->map(function($user) use($centrosSupervisor){
                $tieneReportesEnElCentro = $user->reportes()->WhereIn('centro_costo_id',$centrosSupervisor)->count();

                return $tieneReportesEnElCentro > 0 ? $user : false;
            })->filter();
        }else{
            $userEmpleados = User::UsersWithRol('empleado')->orderBy('name')->get();
        }
        foreach ($userEmpleados as $value) {
            $userFiltro[] = [
                'label' => $value->name,
                'value' => (int)($value->id),
            ];
        }
        return $showSelect;

    }

    public function Filtros(&$request, &$Reportes) {

        if ($request->has('search')) {
            $Reportes->whereMonth('fecha_ini', $request->search);
//            $Reportes->OrwhereDay('fecha_fin', $request->search);
            // $Reportes->orWhere('fecha_fin', 'LIKE', "%" . $request->search . "%");
        }
        if ($request->has('searchDDay')) {
            $Reportes->whereDay('fecha_ini', $request->searchDDay);
        }

        if ($request->has(['field', 'order'])) {
                $Reportes->orderBy($request->field, $request->order);
            }else{
                $Reportes->orderByDesc('fecha_ini');
        }
        //# solo validos //0 aun no | 1 valido | 2 rechazado
        if ($request->has('soloValidos')) {$Reportes->where('valido', 1);}

        if ($request->has(['FiltroUser'])) {
            $Reportes->Where('user_id',$request->FiltroUser);
        }
    }



//    validar que no se use mas
    private function CalcularTituloQuincena($numberPermissions,$Authuser,$horasPersonal) {
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


    //  aqui se filtra
    public function fNombresTabla($numberPermissions ,&$Reportes,$Authuser,$request, &$titulo) {

        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();
        $quincena = [];
        $horasPersonal = 0;

        if ($request->has(['field', 'order'])) {
            $Reportes->orderBy($request->field, $request->order);
        }else{
            $Reportes->orderBy('valido')->orderByDesc('fecha_ini');
        }



        $horasemana = Reporte::Where('user_id',$Authuser->id)
            ->WhereBetween('fecha_ini', [$startDate, $endDate])
            ->sum('horas_trabajadas');
        if($numberPermissions === 1) { //1 = empleado | 2 administrativo | 3 supervisor | 4 ingeniero
            $Reportes->whereUser_id($Authuser->id);

            $nombresTabla =[//0: como se ven //1 como es la BD
                ["Acciones","#","Centro costo", "valido",   "inicio","fin","horas trabajadas",  'diurnas', 'nocturnas', 'extra diurnas', 'extra nocturnas', 'dominical diurno', 'dominical nocturno', 'dominical extra diurno', 'dominical extra nocturno', "observaciones"],
                ["b_valido","t_fecha_ini", "t_fecha_fin", "i_horas_trabajadas", 'i_diurnas', 'i_nocturnas', 'i_extra_diurnas', 'i_extra_nocturnas', 'i_dominical_diurno', 'i_dominical_nocturno', 'i_dominical_extra_diurno', 'i_dominical_extra_nocturno',"s_observaciones"], //m for money || t for datetime || d date || i for integer || s string || b boolean
                [null,null,null,null,"b_valido","t_fecha_ini", "t_fecha_fin", "i_horas_trabajadas", 'i_diurnas', 'i_nocturnas', 'i_extra_diurnas', 'i_extra_nocturnas', 'i_dominical_diurno', 'i_dominical_nocturno', 'i_dominical_extra_diurno', 'i_dominical_extra_nocturno',"s_observaciones"] //m for money || t for datetime || d date || i for integer || s string || b boolean
            ];



            //# solo validos 0 aun no | 1 valido | 2 rechazado
            if ($request->has('soloValidos')) {$Reportes->where('valido', 1);}


        }else{ //administrativo, supervisor y admin
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
            $horasPersonal = Reporte::WhereBetween('fecha_ini', [$startDate, $endDate])->sum('horas_trabajadas');

            if($numberPermissions == 3){ //supervisor
                $ArrayCentrosUserID = $Authuser->ArrayCentrosID();
                $horasPersonal = Reporte::WhereBetween('fecha_ini', [$startDate, $endDate])
                    ->WhereIn('centro_costo_id', $ArrayCentrosUserID)
                    ->sum('horas_trabajadas');

                $Reportes->WhereIn('centro_costo_id',$ArrayCentrosUserID);
                $quincena = [
                    'Primera quincena' => Reporte::whereBetween('fecha_ini',[Carbon::now()->startOfMonth(),Carbon::now()->startOfMonth()->addDays(14)])
                        ->Where('centro_costo_id',$Authuser->centro_costo_id)
                        ->count(),
                    'Segunda quincena' => Reporte::whereBetween('fecha_ini',[Carbon::now()->startOfMonth()->addDays(15),Carbon::now()->LastOfMonth()])
                        ->Where('centro_costo_id',$Authuser->centro_costo_id)
                        ->count()
                ];

            }else{ //admin y administrativo
                if($numberPermissions == 2){//administrativo
                    $horasPersonal = Reporte::WhereBetween('fecha_ini', [$startDate, $endDate])
                        ->sum('horas_trabajadas');

//                    $Reportes->where('centro_costo_id', $Authuser->centro_costo_id);
                }
            }
            $titulo = 'Horas trabajadas quincena: '.$horasPersonal;
        }
        return [$nombresTabla , $horasemana, $quincena,$horasPersonal];
    }


    private function DoArrayHorasSemanales(){
        $parametros = Parametro::first();
        if($parametros) {
            return [
                'HORAS_ORDINARIAS' => $parametros['HORAS_ORDINARIAS'],
                'MAXIMO_HORAS_SEMANALES' => $parametros['HORAS_NECESARIAS_SEMANA'],
            ];
        }
        return [0,0];
    }

    public function index(Request $request) {
        $permissions = Myhelp::EscribirEnLog($this, ' |reportes index| ');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);
        $Authuser = Myhelp::AuthU();
        Carbon::setLocale('es');
        $titulo = __('app.label.Reportes');

        $Reportes = Reporte::query();
        $showSelect = $this->losSelect($valoresSelectConsulta, $showUsers,$valoresSelect,$userFiltro,$numberPermissions);
        $IntegerDefectoSelect = $valoresSelectConsulta->first()->id;

        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();
        $startDateMostrar = $startDate->isoFormat('dddd D [de] MMMM');
        $endDateMostrar = $endDate->isoFormat('dddd D [de] MMMM');

        $perPage = $request->has('perPage') ? $request->perPage : 10;

        //  aqui se filtra
        $fnombresT = $this->fNombresTabla($numberPermissions ,$Reportes,$Authuser,$request,$titulo);

        $nombresTabla = $fnombresT[0];$horasemana = $fnombresT[1];$quincena = $fnombresT[2];$horasPersonal = $fnombresT[3];

        //para mostrar la suma de las horas extras
        $sumhoras_trabajadas = $Reportes->sum('horas_trabajadas');
        $sumdiurnas  = $Reportes->sum('diurnas'); $sumnocturnas  = $Reportes->sum('nocturnas'); $sumextra_diurnas  = $Reportes->sum('extra_diurnas'); $sumextra_nocturnas  = $Reportes->sum('extra_nocturnas'); $sumdominical_diurno  = $Reportes->sum('dominical_diurno'); $sumdominical_nocturno  = $Reportes->sum('dominical_nocturno'); $sumdominical_extra_diurno  = $Reportes->sum('dominical_extra_diurno'); $sumdominical_extra_nocturno = $Reportes->sum('dominical_extra_nocturno');


        //5abril2024
        //suma de horas ordinarias[0] &
        $ArrayOrdinarias = Myhelp::CalcularPendientesQuicena($Authuser);
        $HorasDeCadaSemana = Myhelp::CalcularHorasDeCadaSemana($startDate, $endDate,$Authuser);


        //20ene2024
        $esteMes = Carbon::now()->format('m');
        $horasTrabajadasHoy = Reporte::WhereMonth('fecha_ini',$esteMes)
            ->Where('user_id',$Authuser->id)->pluck('horas_trabajadas','fecha_ini')
            ->toArray();
        foreach ($horasTrabajadasHoy as $index => $item) {
            $elIndex = (int)Carbon::parse($index)->format('d');
            if(isset($horasTrabajadasHoy2[$elIndex]) ){
                $horasTrabajadasHoy2[$elIndex] += $item;
            }else{
                $horasTrabajadasHoy2[$elIndex] = $item;
            }
        }

        $ArrayHorasSemanales = $this->DoArrayHorasSemanales();

        return Inertia::render('Reportes/Index', [ //carpeta
            'title'                 =>  $titulo,
            'filters'               =>  $request->all(['search', 'field', 'order','soloValidos','FiltroUser','searchDDay']),
            'perPage'               =>  (int) $perPage,
            'fromController'        =>  $Reportes->paginate($perPage),
            'breadcrumbs'           =>  [['label' => __('app.label.Reportes'), 'href' => route('Reportes.index')]],
            'nombresTabla'          =>  $nombresTabla,

            'valoresSelect'         =>  $valoresSelect,
            'showSelect'            =>  $showSelect,
            'IntegerDefectoSelect'  =>  $IntegerDefectoSelect,
            'showUsers'             =>  $showUsers,
            'quincena'              =>  $quincena,
            'horasemana'            =>  $horasemana,
            'horasPersonal'         =>  $horasPersonal,
            'startDateMostrar'      =>  $startDateMostrar,
            'endDateMostrar'        =>  $endDateMostrar,
            'numberPermissions'     =>  $numberPermissions,
            'ArrayOrdinarias'         =>  $ArrayOrdinarias,
            'userFiltro'            =>  $userFiltro,

            'sumhoras_trabajadas'   =>  (int)($sumhoras_trabajadas),
            'sumdiurnas'            =>  $sumdiurnas, 'sumnocturnas'                  =>  $sumnocturnas, 'sumextra_diurnas'              =>  $sumextra_diurnas, 'sumextra_nocturnas'            =>  $sumextra_nocturnas, 'sumdominical_diurno'           =>  $sumdominical_diurno, 'sumdominical_nocturno'         =>  $sumdominical_nocturno, 'sumdominical_extra_diurno'     =>  $sumdominical_extra_diurno, 'sumdominical_extra_nocturno'   =>  $sumdominical_extra_nocturno,
            'horasTrabajadasHoy'    =>  $horasTrabajadasHoy2 ?? [],
            'HorasDeCadaSemana'     =>  $HorasDeCadaSemana,
            'ArrayHorasSemanales'   =>  $ArrayHorasSemanales,
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
            $fecha_ini = $this->updatingDate($request->fecha_ini);
            $fecha_fin = $this->updatingDate($request->fecha_fin);
            $fecha_ini2 = Carbon::parse($fecha_ini)->addMinute()->format('Y-m-d H:i:s');
            $fecha_fin2 = Carbon::parse($fecha_fin)->addMinutes(-1)->format('Y-m-d H:i:s');

            $traslapa =  Reporte::WhereBetween('fecha_ini',[$fecha_ini,$fecha_fin2])->Where('user_id',$thisUserId)->count();
            $traslapa2 = Reporte::WhereBetween('fecha_fin',[$fecha_ini2,$fecha_fin])->Where('user_id',$thisUserId)->count();
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

                $Reportes->dominical_diurno = $request->dominical_diurnas;$Reportes->dominical_nocturno = $request->dominical_nocturnas;$Reportes->dominical_extra_diurno = $request->dominical_extra_diurnas;$Reportes->dominical_extra_nocturno = $request->dominical_extra_nocturnas;

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
    }
    //fin store

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
         * @return \Illuminate\Http\RedirectResponse
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
            }
            DB::commit();
            return back()->with('warning', __('app.label.not_deleted', ['name' => 'Reporte']). '. Ya esta aprobado');

        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.Reportes')]) . $th->getMessage());
        }
    }



    public function MassiveReportes(Request $request)
    {
        $numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' |reportes| ')); //0:error, 1:estudiante,  2: profesor, 3:++ )

        DB::beginTransaction();
        try {
//            $thisUserId = myhelp::AuthU()->id;
            $fecha_ini = Carbon::parse($request->fecha_ini[0],'America/Bogota')->addHours(-5);
            $fecha_fin = Carbon::parse($request->fecha_ini[1],'America/Bogota')->addHours(-5);
            $diff = $fecha_ini->diffinDays($fecha_fin) + 1;
            $valorQuemado_HorasTrabajadas = 11;

//            $fecha_ini = $this->updatingDate($request->fecha_ini);
//            $fecha_fin = $this->updatingDate($request->fecha_fin);

            $UltimoUser = User::WhereHas("roles", function ($q) {
                $q->Where("name", "empleado");
            })->orderBy('id', 'desc')->first()->id;


            for ($i = 0; $i < $diff; $i++) {
                $Reportes = new Reporte;
                $fecha_in2 = clone $fecha_ini;
                $Reportes->fecha_ini = $fecha_in2;
                $fecha_fin = clone $fecha_ini;
                $fecha_fin->addHours($valorQuemado_HorasTrabajadas);
                if ($fecha_ini->isSunday()) {
                    $fecha_ini->addDays();
                    continue;
                }
                $fecha_ini->addDays();
                $Reportes->fecha_fin = $fecha_fin;

                $Reportes->horas_trabajadas = $valorQuemado_HorasTrabajadas;

                $Reportes->almuerzo = 1;
                $Reportes->diurnas = $valorQuemado_HorasTrabajadas;
                $Reportes->nocturnas = 0;
                $Reportes->extra_diurnas = 0;
                $Reportes->extra_nocturnas = 0;
                $Reportes->dominical_diurno = 0;
                $Reportes->dominical_nocturno = 0;
                $Reportes->dominical_extra_diurno = 0;
                $Reportes->dominical_extra_nocturno = 0;

                $Reportes->valido = 1;
                $Reportes->observaciones = $request->observaciones;
                $Reportes->centro_costo_id = $request->centro_costo_id;

                $Reportes->user_id = $UltimoUser;
                $Reportes->save();
            }

            DB::commit();
            return back()->with('success', __('app.label.created_successfully', ['name' => $Reportes->nombre]));

        } catch (\Throwable $th) {
            DB::rollback();
            $mensajeErrorTH = $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile();
            myhelp::EscribirEnLog($this,'',1,$mensajeErrorTH);
            return back()->with('error', __('app.label.created_error', ['name' => __('app.label.Reportes')]) . $mensajeErrorTH);
        }
    }

    public function destroyBulk(Request $request){
        try {
            $reportes = Reporte::whereIn('id', $request->id);
            $reportes->delete();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.reporte')]));
        } catch (\Throwable $th) {
            return back()->with('error', __('app.label.deleted_error', ['name' => count($request->id) . ' ' . __('app.label.reporte')]) . $th->getMessage());
        }
    }


}
