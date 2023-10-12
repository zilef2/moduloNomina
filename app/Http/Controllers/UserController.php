<?php

namespace App\Http\Controllers;

use App\Exports\SiigoExport;
use App\Http\Requests\User\UserIndexRequest;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;

use App\Imports\UsersImport;
use App\Exports\UsersExport;
use App\helpers\Myhelp;
use App\Models\Cargo;
use App\Models\CentroCosto;
use App\Models\Permission;
use App\Models\Reporte;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use Illuminate\Support\Facades\Log;
use Stevebauman\Location\Request as LocationRequest;



class UserController extends Controller {
    public function __construct() {
        $this->middleware('permission:create user', ['only' => ['create', 'store']]);
        $this->middleware('permission:read user', ['only' => ['index', 'show']]);
        $this->middleware('permission:update user', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete user', ['only' => ['destroy', 'destroyBulk']]);
    }

    public function Dashboard() {
        $ListaControladoresYnombreClase = (explode('\\',get_class($this))); $nombreC = end($ListaControladoresYnombreClase);
        Log::info(' U -> '.Auth::user()->name. ' Accedio a la vista ' .$nombreC);
        $Authuser = Auth::user();
        $permissions = auth()->user()->roles->pluck('name')[0];
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);
    
        $ultimos5dias = null;
        if($numberPermissions === 1) {
            return redirect()->route('Reportes.index')->with('success', 'Bienvenido');
            // $reportes = (int) Reporte::Where('user_id', $Authuser->id)->count();
        }else{
            $reportes = (int) Reporte::count();
            
            if($numberPermissions === 3){
                $centroMio = $Authuser->centro_costo_id;
                $reportes = (int) Reporte::Where('centro_costo_id',$centroMio)->count();

                $ultimos5dias = [
                    'Mes pasado' => Reporte::Where('centro_costo_id',$centroMio)->whereValido(1)->where('fecha_ini','<', Carbon::today()->addMonth(-1))->get()->count(),
        
                    'Semana pasada' => Reporte::Where('centro_costo_id',$centroMio)->whereValido(1)->whereBetween('fecha_ini', [Carbon::now()->addDays(-7)->startOfWeek() ,
                        Carbon::now()->addDays(-7)->endOfWeek()])
                        ->get()->count(),
        
                    'Semana actual' => Reporte::Where('centro_costo_id',$centroMio)->whereValido(1)->whereBetween('fecha_ini', [Carbon::now()->startOfWeek() ,
                        Carbon::now()->endOfWeek()])
                        ->get()->count(),
                ];

                $diasNovalidos = [
                    'Mes pasado' => Reporte::Where('centro_costo_id',$centroMio)->whereIn('valido',[0,2])->where('fecha_ini','<', Carbon::today()->startOfMonth())->get()->count(),
                    'Mes actual' => Reporte::Where('centro_costo_id',$centroMio)->whereIn('valido',[0,2])->where('fecha_ini','>', Carbon::today()->startOfMonth())->get()->count(),
                ];

            }else{
                $ultimos5dias = [
                    'Mes pasado' => Reporte::whereValido(1)->where('fecha_ini','<', Carbon::today()->addMonth(-1))->get()->count(),
        
                    'Semana pasada' => Reporte::whereValido(1)->whereBetween('fecha_ini', [Carbon::now()->addDays(-7)->startOfWeek() ,
                        Carbon::now()->addDays(-7)->endOfWeek()])
                        ->get()->count(),
        
                    'Semana actual' => Reporte::whereValido(1)->whereBetween('fecha_ini', [Carbon::now()->startOfWeek() ,
                        Carbon::now()->endOfWeek()])
                        ->get()->count(),
                ];
                $diasNovalidos = [
                    'Mes pasado' => Reporte::whereIn('valido',[0,2])->where('fecha_ini','<', Carbon::today()->startOfMonth())->get()->count(),
                    'Mes actual' => Reporte::whereIn('valido',[0,2])->where('fecha_ini','>', Carbon::today()->startOfMonth())->get()->count(),
                ];
               
            }
            $usuariosConRol = User::whereHas("roles", function($q){ 
                $q->where("name", "empleado");
            })->take(5)->get();
            foreach ($usuariosConRol as $value) {
                $BooleanreportoHoy = Reporte::where('user_id',$value->id)
                    ->whereDate('fecha_fin',Carbon::today())
                    ->first();
    
                if($BooleanreportoHoy !== null)
                    $trabajadoresHoy[$value->name] = $BooleanreportoHoy->horas_trabajadas;
                else
                    $trabajadoresHoy[$value->name] = 0;
            }
    
            $centros = CentroCosto::all();
            foreach ($centros as $value) {
                $BooleanReporteCentro = Reporte::where('centro_costo_id',$value->id)->whereDate('fecha_fin',Carbon::today())->sum('horas_trabajadas');
                if($BooleanReporteCentro !== null){
                    $centrosHoy[$value->nombre] = $BooleanReporteCentro;
                }
                else
                    $centrosHoy[$value->nombre] = 0;
    
            }
        }
    
        //#location
            // if ($position = Location::get()) { // Successfully retrieved position.
            //     $positio = $position->countryName;
            //     Log::channel('stevebauman')->info('Vista: welcome. User => '  .Auth::user()->name .' posicion:'. $positio); } else {
            //     $positio =' Failed retrieving position.';
            //     Log::channel('stevebauman')->info('Vista: welcome. User => '  .Auth::user()->name .' '. $positio); }
    
        return Inertia::render('Dashboard', 
        [
            'versionZilef'  => '0.9.1',
            'users'         => (int) User::count(),
            'roles'         => (int) Role::count(),
            'permissions'   => (int) Permission::count(),
            'reportes'   => $reportes,
            'ultimos5dias' => $ultimos5dias,
            'diasNovalidos' => $diasNovalidos ?? [],
            'trabajadoresHoy' => $trabajadoresHoy ?? [],
            'centrosHoy' => $centrosHoy ?? [],
        ]);
    }



    //empieza el index
    public function MapearClasePP(&$users, $numberPermissions) {
        $users = $users->get()->map(function ($user) use ($numberPermissions) {
            $user->cc = $user->centroName();
            return $user;
        })->filter();
    }

    public function Busqueda(UserIndexRequest $request){
        $users = User::query()->with('roles','cargo');

        if ($request->has('search')) {
            $users->where('name', 'LIKE', "%" . $request->search . "%");
            $users->orWhere('email', 'LIKE', "%" . $request->search . "%");
            $users->orWhere('cedula', 'LIKE', "%" . $request->search . "%");
        }

        if ($request->has(['field', 'order'])) {
            dd($request->field, $request->order);
            $users->orderBy($request->field, $request->order);
        }else{
            $users->orderByDesc('updated_at');

        }

        if($request->has(['onlySupervis'])){
            $users->whereHas('roles', function ($query) {
                return $query->where('name', 'supervisor');
            });
        }

        $numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, 'users'));
        
//         if($numberPermissions == 3){//supervisor
//             $centroID = Auth::user()->centro_costo_id;
//             if($centroID){
//                 $users->Where('centro_costo_id' , $centroID);
//             }else{
            // $users->Where('id', 0);
// }
        // }
        return $users;
    }

    public function index(UserIndexRequest $request){
        $permissions = Myhelp::EscribirEnLog($this, ' users');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);
        $users = $this->Busqueda($request);

        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $role = Auth()->user()->roles->pluck('name')[0];
        $roles = Role::get();
        if ($numberPermissions != 3 && $numberPermissions < 10) {
            $users->whereHas('roles', function ($query) {
                return $query->where('name', '<>', 'superadmin');
            });
            $roles = Role::where('name', '<>', 'superadmin')
                ->where('name', '<>', 'admin')
                ->get();
        }
        $cargos = Cargo::all();
        $centros = CentroCosto::all();
        

        $this->MapearClasePP($users,$numberPermissions);

        $page = request('page', 1); // Current page number
        $total = $users->count();
        $paginated = new LengthAwarePaginator(
            $users->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        $sexoSelect[] = [ 'label' => 'masculino', 'value' => 0 ];
        $sexoSelect[] = [ 'label' => 'femenino', 'value' => 1 ];

        // 21sept
        $superviNullCentro = User::whereHas('roles', function ($query) {
            return $query->where('name', 'supervisor');
        })->WhereNull('centro_costo_id')->count();

        return Inertia::render('User/Index', [
            'title'             => __('app.label.user'),
            'filters'           => $request->all(['search', 'field', 'order']),
            'perPage'           => (int) $perPage,
            'users'             => $paginated,
            'roles'             => $roles,
            'cargos'            => $cargos,
            'centros'           => $centros,
            'sexoSelect'        => $sexoSelect,
            'superviNullCentro' => $superviNullCentro,
            'breadcrumbs'   => [['label' => __('app.label.user'), 'href' => route('user.index')]],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() { }

    public function store(UserStoreRequest $request) {
        Myhelp::EscribirEnLog($this, 'users');
        DB::beginTransaction();

        try {

            $elCentroId = $request->centroid == 0 ? null : $request->centroid;

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->cedula.'*'),
                // 'password' => Hash::make($request->password),
                'cargo_id' => $request->cargo,
                'cedula'=> $request->cedula,
                'telefono' => $request->telefono,
                'celular' => $request->celular,
                'fecha_de_ingreso' => $request->fecha_de_ingreso,
                'sexo' => $request->sexo,
                'salario' => $request->salario,

                'centro_costo_id' => $elCentroId,
            ]);
            $user->assignRole($request->role);
            DB::commit();
            return back()->with('success', __('app.label.created_successfully', ['name' => $user->name]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.created_error', ['name' => __('app.label.user')]) . $th->getMessage());
        }
    }

    public function show($id) { } public function edit($id) { }


    public function update(UserUpdateRequest $request, $id) {
        Myhelp::EscribirEnLog($this, 'users');
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $elCentroId = $request->centroid == 0 ? null : $request->centroid;
            
            if($request->role === 'supervisor' && $elCentroId == null){
                $elCentroId = CentroCosto::all()->first()->id;
            }else{
                // $elCentroId = null;
            }

            $user->update([
                'name'      => $request->name,
                'email'     => $request->email,
                // 'password'  => $request->password ? Hash::make($request->password) : $user->password,
                'cargo_id' => $request->cargo,
                'cedula'=> $request->cedula,
                'telefono' => $request->telefono,
                'celular' => $request->celular,
                'fecha_de_ingreso' => $request->fecha_de_ingreso,
                'sexo' => $request->sexo,
                'salario' => $request->salario,

                'centro_costo_id' => $elCentroId,
            ]);
            $user->syncRoles($request->role);
            DB::commit();
            return back()->with('success', __('app.label.updated_successfully', ['name' => $user->name]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.updated_error', ['name' => $user->name]) . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user) {
        Myhelp::EscribirEnLog($this, 'users');
        try {
            $user->delete();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => $user->name]));
        } catch (\Throwable $th) {
            return back()->with('error', __('app.label.deleted_error', ['name' => $user->name]) . $th->getMessage());
        }
    }
   

    public function destroyBulk(Request $request) {
        Myhelp::EscribirEnLog($this, ' |users Bulk| ');
        try {
            $user = User::whereIn('id', $request->id);
            $user->delete();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.user')]));
        } catch (\Throwable $th) {
            return back()->with('error', __('app.label.deleted_error', ['name' => count($request->id) . ' ' . __('app.label.user')]) . $th->getMessage());
        }
    }

    public function FunctionUploadFromEx(Request $request) {
        Myhelp::EscribirEnLog($this, 'users FunctionUploadFromEx');
        $users = User::Select('id','name','cedula','cargo_id')->WhereHas("roles", function($q){
            $q->Where("name", "empleado");
        })->count();
        $iniFormat = '';
        $finFormat = '';
        if($request->quincena && $request->fecha_ini){
            $quincena = intval($request->quincena);
        
            $year = intval($request->fecha_ini['year']);
            $month = intval($request->fecha_ini['month']+1);
            $NumReportesIniFin = $this->CalcularIniFinQuincena($quincena,$month,$year);
            $iniFormat = Carbon::parse($NumReportesIniFin['ini'])->format('d-m-Y');
            $finFormat = Carbon::parse($NumReportesIniFin['fin'])->format('d-m-Y');
        }

        return Inertia::render('User/uploadFromExcel', [
            'title'       => __('app.label.user'),
            'breadcrumbs' => [['label' => __('app.label.user'), 'href' => route('user.index')]],
            'NumUsers'    => $users,
            'NumReportes' => $NumReportesIniFin['NumReportes']?? 0,
            'ini'         => $iniFormat,
            'fin'         => $finFormat,
        ]);
    }

    public function FunctionUploadFromExPost(Request $request) { //import
        Myhelp::EscribirEnLog($this, 'users FunctionUploadFromExPost');
        $exten = $request->archivo1->getClientOriginalExtension();
        // Validar que el archivo es de Excel
        if ($exten != 'xlsx' && $exten != 'xls') {
            return back()->with('warning', 'El archivo debe ser de Excel');
        }
        $pesoKilobyte = ((int)($request->archivo1->getSize()))/(1024);
        if ($pesoKilobyte > 1256) { //debe pesar menos de 1MB +-
            return back()->with('warning', 'El archivo debe pesar menos de 1MB');
        }

        try{
            $import = new UsersImport();
            $import->import($request->archivo1);

            $CountFilas = session('CountFilas');
            $usuariosActualizados = session('usuariosActualizados',[]);
            $countNoleidos = session('countNoleidos',0); $countNoCargo = session('countNoCargo',0); $countSex = session('countSex',0); $countCedulaRepetida = session('countCedulaRepetida',0);

            session(['CountFilas' => 0]);
            session(['countNoleidos' => 0]);
            session(['countNoCargo' => 0]);
            session(['countSex' => 0]);
            session(['usuariosActualizados' => []]);
            if( $countNoleidos == 0 && $countNoCargo == 0 && $countSex == 0 && $countCedulaRepetida == 0)
                $messageSuccess1 = 'Usuarios nuevos: '.$CountFilas;
            else{

                $messageSuccess1 = 'Usuarios nuevos: '.$CountFilas.
                '.  Hubo ' .($countNoleidos).' no leidas, ';

                $partSuccess1  = $countNoCargo > 0 ? $countNoCargo. ' con cargo erroneo, ' : '';
                $messageSuccess1 .= $partSuccess1;

                $partSuccess1  = $countSex > 0 ? $countSex. ' genero mal escrito, ' : '';
                $messageSuccess1 .= $partSuccess1;

                $partSuccess1  = $countCedulaRepetida > 0 ? $countCedulaRepetida. ' identificacion mal escrita, ' : '';
                $messageSuccess1 .= $partSuccess1;
            }
            if(count($usuariosActualizados) > 0){
                $StringUsuariosRep = implode(", ",$usuariosActualizados);
                session(['countCedulaRepetida' => 0]);
                return back()->with('success', $messageSuccess1)
                    ->with('warning', count($usuariosActualizados).' Usuarios actualizados: '.$StringUsuariosRep);
            }else{
                return back()->with('success', $messageSuccess1);
            }

        } catch (ValidationException $e) {
            $failures = $e->failures();
            $lasRowMalas = [];
            foreach ($failures as $failure) {
                $lasRowMalas[] = $failure->row(); // row that went wrong
                // $failure->attribute(); // either heading key (if using heading row concern) or column index
                // $failure->errors(); // Actual error messages from Laravel validator
                // $failure->values(); // The values of the row that has failed.
            }
            $StringlasRowMalas = implode(", ",$lasRowMalas);
            session(['CountFilas' => 0]);
            session(['countNoleidos' => 0]);
            session(['usuariosActualizados' => []]);
            // if (config('app.env') === 'production') {
                return back()->with('warning', 'Error Excel. ' . $e->getMessage(). '. filas con errores: '.$StringlasRowMalas);
            // }else{
            //     return back()
            //     ->with('error', 'Error en el proceso de Excel. ' . $lasRowMalas)
            // }
        }
    }


    //exportar el formato unico de ECnomina
    public function export(Request $request) {
        Myhelp::EscribirEnLog($this, 'users export');
        $quincena = intval($request->quincena);
        
        $year = intval($request->year);
        $month = intval($request->month+1);
        $NumReportesIniFin = $this->CalcularIniFinQuincena($quincena,$month,$year);
        $NumeroDiasFestivos = intval($request->NumeroDiasFestivos);
        if($NumReportesIniFin['NumReportes'] > 0){
            return Excel::download(new UsersExport( $NumReportesIniFin['ini'],$NumReportesIniFin['fin'], $NumeroDiasFestivos),
                    "".$year.'Quincena'.$quincena.'DelMes'.$month.".xlsx"
            );
        }else{
            return redirect()->route('user.uploadexcel')->with('warning', 'No hay reportes, en el rango de fechas seleccionadas');
        }
        // return view('reporte1temp',$ini,$fin);
    }

    private function CalcularIniFinQuincena($quincena,$month,$year){
        $ini = Carbon::createFromFormat('d/m/Y',  '1/'.$month.'/'.$year)->setHour(0)->setminutes(0);
        // dd($ini);
        $fin = Carbon::createFromFormat('d/m/Y',  '1/'.$month.'/'.$year)->setHour(23)->setminutes(0);
        if($quincena == 1){
            // $ini->addDays(-3);
            $fin->addDays(14);//antes era 12
        }else{
            $ini->addDays(15);//
            $fin->addMonths(1)->addDays(-1); //antes era -4
            $nombreDiaSemana = intval($fin->format('d'));

            if( $nombreDiaSemana == 31) $fin->addDays(-1);
        }
        // dd($ini,$fin);
        $users = User::Select('id','name','cedula','cargo_id')->WhereHas("roles", function($q){
            $q->Where("name", "empleado");
        })->get();
        $NumReportes = 0;
        foreach ($users as $value) {
            $NumReportes += Reporte::where('user_id', $value->id)
                ->where('valido',1)
                ->whereBetween('fecha_ini', [$ini,$fin])->count();
        }

        return [
            'NumReportes' => $NumReportes,
            'ini' => $ini,
            'fin' => $fin,
        ];
    }

    public function downloadsigo(Request $request) {
        Myhelp::EscribirEnLog($this, ' |users sigo download| ');
        
        $quincena = intval($request->quincena);
        $year = intval($request->year);
        $month = intval($request->month+1);
        $ini = Carbon::createFromFormat('d/m/Y',  '1/'.$month.'/'.$year)->setHour(0);
        $fin = Carbon::createFromFormat('d/m/Y',  '1/'.$month.'/'.$year)->setHour(23);
        $NumeroDiasFestivos = intval($request->NumeroDiasFestivos);

        //? NOTE:: values 15-30
        if($quincena == 1){
            // $ini->addDays(-3);
            $fin->addDays(14);//antes era 12            
        }else{
            $ini->addDays(15);
            $fin->addMonths(1)->addDays(-1); //antes era -4
        }
        // dd($ini,$fin);

        $users = User::Select('id','name','cedula','cargo_id')->WhereHas("roles", function($q){
            $q->Where("name", "empleado");
        })->get();

        $NumReportes = 0;
        foreach ($users as $value) {
            $NumReportes += Reporte::where('user_id', $value->id)
                ->where('valido',1)
                ->whereBetween('fecha_ini', [$ini,$fin])->count();
        }

        if($NumReportes > 0){
            return Excel::download(new SiigoExport($ini,$fin, $NumeroDiasFestivos), "Siigo ".$year.'Quincena'.$quincena.'DelMes'.$month.".xlsx");
        }else{
            return redirect()->route('user.uploadexcel')->with('warning', 
                'El numero de reportes en esa quincena es 0. '.
                'formato de la fecha: Año - Mes - dia. '.
                'fecha inicial: '.$ini->format('Y-m-d').' - '.
                'fecha final: '.$fin->format('Y-m-d')
            );

            // return back()->with('error', __('app.label.created_error', ['name' => 'No hay reportes']) . 'En el rango de fechas seleccionadas');
        }
        // return view('reporte1temp',$ini,$fin);
    }

    public function showReporte($id) {
        Myhelp::EscribirEnLog($this, 'users showreporte');
        // $centroCostos = centroCosto::findOrFail($id);
        $Reportes = Reporte::query();
        $nombrePersona = User::find($id)->name;
        $titulo = __('app.label.Reportes');
        $permissions = Auth()->user()->roles->pluck('name')[0];
        $Reportes->Where('user_id',$id);
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
        
        if($permissions === "empleado") { //NO admin | administrativo
        }else{ // not empleado
            // $ReportesEsteMes = Reporte::WhereMonth('fecha_ini',$esteMes)->get()->count();
            $titulo = $this->CalcularTituloQuincena($permissions);
            
            $Reportes->orderBy('fecha_ini'); $perPage = 15;

            // $nombresTabla =[//0: como se ven //1 como es la BD
            //     ["Acciones","#","Centro costo","Trabajador", "valido",   "inicio",       "fin",        "horas trabajadas",   "observaciones"],
            //     ["b_valido","t_fecha_ini", "t_fecha_fin", "i_horas_trabajadas", "s_observaciones"], //m for money || t for datetime || d date || i for integer || s string || b boolean 
            //     [null,null,null,null,null,null,null,null,null,null,null,null,null,null] //campos ordenables
            // ];
            $nombresTabla =[//0: como se ven //1 como es la BD
                
                ["Acciones","#","Centro costo","Trabajador", "valido",   "inicio","fin","horas trabajadas",  'diurnas', 'nocturnas', 'extra diurnas', 'extra nocturnas', 'dominical diurno', 'dominical nocturno', 'dominical extra diurno', 'dominical extra nocturno', "observaciones"],
                ["b_valido","t_fecha_ini", "t_fecha_fin", "i_horas_trabajadas", 'i_diurnas', 'i_nocturnas', 'i_extra_diurnas', 'i_extra_nocturnas', 'i_dominical_diurno', 'i_dominical_nocturno', 'i_dominical_extra_diurno', 'i_dominical_extra_nocturno',"s_observaciones"], //m for money || t for datetime || d date || i for integer || s string || b boolean 
                [null,null,null,null,"b_valido","t_fecha_ini", "t_fecha_fin", "i_horas_trabajadas", 'i_diurnas', 'i_nocturnas', 'i_extra_diurnas', 'i_extra_nocturnas', 'i_dominical_diurno', 'i_dominical_nocturno', 'i_dominical_extra_diurno', 'i_dominical_extra_nocturno',"s_observaciones"] //m for money || t for datetime || d date || i for integer || s string || b boolean 
            ];

            //sin uso1
            $quincena = Reporte::Where('user_id',$id)
                ->WhereBetween('fecha_ini',[Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()])
                ->Orderby('fecha_ini')->get();

                //touse //myhelp
            $diasSemana = ['Lunes','Martes','Miercoles','Jueves','Viernes','Sabado','Domingo'];
            foreach ($quincena as $key => $value) {
                // dd($value->fecha_ini);
                $laKey = $diasSemana[$key] . Carbon::createFromFormat('Y-m-d H:i:s', $value->fecha_ini)->format('d');
                // dd($laKey);
                $quincena[$laKey ] = $value->horas_trabajadas;
                unset($quincena[$key]);
            }
            //fin sin uso1

            $quincena = [
                'Primera quincena' => Reporte::Where('user_id',$id)->WhereBetween('fecha_ini',[Carbon::now()->startOfMonth(),Carbon::now()->startOfMonth()->addDays(14)])->count(),
                'Segunda quincena' => Reporte::Where('user_id',$id)->WhereBetween('fecha_ini',[Carbon::now()->startOfMonth()->addDays(15),Carbon::now()->LastOfMonth()])->count(),
            ];
        }
        $sumhoras_trabajadas = $Reportes->sum('horas_trabajadas');
        return Inertia::render('Reportes/Index', [ //carpeta
            'title'          =>  $titulo,
            'filters'        =>  null,
            'perPage'        =>  (int) $perPage,
            'fromController' =>  $Reportes->paginate($perPage),
            'breadcrumbs'    =>  [['label' => __('app.label.Reportes'), 'href' => route('Reportes.index')]],
            'nombresTabla'   =>  $nombresTabla,

            'valoresSelect'   =>  $valoresSelect,
            'showSelect'   =>  $showSelect,
            'IntegerDefectoSelect'   =>  $IntegerDefectoSelect,
            'showUsers'   =>  $showUsers,
            'quincena'   =>  $quincena,
            'nombrePersona'   =>  $nombrePersona,
            'sumhoras_trabajadas'   =>  $sumhoras_trabajadas,
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
}
