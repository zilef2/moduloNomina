<?php

use App\Http\Controllers\CentroCostosController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

use App\Models\Permission;
use App\Models\Reporte;
use App\Models\Role; use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Application; 
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
    $permissions = $Authuser->getRoleNames()->first();
    $countSessiones = 0;
    $ultimaSesion ='';
    $ultimos5dias = null;
    if($permissions === "operator") { //admin | validador
        $reportes = (int) Reporte::Where('user_id', $Authuser->id)->count();
        
    }else{
        $reportes = (int) Reporte::count();
        
        $this->sesiones = DB::table('sessions')->get();
        foreach ($this->sesiones as $sesio) {
            if($sesio->user_id !== null){
                $sesio->elUser = User::find($sesio->user_id);
                $countSessiones ++;
            }
            $ultimaSesion = Carbon::parse($sesio->last_activity)
                ->diffForHumans(Carbon::now());
        }

        $ultimos5dias = [
            'Mes pasado' => Reporte::whereValido(1)->where('fecha_ini','<', Carbon::today()->addMonth(-1)) ->get()->count(),

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
    }

    return Inertia::render('Dashboard', 
    [
        'users'         => (int) User::count(),
        'roles'         => (int) Role::count(),
        'permissions'   => (int) Permission::count(),
        'reportes'   => $reportes,
        'countSessiones'   => $countSessiones,
        'ultimaSesion'   => $ultimaSesion,
        'ultimos5dias' => $ultimos5dias,
        'ultimasHoras' => $ultimasHoras,
        'diasNovalidos' => $diasNovalidos,
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
    
    Route::resource('/CentroCostos', CentroCostosController::class);
    Route::resource('/Reportes', ReportesController::class);


    //# excel
    Route::get('users/export/', [UserController::class, 'export']);
    
});

require __DIR__.'/auth.php';


// <editor-fold desc="Artisan">
    Route::get('/exception',function(){
        throw new Exception('Probando excepciones y enrutamiento. La prueba ha concluido exitosamente.');
    });

    Route::get('/foo', function () {
        if (file_exists(public_path('storage'))){
            return 'Ya existe';
        }
        App('files')->link(
            storage_path('App/public'), public_path('storage')
        );return 'Listo';
    });

    Route::get('/clear-c', function () {
        // Artisan::call('optimize');
        Artisan::call('optimize:clear');
        return "Optimizacion finalizada";
        // throw new Exception('Optimizacion finalizada!');
    });

    Route::get('/tmantenimientot-ñ', function () {
        echo Artisan::call('down --secret="token-it"');
        return "Aplicación abajo: token-it";
    });
    Route::get('/Arriba', function () {
        echo Artisan::call('up');
        return "Aplicación funcionando";
    });

//</editor-fold>
