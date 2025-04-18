<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\Http\Controllers\Controller;

use App\Models\Parametro;
use App\Http\Requests\ParametroRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ParametrosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $parametros = Parametro::all();
        $titulo = __('app.label.Params');
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, 'Parametros index')); //0:error, 1:estudiante,  2: profesor, 3:++ )

        $nombresTabla = [ //0: como se ven //1 como es la BD
            [
                'Dias Gabela',
                'Horas necesarias diarias',
                'Horas necesarias semanales',
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
                's_s_Dias_gabela',
                's_HORAS_ORDINARIAS',
                's_HORAS_NECESARIAS_SEMANA',
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

        if($numberPermissions != 3){//supervisor
            array_unshift($nombresTabla[0],'Editar');
//            array_unshift($nombresTabla[1],'Editar');
        }
        return Inertia::render('Parametros/Index', [ //carpetaP
            'title'          =>  $titulo,
            'fromController' =>  $parametros,
            'breadcrumbs'    =>  [['label' => __('app.label.Reportes'), 'href' => route('Reportes.index')]],
            'nombresTabla'   =>  $nombresTabla,
        ]);
    }
    public function create(){}

    public function store(ParametroRequest $request){
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
        $parametro->s_Dias_gabela = $request->input('s_Dias_gabela');
        $parametro->save();
        return redirect()->route('parametros.index');
    }

    public function update(ParametroRequest $request, $id){
        DB::beginTransaction();
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, 'Parametros update'));

        try {
            $parametro = Parametro::findOrFail($id);
            $parametro->subsidio_de_transporte_dia = $request->input('subsidio_de_transporte_dia');
            $parametro->salario_minimo = $request->input('salario_minimo');
            $parametro->s_Dias_gabela = $request->input('s_Dias_gabela');
            $parametro->valor_maximo_subsidio_de_transporte = 2*(int)$request->input('salario_minimo');
            $today = Carbon::now();

            $last_update = Carbon::parse($parametro->updated_at);
            $diff = $today->isSameYear($last_update);

            if ($numberPermissions < 10 && $diff) {
                if ($parametro->HORAS_NECESARIAS_SEMANA != (int)($request->input('HORAS_NECESARIAS_SEMANA'))) {
                    Myhelp::EscribirEnLog($this, 'ParametrosController',' No dejo actualizar parametros',false);
                    return back()->with('error', 'No se puede actualizar las horas necesarias por semana mas de una vez por año');
                }
                $parametro->save();
                DB::commit();
                return back()->with('success', __('app.label.updated_successfully', ['name' => 'Parametros']));
            } else {
                $parametro->HORAS_NECESARIAS_SEMANA = $request->input('HORAS_NECESARIAS_SEMANA');
                $parametro->save();
                DB::commit();
                $mensaje = ' U -> ' . Auth::user()->name . '  actualizo los paramaetros:'.
                'subsidio_de_transporte_dia = ' . $parametro->subsidio_de_transporte_dia .
                ' salario_minimo =' . $parametro->salario_minimo;

                Myhelp::EscribirEnLog($this, 'ParametrosController',$mensaje,false);

                return back()->with('success', __('app.label.updated_successfully', ['name' => 'Parametros']));
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $mensaje = ' U -> ' . Auth::user()->name . ' Accedio a la vista parametros y Fallo la operacion: ' . $th->getMessage();
            Myhelp::EscribirEnLog($this, 'ParametrosController',$mensaje,false,true);

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
