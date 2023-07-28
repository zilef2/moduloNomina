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
use App\Models\Cargo;
use App\Models\CentroCosto;
use App\Models\Reporte;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class UserController extends Controller
{

    public function __construct() {
        $this->middleware('permission:create user', ['only' => ['create', 'store']]);
        $this->middleware('permission:read user', ['only' => ['index', 'show']]);
        $this->middleware('permission:update user', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete user', ['only' => ['destroy', 'destroyBulk']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UserIndexRequest $request){
        // $ListaControladoresYnombreClase = (explode('\\',get_class($this))); $nombreC = end($ListaControladoresYnombreClase);
        // Log::info(' U -> '.Auth::user()->name. ' Accedio a la vista ' .$nombreC);

        $users = User::query();
        if ($request->has('search')) {
            $users->where('name', 'LIKE', "%" . $request->search . "%");
            $users->orWhere('email', 'LIKE', "%" . $request->search . "%");
        }

        if ($request->has(['field', 'order'])) {
            $users->orderBy($request->field, $request->order);
        }

        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $role = Auth()->user()->roles->pluck('name')[0];
        $roles = Role::get();
        if ($role != 'superadmin') {
            $users->whereHas('roles', function ($query) {
                return $query->where('name', '<>', 'superadmin');
            });
            $roles = Role::where('name', '<>', 'superadmin')
                ->where('name', '<>', 'admin')
                ->get();
        }
        $cargos = Cargo::all();

        $sexoSelect[] = [ 'label' => 'Masculino', 'value' => 0 ];
        $sexoSelect[] = [ 'label' => 'Femenino', 'value' => 1 ];

        return Inertia::render('User/Index', [
            'title'         => __('app.label.user'),
            'filters'       => $request->all(['search', 'field', 'order']),
            'perPage'       => (int) $perPage,
            'users'         => $users->with('roles','cargo')->paginate($perPage),
            'roles'         => $roles,
            'cargos'         => $cargos,
            'sexoSelect'         => $sexoSelect,
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
        DB::beginTransaction();
        try {
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
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
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
    public function destroy(User $user)
    {
        try {
            $user->delete();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => $user->name]));
        } catch (\Throwable $th) {
            return back()->with('error', __('app.label.deleted_error', ['name' => $user->name]) . $th->getMessage());
        }
    }
   

    public function destroyBulk(Request $request)
    {
        try {
            $user = User::whereIn('id', $request->id);
            $user->delete();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.user')]));
        } catch (\Throwable $th) {
            return back()->with('error', __('app.label.deleted_error', ['name' => count($request->id) . ' ' . __('app.label.user')]) . $th->getMessage());
        }
    }

    public function FunctionUploadFromEx(Request $request) {
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
        $exten = $request->archivo1->getClientOriginalExtension();
        // Validar que el archivo es de Excel
        if ($exten != 'xlsx' && $exten != 'xls') {
            return back()->with('warning', 'El archivo debe ser de Excel');
        }
        $pesoKilobyte = ((int)($request->archivo1->getSize()))/(1024);
        if ($pesoKilobyte > 256) { //debe pesar menos de 256KB
            return back()->with('warning', 'El archivo debe pesar menos de 256KB');
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
            $messageSuccess1 = 'Usuarios nuevos: '.$CountFilas .($countNoleidos).' no leidas, '.$countNoCargo. ' con cargo erroneo, '. $countSex.' sexo mal escrito '.$countCedulaRepetida. ' cedula repetida.';
            if(count($usuariosActualizados) > 0){
                $StringUsuariosRep = implode(", ",$usuariosActualizados);
                session(['countCedulaRepetida' => 0]);
                return back()->with('success', $messageSuccess1)
                    ->with('warning', 'Usuarios actualizados: '.$StringUsuariosRep);
            }else{
                return back()->with('success', $messageSuccess1);
            }
            // return back()->with('success', __('upload_complete'). $CountFilas);

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


    public function export(Request $request) {
        // $fechaIni = new DateTime($request->ini);
        // $anio = $fechaIni->format('Y');
        // $diaInicial = $fechaIni->format('d');
        // $quincena = $diaInicial < 14 ? '1':'2';
        $quincena = intval($request->quincena);
        
        $year = intval($request->year);
        $month = intval($request->month+1);
        $NumReportesIniFin = $this->CalcularIniFinQuincena($quincena,$month,$year);

        if($NumReportesIniFin['NumReportes'] > 0){
            return Excel::download(new UsersExport($NumReportesIniFin['ini'],$NumReportesIniFin['fin']), "".$year.'Quincena'.$quincena.'DelMes'.$month.".xlsx");
        }else{
            // dd('El numero de reportes en esa quincena es 0');

            // return back()->with('error', 'No hay reportes, en el rango de fechas seleccionadas');
            return redirect()->route('user.uploadexcel')->with('warning', 'No hay reportes, en el rango de fechas seleccionadas');
        }
        // return view('reporte1temp',$ini,$fin);
    }

    public function CalcularIniFinQuincena($quincena,$month,$year){
        $ini = Carbon::createFromFormat('d/m/Y',  '1/'.$month.'/'.$year)->setHour(0)->setminutes(0);
        // dd($ini);
        $fin = Carbon::createFromFormat('d/m/Y',  '1/'.$month.'/'.$year)->setHour(23)->setminutes(0);
        if($quincena == 1){
            // $ini->addDays(-3);
            $fin->addDays(14);//antes era 12
        }else{
            $ini->addDays(15);//
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

        return [
            'NumReportes' => $NumReportes,
            'ini' => $ini,
            'fin' => $fin,
        ];
    }

    public function downloadsigo(Request $request) {
        // $fechaIni = new DateTime($request->ini);
        // $anio = $fechaIni->format('Y');
        // $diaInicial = $fechaIni->format('d');
        // $quincena = $diaInicial < 14 ? '1':'2';
        
        $quincena = intval($request->quincena);
        $year = intval($request->year);
        $month = intval($request->month+1);
        $ini = Carbon::createFromFormat('d/m/Y',  '1/'.$month.'/'.$year)->setHour(0);
        $fin = Carbon::createFromFormat('d/m/Y',  '1/'.$month.'/'.$year)->setHour(23);
        if($quincena == 1){
            // $ini->addDays(-3);
            $fin->addDays(14);//antes era 12            
        }else{
            $ini->addDays(15);//
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
            return Excel::download(new SiigoExport($ini,$fin), "Siigo ".$year.'Quincena'.$quincena.'DelMes'.$month.".xlsx");
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

            $nombresTabla =[//0: como se ven //1 como es la BD
                ["Acciones","#","Centro costo","Trabajador", "valido",   "inicio",       "fin",        "horas trabajadas",   "observaciones"],
                ["b_valido","t_fecha_ini", "t_fecha_fin", "i_horas_trabajadas", "s_observaciones"], //m for money || t for datetime || d date || i for integer || s string || b boolean 
                [null,null,null,null,null,null,null,null,null,null,null,null,null,null] //campos ordenables
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
