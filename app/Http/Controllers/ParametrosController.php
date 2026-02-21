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
use Illuminate\Validation\ValidationException;
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
		$parametros = Parametro::orderBy('anio', 'desc')->get();
		$titulo = __('app.label.Params');
		$numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, 'Parametros index')); //0:error, 1:estudiante,  2: profesor, 3:++ )

		$nombresTabla = [ //0: como se ven //1 como es la BD
			[
				'ID',
				'Año',
				'Dias Gabela',
				'Horas necesarias diarias',
				'Horas necesarias semanales',
				'Maximo Horas Semanales',
				'Horas del Mes (30 días)',
				'subsidio de transporte dia/quincena',
				'salario minimo',
				'Minimo Material',
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
				's_id',
				's_anio',
				's_s_Dias_gabela',
				's_HORAS_ORDINARIAS',
				's_HORAS_NECESARIAS_SEMANA',
				's_MAXIMO_HORAS_SEMANALES',
				's_HORAS_DEL_MES_30_DIAS',
				'o_subsidio_de_transporte_dia',
				'o_salario_minimo',
				'o_minimo_material',
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

		if ($numberPermissions != 3) { //supervisor
			array_unshift($nombresTabla[0], 'Editar');
		//            array_unshift($nombresTabla[1],'Editar');
		}

		return Inertia::render('Parametros/Index', [ //carpetaP
			'title' => $titulo,
			'fromController' => $parametros,
			'breadcrumbs' => [['label' => __('app.label.Reportes'), 'href' => route('Reportes.index')]],
			'nombresTabla' => $nombresTabla,
			'numberPermissions' => $numberPermissions,
		]);
	}

	public function create()
	{
	}

	public function store(ParametroRequest $request)
	{
		$parametro = Parametro::create([
			'anio' => $request->anio,
			'salario_minimo' => $request->salario_minimo,
			'subsidio_de_transporte_dia' => $request->subsidio_de_transporte_dia,
			's_Dias_gabela' => $request->s_Dias_gabela,
			'HORAS_NECESARIAS_SEMANA' => $request->HORAS_NECESARIAS_SEMANA,
			'HORAS_ORDINARIAS' => $request->HORAS_ORDINARIAS,
			'HORAS_DEL_MES_30_DIAS' => $request->HORAS_DEL_MES_30_DIAS,
			'MAXIMO_HORAS_SEMANALES' => $request->MAXIMO_HORAS_SEMANALES,
			'valor_maximo_subsidio_de_transporte' => 2 * $request->salario_minimo,
			'porcentaje_diurno' => $request->porcentaje_diurno ?? 0,
			'porcentaje_nocturno' => $request->porcentaje_nocturno ?? 0,
			'porcentaje_extra_diurno' => $request->porcentaje_extra_diurno ?? 0,
			'porcentaje_extra_nocturno' => $request->porcentaje_extra_nocturno ?? 0,
			'porcentaje_dominical_diurno' => $request->porcentaje_dominical_diurno ?? 0,
			'porcentaje_dominical_nocturno' => $request->porcentaje_dominical_nocturno ?? 0,
			'porcentaje_dominical_extra_diurno' => $request->porcentaje_dominical_extra_diurno ?? 0,
			'porcentaje_dominical_extra_nocturno' => $request->porcentaje_dominical_extra_nocturno ?? 0,
			'minimo_material' => $request->minimo_material ?? 0,
		]);

		Myhelp::EscribirEnLog($this, static::class , 'Parámetro creado para el año ' . $request->anio, false);
		return redirect()->route('Parametros.index')->with('success', 'Parámetro creado correctamente');
	}

	public function update(ParametroRequest $request, $id)
	{
		DB::beginTransaction();
		$numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, 'Parametros update'));
		$eluser = Myhelp::AuthU()->name;

		try {
			// DB::transaction(function () use ($request, $id, $numberPermissions,$eluser) {

			$parametro = Parametro::findOrFail($id);

			$nuevoSalario = (int)$request->salario_minimo;
			$nuevoSubsidio = (int)$request->subsidio_de_transporte_dia;
			$nuevasHoras = (int)$request->HORAS_NECESARIAS_SEMANA;
			$nuevasOrdinarias = (int)$request->HORAS_ORDINARIAS;
			$nuevoAnio = (int)$request->anio;

			$yaActualizadoEsteAnio = $parametro->updated_at->isCurrentYear();

			// 🔒 Restricción para usuarios sin alto permiso
			if ($numberPermissions < 10 && $yaActualizadoEsteAnio) {

				$intentaCambiarValoresProtegidos = $parametro->salario_minimo != $nuevoSalario || $parametro->subsidio_de_transporte_dia != $nuevoSubsidio;

				if ($intentaCambiarValoresProtegidos) {
					Myhelp::EscribirEnLog($this, static::class , 'Intento de cambio normal bloqueado a ' . $eluser, false);
					throw ValidationException::withMessages(['salario_minimo' => 'Estos valores solo pueden modificarse una vez por año.']);
				}
			}
			// 🔒 Solo el super puede cambiar horas ordinarias
			if ($numberPermissions < 10) {

				$intentaCambiarOrdinarias = $parametro->HORAS_ORDINARIAS != $nuevasOrdinarias;

				if ($intentaCambiarOrdinarias) {
					Myhelp::EscribirEnLog($this, static::class , 'Intento de cambio ordinarias bloqueado a ' . $eluser, false);
					throw ValidationException::withMessages(['HORAS_ORDINARIAS' => 'Estos valores solo pueden modificarse con permisos.']);
				}
			}

			// ✏️ Actualización
			$parametro->update([
				'anio' => $nuevoAnio,
				'salario_minimo' => $nuevoSalario,
				'subsidio_de_transporte_dia' => $nuevoSubsidio,
				's_Dias_gabela' => (int)$request->s_Dias_gabela,
				'HORAS_NECESARIAS_SEMANA' => $nuevasHoras,
				'valor_maximo_subsidio_de_transporte' => 2 * $nuevoSalario,
				'HORAS_ORDINARIAS' => $nuevasOrdinarias,
				'HORAS_DEL_MES_30_DIAS' => (int)$request->HORAS_DEL_MES_30_DIAS,
				'MAXIMO_HORAS_SEMANALES' => (int)$request->MAXIMO_HORAS_SEMANALES,
				'minimo_material' => (int)$request->minimo_material,

				'porcentaje_diurno' => $request->porcentaje_diurno,
				'porcentaje_nocturno' => $request->porcentaje_nocturno,
				'porcentaje_extra_diurno' => $request->porcentaje_extra_diurno,
				'porcentaje_extra_nocturno' => $request->porcentaje_extra_nocturno,
				'porcentaje_dominical_diurno' => $request->porcentaje_dominical_diurno,
				'porcentaje_dominical_nocturno' => $request->porcentaje_dominical_nocturno,
				'porcentaje_dominical_extra_diurno' => $request->porcentaje_dominical_extra_diurno,
				'porcentaje_dominical_extra_nocturno' => $request->porcentaje_dominical_extra_nocturno,
			]);
			DB::commit();
			Myhelp::EscribirEnLog($this, static::class , 'Parámetros actualizados por ' . auth()->user()->name, false);
			return back()->with('success', __('app.label.updated_successfully', ['name' => 'Parametro']));

		// });
		}
		catch (\Throwable $th) {
			DB::rollback();
			$mensaje = ' U -> ' . Auth::user()->name . ' Accedio a la vista parametros y Fallo la operacion: ' . $th->getMessage();
			Myhelp::EscribirEnLog($this, 'ParametrosController', $mensaje, false, true);

			return back()->with('error', __('app.label.updated_error', ['name' => __('app.label.Parametross')]) . $th->getMessage());
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function destroy($id)
	{
		$parametro = Parametro::findOrFail($id);
		$parametro->delete();

		Myhelp::EscribirEnLog($this, static::class , 'Parámetro eliminado', false);
		return back()->with('success', 'Parámetro eliminado correctamente');
	}
}
