<?php

namespace App\Http\Controllers;

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
use App\Models\CentroCosto;
use App\Models\Reporte;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{

    public function __construct()
    {

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
    public function index(UserIndexRequest $request)
    {
        $users = User::query();
        if ($request->has('search')) {
            $users->where('name', 'LIKE', "%" . $request->search . "%");
            $users->orWhere('email', 'LIKE', "%" . $request->search . "%");
        }
        if ($request->has(['field', 'order'])) {
            $users->orderBy($request->field, $request->order);
        }
        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $role = auth()->user()->roles->pluck('name')[0];
        $roles = Role::get();
        if ($role != 'superadmin') {
            $users->whereHas('roles', function ($query) {
                return $query->where('name', '<>', 'superadmin');
            });
            $roles = Role::where('name', '<>', 'superadmin')->get();
        }
        return Inertia::render('User/Index', [
            'title'         => __('app.label.user'),
            'filters'       => $request->all(['search', 'field', 'order']),
            'perPage'       => (int) $perPage,
            'users'         => $users->with('roles')->paginate($perPage),
            'roles'         => $roles,
            'breadcrumbs'   => [['label' => __('app.label.user'), 'href' => route('user.index')]],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            $user->assignRole($request->role);
            DB::commit();
            return back()->with('success', __('app.label.created_successfully', ['name' => $user->name]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.created_error', ['name' => __('app.label.user')]) . $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $user->update([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => $request->password ? Hash::make($request->password) : $user->password,
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

    public function FunctionUploadFromEx()
    {
        
        return Inertia::render('User/uploadFromExcel', [
            'title'         => __('app.label.user'),
            'breadcrumbs'   => [['label' => __('app.label.user'), 'href' => route('user.index')]],
        ]);
    }

    public function FunctionUploadFromExPost(Request $request)
    {

        $exten = $request->archivo1->getClientOriginalExtension();
        // // Validar que el archivo es de Excel
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

            $nuevosUsuarios = session('CountFilas');
            return back()->with('success', 'upload_complete'. $nuevosUsuarios);
            // return back()->with('success', __('upload_complete'). $nuevosUsuarios);

        } catch (\Throwable $th) {
            return back()->with('error', 'Error en el proceso de Excel' . $th->getMessage());
        }
    }

    public function export() 
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }

    public function showReporte(Request $request, $id) {
        // $centroCostos = centroCosto::findOrFail($id);
        $Reportes = Reporte::query();
        
        $titulo = __('app.label.Reportes');
        $Authuser = Auth::user();
        $permissions = $Authuser->getRoleNames()->first();
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
        
        if($permissions === "operator") { //admin | validador
            $Reportes->whereUser_id($Authuser->id);

            if ($request->has(['field', 'order'])) {
                $Reportes->orderBy($request->field, $request->order);
            }else{
                $Reportes->orderBy('fecha_ini');
            }
            $perPage = $request->has('perPage') ? $request->perPage : 10;

            $nombresTabla =[//0: como se ven //1 como es la BD //2??
                ["Acciones","#","Centro costo","Trabajador","inicio", "fin", "horas trabajadas", "valido", "observaciones"],
                ["t_fecha_ini", "t_fecha_fin", "i_horas_trabajadas", "b_valido", "s_observaciones"], //m for money || t for datetime || d date || i for integer || s string || b boolean 
                [null,null,null,null,"t_fecha_ini", "t_fecha_fin", "i_horas_trabajadas", "b_valido", "s_observaciones"] //campos ordenables
            ];

        $quincena = [];
            
        }else{ // not operator
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
        }
// dd($quincena);

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
    }

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
}
