<?php

use App\Http\Controllers\CentroCostosController;
use App\Http\Controllers\ParametrosController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Models\CentroCosto;
use App\Models\Permission;
use App\Models\Reporte;
use App\Models\Role; use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Application; 
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route; use Illuminate\Support\Facades\Session; use Inertia\Inertia;


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $Authuser = Auth::user();
    $permissions = auth()->user()->roles->pluck('name')[0];
    $ultimos5dias = null;
    if($permissions === "operator") { //admin | validador
        $reportes = (int) Reporte::Where('user_id', $Authuser->id)->count();
        
    }else{
        $reportes = (int) Reporte::count();
        
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

        $ultimasHoras = [
            'Horas Semana Pasada' => Reporte::whereValido(1)->whereBetween('fecha_ini', [Carbon::now()->addDays(-7)->startOfWeek(),Carbon::now()->addDays(-7)->endOfWeek()])
                ->sum('horas_trabajadas'),
            'Horas Semana' => Reporte::whereValido(1)->whereBetween('fecha_ini', [Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()])
                ->sum('horas_trabajadas'),
        ];
        //!toremember
        $usuariosConRol = User::whereHas("roles", function($q){ $q->where("name", "operator"); })->take(6)->get();

        foreach ($usuariosConRol as $value) {
            $BooleanreportoHoy = Reporte::where('user_id',$value->id)->whereDate('fecha_fin',Carbon::today())->first();
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

    return Inertia::render('Dashboard', 
    [
        'users'         => (int) User::count(),
        'roles'         => (int) Role::count(),
        'permissions'   => (int) Permission::count(),
        'reportes'   => $reportes,
        'ultimos5dias' => $ultimos5dias,
        'ultimasHoras' => $ultimasHoras ?? [],
        'diasNovalidos' => $diasNovalidos ?? [],
        'trabajadoresHoy' => $trabajadoresHoy ?? [],
        'centrosHoy' => $centrosHoy ?? [],
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/setLang/{locale}', function ($locale) { Session::put('locale', $locale); return back(); })->name('setlang');

Route::middleware('auth', 'verified')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('/user', UserController::class)->except('create', 'show', 'edit');
    Route::post('/user/destroy-bulk', [UserController::class, 'destroyBulk'])->name('user.destroy-bulk');
    
    Route::get('/userUploadExcel', [UserController::class,'FunctionUploadFromEx'])->name('user.uploadexcel');
    Route::post('/userUploadExcelPost', [UserController::class,'FunctionUploadFromExPost'])->name('user.uploadexcelpost');
    
    Route::resource('/role', RoleController::class)->except('create', 'show', 'edit');
    Route::post('/role/destroy-bulk', [RoleController::class, 'destroyBulk'])->name('role.destroy-bulk');

    Route::resource('/permission', PermissionController::class)->except('create', 'show', 'edit');
    Route::post('/permission/destroy-bulk', [PermissionController::class, 'destroyBulk'])->name('permission.destroy-bulk');
    
    Route::resource('/CentroCostos', CentroCostosController::class);//show -> reportes del centro
    Route::resource('/Reportes', ReportesController::class);
    
    Route::get('/userReportes/{id}', [UserController::class,'showReporte'])->name('user.showReporte');
    
    Route::resource('/Parametros', ParametrosController::class);


    //# excel
    Route::get('users/export/{quincena}/{month}/{year}', [UserController::class, 'export'])->name('reporte1');
    // Route::get('users/export', [UserController::class, 'export'])->name('reporte1');
    
});

require __DIR__.'/auth.php';


// <editor-fold desc="Artisan">
    Route::get('/exception',function(){
        throw new Exception('Probando excepciones y enrutamiento. La prueba ha concluido exitosamente.');
    });

    Route::get('/clear-c', function () {
        // Artisan::call('optimize');
        Artisan::call('optimize:clear');
        return "Optimizacion finalizada";
        // throw new Exception('Optimizacion finalizada!');
    });
//</editor-fold>
