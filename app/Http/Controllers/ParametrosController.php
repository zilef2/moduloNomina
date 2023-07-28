<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Parametro;
use App\Http\Requests\ParametroRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ParametrosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $parametros= Parametro::all();
        $titulo = __('app.label.Params');

        $nombresTabla =[//0: como se ven //1 como es la BD
            [
                'subsidio de transporte dia/quincena', 
                // 'subsidio de transporte',
                'salario minimo',
                'porcentaje diurno',
                'porcentaje nocturno',
                'porcentaje extra diurno',
                'porcentaje extra nocturno',
                'porcentaje dominical diurno',
                'porcentaje dominical nocturno',
                'porcentaje dominical extra diurno',
                'porcentaje dominical extra nocturno'
            ],
            [
                'o_subsidio_de_transporte_dia',
                // 'o_subsidio_de_transporte',
                'o_salario_minimo',
                'p_porcentaje_diurno',
                'p_porcentaje_nocturno',
                'p_porcentaje_extra_diurno',
                'p_porcentaje_extra_nocturno',
                'p_porcentaje_dominical_diurno',
                'p_porcentaje_dominical_nocturno',
                'p_porcentaje_dominical_extra_diurno',
                'p_porcentaje_dominical_extra_nocturno'
            ],
        ];
        return Inertia::render('Parametros/Index', [ //carpetaP
            'title'          =>  $titulo,
            'fromController' =>  $parametros,
            'breadcrumbs'    =>  [['label' => __('app.label.Reportes'), 'href' => route('Reportes.index')]],
            'nombresTabla'   =>  $nombresTabla,
        ]);
    }

    
    public function create()
    {
    }

    
    public function store(ParametroRequest $request) {
        $parametro = new Parametro;
		$parametro->subsidio_de_transporte = $request->input('subsidio_de_transporte');
		$parametro->salario_minimo = $request->input('salario_minimo');
		$parametro->porcentaje_diurno = $request->input('porcentaje_diurno');
		$parametro->porcentaje_nocturno = $request->input('porcentaje_nocturno');
		$parametro->porcentaje_extra_diurno = $request->input('porcentaje_extra_diurno');
		$parametro->porcentaje_extra_nocturno = $request->input('porcentaje_extra_nocturno');
		$parametro->porcentaje_dominical_diurno = $request->input('porcentaje_dominical_diurno');
		$parametro->porcentaje_dominical_nocturno = $request->input('porcentaje_dominical_nocturno');
		$parametro->porcentaje_dominical_extra_diurno = $request->input('porcentaje_dominical_extra_diurno');
		$parametro->porcentaje_dominical_extra_nocturno = $request->input('porcentaje_dominical_extra_nocturno');
        $parametro->save();
        return redirect()->route('parametros.index');
    }

    public function update(ParametroRequest $request, $id) {
        DB::beginTransaction();
        $ListaControladoresYnombreClase = (explode('\\',get_class($this))); $nombreC = end($ListaControladoresYnombreClase);

        try {
            $parametro = Parametro::findOrFail($id);
            $parametro->subsidio_de_transporte_dia = $request->input('subsidio_de_transporte_dia');
            $parametro->salario_minimo = $request->input('salario_minimo');
            // $parametro->porcentaje_diurno = $request->input('porcentaje_diurno');
            // $parametro->porcentaje_nocturno = $request->input('porcentaje_nocturno');
            // $parametro->porcentaje_extra_diurno = $request->input('porcentaje_extra_diurno');
            // $parametro->porcentaje_extra_nocturno = $request->input('porcentaje_extra_nocturno');
            // $parametro->porcentaje_dominical_diurno = $request->input('porcentaje_dominical_diurno');
            // $parametro->porcentaje_dominical_nocturno = $request->input('porcentaje_dominical_nocturno');
            // $parametro->porcentaje_dominical_extra_diurno = $request->input('porcentaje_dominical_extra_diurno');
            // $parametro->porcentaje_dominical_extra_nocturno = $request->input('porcentaje_dominical_extra_nocturno');
            $parametro->save();
            DB::commit();
            Log::info(' U -> '.Auth::user()->name. ' Accedio a la vista ' .$nombreC . ' y actualizo los paramaetros:
                subsidio_de_transporte_dia = '.$parametro->subsidio_de_transporte_dia.
                'salario_minimo ='.$parametro->salario_minimo 
            );
            return back()->with('success', __('app.label.updated_successfully', ['name' => 'Parametros']));
        } catch (\Throwable $th) {
            DB::rollback();
            Log::critical(' U -> '.Auth::user()->name. ' Accedio a la vista ' .$nombreC. ' Fallo la operacion: '.$th->getMessage());
            return back()->with('error', __('app.label.updated_error', ['name' => __('app.label.Parametross')]) . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // $parametro = Parametro::findOrFail($id);
        // $parametro->delete();

        // return redirect()->route('parametros.index');
    }
}
