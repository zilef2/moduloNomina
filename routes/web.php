<?php

use App\Http\Controllers\CentroCostosController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

use App\Models\Permission;
use App\Models\Reporte;
use App\Models\Role; use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Application; use Illuminate\Support\Facades\Artisan;
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
            Reporte::whereDay('created_at', Carbon::today()->addDays(-2))->get()->count(),
            Reporte::whereDay('created_at', Carbon::yesterday())->get()->count(),
            Reporte::whereDay('created_at', Carbon::today())->get()->count(),
        ];
        // dd($ultimos5dias);
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
    ]);


})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/setLang/{locale}', function ($locale) {
    Session::put('locale', $locale); 
    return back();
})->name('setlang');

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
        Artisan::call('optimize');
        Artisan::call('optimize:clear');
        return "Optimizacion finalizada";
        // throw new Exception('Optimizacion finalizada!');
    });

    Route::get('/tmantenimiento', function () {
        echo Artisan::call('down --secret="token-it"');
        return "Aplicación abajo: token-it";
    });
    Route::get('/Arriba', function () {
        echo Artisan::call('up');
        return "Aplicación funcionando";
    });

//</editor-fold>
