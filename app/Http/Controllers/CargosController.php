<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Cargo;
use App\Http\Requests\CargoRequest;

class CargosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cargos= Cargo::all();
        return view('cargos.index', ['cargos'=>$cargos]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('cargos.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CargoRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CargoRequest $request)
    {
        $cargo = new Cargo;
		$cargo->nombre = $request->input('nombre');
        $cargo->save();

        return redirect()->route('cargos.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cargo = Cargo::findOrFail($id);
        return view('cargos.show',['cargo'=>$cargo]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $cargo = Cargo::findOrFail($id);
        return view('cargos.edit',['cargo'=>$cargo]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CargoRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CargoRequest $request, $id)
    {
        $cargo = Cargo::findOrFail($id);
		$cargo->nombre = $request->input('nombre');
        $cargo->save();

        return redirect()->route('cargos.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cargo = Cargo::findOrFail($id);
        $cargo->delete();

        return redirect()->route('cargos.index');
    }
}
