<?php

use App\Http\Controllers\CentroCostosController;
use App\Http\Controllers\CentroTableController;
use App\Http\Controllers\ConsignarViaticoController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\DeudaSingularController;
use App\Http\Controllers\EstadisticasController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\ParametrosController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QRController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ServiciosController;
use App\Http\Controllers\SolicitudViaticoController;
use App\Http\Controllers\UbicacionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ViaticoController;
use App\Http\Controllers\ZipController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
require __DIR__.'/auth.php';

// use Stevebauman\Location\Facades\Location;

//Route::get('/', function () {
//    return Inertia::render('Welcome', [
//        'canLogin' => Route::has('login'),
//        'canRegister' => Route::has('register'),
//        'laravelVersion' => Application::VERSION,
//        'phpVersion' => PHP_VERSION,
//    ]);
//});
Route::get('/', function () {
    return redirect('/login');
});

Route::get('/CentroObsoleto', [EstadisticasController::class, 'CentroObsoleto'])->name('CentroObsoleto');
Route::get('/dashboard', [UserController::class, 'Dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/setLang/{locale}', function ($locale) {
    Session::put('locale', $locale);

    return back();
})->name('setlang');

Route::get('/webhooks',[]);

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
    Route::get('/IndexTrashed', [UserController::class, 'IndexTrashed'])->name('IndexTrashed');
    Route::post('/Recontratar/{id}', [UserController::class, 'Recontratar'])->name('Recontratar');
    Route::post('/user/destroy-bulk', [UserController::class, 'destroyBulk'])->name('user.destroy-bulk');
    Route::get('/userUploadExcel', [UserController::class, 'FunctionUploadFromEx'])->name('user.uploadexcel');
    Route::get('/validatingSigo', [UserController::class, 'getNumReportesSiigo'])->name('user.uploadexceSigo');
    Route::post('/userUploadExcelPost', [UserController::class, 'FunctionUploadFromExPost'])->name('user.uploadexcelpost');
    Route::get('/userReportes/{id}', [UserController::class, 'showReporte'])->name('user.showReporte');
    Route::post('/userdestroyDefinitive/{id}', [UserController::class, 'userdestroyDefinitive'])->name('userdestroyDefinitive');
    //</editor-fold>

    Route::resource('/CentroCostos', CentroCostosController::class); //show -> reportes del centro
    Route::resource('/Reportes', ReportesController::class);
    Route::post('/Reportes/destroy-bulk', [ReportesController::class, 'destroyBulk'])->name('reporte.destroy-bulk');
    Route::post('/MassiveReportes', [ReportesController::class, 'MassiveReportes'])->name('MassiveReportes');
    Route::post('/guardarCiudad', [\App\Http\Controllers\DashboardController::class, 'guardarCiudad'])->name('guardarCiudad');

    //# excel
    Route::get('users/export/{NumeroDiasFestivos}/{quincena}/{month}/{year}', [UserController::class, 'export'])->name('reporte1');
    Route::get('users/downloadsigo/{NumeroDiasFestivos}/{quincena}/{month}/{year}', [UserController::class, 'downloadsigo']);
    Route::get('justcc', [ExcelController::class, 'CentroCostos']);

    //19abril2024
    Route::resource('/Servicios', ServiciosController::class);
    Route::resource('/ubicacion', UbicacionController::class);

    //yan
    Route::get('/qrscanner', [QRController::class, 'index'])->name('qrscanner');
    //16mayo2024
    Route::put('/eporte_Super_Edit/{id}', [ReportesController::class, 'Reporte_Super_Edit'])->name('Reporte_Super_Edit');
    Route::get('/CentroCostoTable/{id}', [CentroTableController::class, 'table'])->name('CentroCostos.table');
    Route::get('/AproxDestroy', [CentroCostosController::class, 'AproxDestroy'])->name('AproxDestroy');
    Route::get('/DescompresionDespliegue/{esAmbientePruebas}', [ZipController::class, 'DescompresionDespliegue']);
    Route::get('/JustDeploy/{pruebas}', [\App\Http\Controllers\ScriptController::class, 'JustDeploy']);

    Route::resource("/cotizacion", CotizacionController::class);
    Route::post('/cotizacion/destroy-bulk', [CotizacionController::class, 'destroyBulk'])->name('cotizacion.destroy-bulk');
    Route::post('/viatico/destroy-bulk', [ViaticoController::class, 'destroyBulk'])->name('viatico.destroy-bulk');
    Route::put('/cotiza/{id}', [CotizacionController::class, 'update2'])->name('cotizacion.update2');
    Route::put('/cotiza3/{id}', [CotizacionController::class, 'update3'])->name('cotizacion.update3');
    Route::get('/deuda', [DeudaSingularController::class, 'index'])->name('deuda.index');
	Route::resource("/viatico", ViaticoController::class);
	
	
	Route::resource("/consignarViatico", ConsignarViaticoController::class);
	
	
    Route::put('/viaticoupdate2/{id}', [SolicitudViaticoController::class, 'viaticoupdate2'])->name('viaticoupdate2');
    Route::get('/viatico2', [ViaticoController::class, 'viatico2'])->name('viatico2');
    Route::put('/legalizarviatico/{id}', [SolicitudViaticoController::class, 'legalizarviatico'])->name('legalizarviatico');
    Route::get('/resetPassword/{id}', [UserController::class, 'resetPassword'])->name('resetPassword');
	Route::resource("/material", \App\Http\Controllers\MaterialController::class);
	Route::resource("/zona", \App\Http\Controllers\ZonaController::class);
	Route::resource("/desarrollo", \App\Http\Controllers\DesarrolloController::class);
	Route::resource("/pagodesarrollo", \App\Http\Controllers\PagodesarrolloController::class);
    
    Route::put('/updatePago/{id}', [\App\Http\Controllers\DesarrolloController::class, 'updatePago'])->name('updatePago');
	Route::resource("/legalizacionviatico", \App\Http\Controllers\LegalizacionviaticoController::class);
    Route::get('/obtenerCentroCostosUltimaQuincena', [ReportesController::class, 'obtenerCentroCostosUltimaQuincena'])->name('obtenerCentroCostosUltimaQuincena');
    Route::get('/FuncionPruebas', [ReportesController::class, 'FuncionPruebas'])->name('FuncionPruebas');
    Route::get('/FuncionPruebas2', [ReportesController::class, 'FuncionPruebas2'])->name('FuncionPruebas2');
	Route::resource("/solicitud_viatico", SolicitudViaticoController::class);
	//aquipues
}); //fin verified






// <editor-fold desc="Artisan">
Route::get('/exception', function () {
    $ReportesDeHoy = \App\Models\Reporte::Where('fecha_ini', \Carbon\Carbon::today())->first();
    if ($ReportesDeHoy) {
        throw new Exception('Reportes de hoy: '.$ReportesDeHoy->horas_trabajadas);
    }
    throw new Exception('Probandof excepciones y enrutamiento. La prueba ha concluido exitosamente.');
});

Route::get('/clear-c', function () {
    // Artisan::call('optimize');
    Artisan::call('optimize:clear');

    return 'Optimizacion finalizada';
    // throw new Exception('Optimizacion finalizada!');
});
Route::get('/back-up', function () {
    $result = Artisan::call('backup:run');
    $output = Artisan::output();
    if ($result === 0) {
        // Éxito
        return response()->json(['status' => 'success', 'message' => 'Backup completed successfully!', 'output' => $output]);
    } else {
        // Error
        return response()->json(['status' => 'error', 'message' => 'Backup failed!', 'output' => $output]);
        //         throw new Exception('Backup failed!'. $output);
    }
});

Route::get('/test-email', function () {
    try {
        \Illuminate\Support\Facades\Mail::raw('Este es un correo de prueba.', function ($message) {
            $message->to('ajelof2@gmail.com')
                ->subject('Correo de prueba');
        });
        return 'Correo enviado con éxito.';
    } catch (\Exception $e) {
        return 'Error al enviar el correo: ' . $e->getMessage();
    }
});
//</editor-fold>
