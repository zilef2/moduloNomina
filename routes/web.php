<?php

use App\Http\Controllers\CentroCostosController;
use App\Http\Controllers\ParametrosController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application; 
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route; use Illuminate\Support\Facades\Session; use Inertia\Inertia;
// use Stevebauman\Location\Facades\Location;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [UserController::class, 'Dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

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
    Route::get('users/export/{NumeroDiasFestivos}/{quincena}/{month}/{year}', [UserController::class, 'export'])->name('reporte1');
    Route::get('users/downloadsigo/{NumeroDiasFestivos}/{quincena}/{month}/{year}', [UserController::class, 'downloadsigo']);
    
    
    //# LocationRequest
    // Route::get('/asd', [UserController::class,'asd'])->name('asd');
    
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
