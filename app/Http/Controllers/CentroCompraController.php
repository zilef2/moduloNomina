<?php

namespace App\Http\Controllers;

use App\Models\CentroCompra;
use App\Http\Requests\StoreCentroCompraRequest;
use App\Http\Requests\UpdateCentroCompraRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CentroCompraController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $centrocompras = CentroCompra::query();//seeder, factory, controller
        if ($request->has('search')) {
            $centrocompras->orWhere('nombre', 'LIKE', "%" . $request->search . "%");
        }
        if ($request->has(['field', 'order'])) {
            $centrocompras->orderBy($request->field, $request->order);
        }
        $perPage = $request->has('perPage') ? $request->perPage : 10;

        $nombresTabla =[
            ["nombre"],
            ["nombre"]
        ];

        return Inertia::render('centrocompras/Index', [
            'title'       => __('app.label.centrocompra'),
            'filters'     => $request->all(['search', 'field', 'order']),
            'perPage'     => (int) $perPage,
            'centrocompras'     => $centrocompras->paginate($perPage),
            'breadcrumbs' => [['label' => __('app.label.centrocompras'), 'href' => route('centrocompra.index')]],
            'nombresTabla'=> $nombresTabla,
        ]);
    }

    public function create() { }

    public function store(StoreCentroCompraRequest $request)
    {
        DB::beginTransaction();
        try {
            $centrocompra = CentroCompra::create([
                'nombre' => $request->nombre
            ]);
            DB::commit();
            return back()->with('success', __('app.label.created_successfully', ['nombre' => $centrocompra->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.centrocompra')]) . $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CentroCompra  $centroCompra
     * @return \Illuminate\Http\Response
     */
    public function show(CentroCompra $centroCompra)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CentroCompra  $centroCompra
     * @return \Illuminate\Http\Response
     */
    public function edit(CentroCompra $centroCompra)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCentroCompraRequest  $request
     * @param  \App\Models\CentroCompra  $centroCompra
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCentroCompraRequest $request, CentroCompra $centroCompra)
    {
        DB::beginTransaction();
        try {
            $centroCompra->update([
                'nombre' => $request->nombre
            ]);
            DB::commit();
            return back()->with('success', __('app.label.updated_successfully', ['nombre' => $centroCompra->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.updated_error', ['nombre' => $centroCompra->nombre]) . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CentroCompra  $centroCompra
     * @return \Illuminate\Http\Response
     */
    public function destroy(CentroCompra $centroCompra)
    {
        //
    }
}
