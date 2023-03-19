<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\CentroCosto;
use App\Http\Requests\CentroCostoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CentroCostosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $centroCostos = centroCosto::query();
        if ($request->has('search')) {
            $centroCostos->orWhere('nombre', 'LIKE', "%" . $request->search . "%");
        }
        if ($request->has(['field', 'order'])) {
            $centroCostos->orderBy($request->field, $request->order);
        }
        $perPage = $request->has('perPage') ? $request->perPage : 10;

        $permissions = Auth::user()->getRoleNames()->first();
        if($permissions === "operator") { //admin | validador
            $nombresTabla =[//[0]: como se ven //[1] como es la BD
                ["#","nombre"],
                [null,"nombre"]
            ];
        }else{
            $nombresTabla =[//[0]: como se ven //[1] como es la BD
                ["#","nombre","Acciones"],
                [null,"nombre",null]
            ];
        }
        return Inertia::render('CentroCostos/Index', [ //carpeta
            'title'          =>  __('app.label.CentroCostos'),
            'filters'        =>  $request->all(['search', 'field', 'order']),
            'perPage'        =>  (int) $perPage,
            'fromController' =>  $centroCostos->paginate($perPage),
            'breadcrumbs'    =>  [['label' => __('app.label.CentroCostos'), 'href' => route('CentroCostos.index')]],
            'nombresTabla'   =>  $nombresTabla,
        ]);
    }

    public function create() { }

    /**
         * Store a newly created resource in storage.
         *
         * @param  centroCostosRequest  $request
         * @return \Illuminate\Http\Response
     */
    public function store(CentroCostoRequest $request)
    {
        DB::beginTransaction();

        try {
            $centroCostos = new centroCosto;
            $centroCostos->nombre = $request->nombre;
            $centroCostos->save();
            DB::commit();
            return back()->with('success', __('app.label.created_successfully', ['name' => $centroCostos->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.created_error', ['name' => __('app.label.centroCostos')]) . $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) { }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $centroCostos = centroCosto::findOrFail($id);
        return Inertia::render('centroCostos.edit',['centroCostos'=>$centroCostos]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  centroCostosRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CentroCostoRequest $request, $id)
    {
         DB::beginTransaction();

        try {
            $centroCostos = centroCosto::findOrFail($id);
            $centroCostos->nombre = $request->input('nombre');
            $centroCostos->save();
            DB::commit();
            return back()->with('success', __('app.label.created_successfully', ['name' => $centroCostos->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.created_error', ['name' => __('app.label.centroCostos')]) . $th->getMessage());
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
        DB::beginTransaction();

        try {
            $centroCostos = CentroCosto::findOrFail($id);
            $centroCostos->delete();

            DB::commit();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => $centroCostos->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.centroCostos')]) . $th->getMessage());
        }
    }
}
