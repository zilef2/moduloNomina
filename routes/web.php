<?php

use App\Http\Controllers\CentroCostosController;
use App\Http\Controllers\ParametrosController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\ServiciosController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route; use Illuminate\Support\Facades\Session; use Inertia\Inertia;
// use Stevebauman\Location\Facades\Location;

//Route::get('/', function () {
//    return Inertia::render('Welcome', [
//        'canLogin' => Route::has('login'),
//        'canRegister' => Route::has('register'),
//        'laravelVersion' => Application::VERSION,
//        'phpVersion' => PHP_VERSION,
//    ]);
//});
Route::get('/', function () { return redirect('/login'); });

Route::get('/dashboard', [UserController::class, 'Dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/setLang/{locale}', function ($locale) { Session::put('locale', $locale); return back(); })->name('setlang');

Route::middleware('auth', 'verified')->group(function () {

    //<editor-fold desc="profile - role - permission">
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('/role', RoleController::class)->except('create', 'show', 'edit');
    Route::post('/role/destroy-bulk', [RoleController::class, 'destroyBulk'])->name('role.destroy-bulk');
    Route::resource('/permission', PermissionController::class)->except('create', 'show', 'edit');
    Route::post('/permission/destroy-bulk', [PermissionController::class, 'destroyBulk'])->name('permission.destroy-bulk');
    //</editor-fold>
    Route::resource('/Parametros', ParametrosController::class);

    //<editor-fold desc="User">
    Route::resource('/user', UserController::class)->except('create', 'show', 'edit');
    Route::get('/IndexTrashed', [UserController::class,'IndexTrashed'])->name('IndexTrashed');
    Route::post('/Recontratar/{id}', [UserController::class,'Recontratar'])->name('Recontratar');
    Route::post('/user/destroy-bulk', [UserController::class, 'destroyBulk'])->name('user.destroy-bulk');
    Route::get('/userUploadExcel', [UserController::class,'FunctionUploadFromEx'])->name('user.uploadexcel');
    Route::get('/validatingSigo', [UserController::class,'getNumReportesSiigo'])->name('user.uploadexceSigo');
    Route::post('/userUploadExcelPost', [UserController::class,'FunctionUploadFromExPost'])->name('user.uploadexcelpost');
    Route::get('/userReportes/{id}', [UserController::class,'showReporte'])->name('user.showReporte');
    Route::post('/userdestroyDefinitive/{id}', [UserController::class,'userdestroyDefinitive'])->name('userdestroyDefinitive');
    //</editor-fold>


    Route::resource('/CentroCostos', CentroCostosController::class);//show -> reportes del centro
    Route::resource('/Reportes', ReportesController::class);
    Route::post('/Reportes/destroy-bulk', [ReportesController::class, 'destroyBulk'])->name('reporte.destroy-bulk');
    Route::post('/MassiveReportes', [ReportesController::class, 'MassiveReportes'])->name('MassiveReportes');


    //# excel
    Route::get('users/export/{NumeroDiasFestivos}/{quincena}/{month}/{year}', [UserController::class, 'export'])->name('reporte1');
    Route::get('users/downloadsigo/{NumeroDiasFestivos}/{quincena}/{month}/{year}', [UserController::class, 'downloadsigo']);

    //19abril2024
    Route::resource('/Servicios', ServiciosController::class);
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
