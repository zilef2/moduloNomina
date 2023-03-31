<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Parametro;
use App\Http\Requests\ParametroRequest;

class ParametrosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $parametros= Parametro::all();
        return view('parametros.index', ['parametros'=>$parametros]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('parametros.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ParametroRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ParametroRequest $request)
    {
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $parametro = Parametro::findOrFail($id);
        return view('parametros.show',['parametro'=>$parametro]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $parametro = Parametro::findOrFail($id);
        return view('parametros.edit',['parametro'=>$parametro]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ParametroRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ParametroRequest $request, $id)
    {
        $parametro = Parametro::findOrFail($id);
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $parametro = Parametro::findOrFail($id);
        $parametro->delete();

        return redirect()->route('parametros.index');
    }
}
