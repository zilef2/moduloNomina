<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Reporte;
use App\Http\Requests\ReporteRequest;

class ReportesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reportes= Reporte::all();
        return view('reportes.index', ['reportes'=>$reportes]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('reportes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ReporteRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReporteRequest $request)
    {
        $reporte = new Reporte;
		$reporte->fecha_ini = $request->input('fecha_ini');
		$reporte->fecha_fin = $request->input('fecha_fin');
		$reporte->horas_trabajadas = $request->input('horas_trabajadas');
		$reporte->valido = $request->input('valido');
		$reporte->observaciones = $request->input('observaciones');
        $reporte->save();

        return redirect()->route('reportes.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $reporte = Reporte::findOrFail($id);
        return view('reportes.show',['reporte'=>$reporte]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $reporte = Reporte::findOrFail($id);
        return view('reportes.edit',['reporte'=>$reporte]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ReporteRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ReporteRequest $request, $id)
    {
        $reporte = Reporte::findOrFail($id);
		$reporte->fecha_ini = $request->input('fecha_ini');
		$reporte->fecha_fin = $request->input('fecha_fin');
		$reporte->horas_trabajadas = $request->input('horas_trabajadas');
		$reporte->valido = $request->input('valido');
		$reporte->observaciones = $request->input('observaciones');
        $reporte->save();

        return redirect()->route('reportes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $reporte = Reporte::findOrFail($id);
        $reporte->delete();

        return redirect()->route('reportes.index');
    }
}
