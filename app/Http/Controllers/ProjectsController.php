<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Project;
use App\Http\Requests\ProjectRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ProjectsController extends Controller
{
    /**
    //  * Display a listing of the resource.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function index(Request $request)
    // {
    //     $Projects = Project::query();
    //     if ($request->has('search')) {
    //         $Projects->orWhere('nombre', 'LIKE', "%" . $request->search . "%");
    //     }
    //     if ($request->has(['field', 'order'])) {
    //         $Projects->orderBy($request->field, $request->order);
    //     }
    //     $perPage = $request->has('perPage') ? $request->perPage : 10;

    //     $nombresTabla =[//0: como se ven //1 como es la BD
    //         ["Acciones","#","nombre/cliente", "Modulos", "valor tentativo", "valor acordado", "valor primer pago", "Primera reunion", "Primer pago", "Entrega", "Observaciones"],
    //         ["nombre", "cliente", "num_modulos", "valor_tentativo", "valor_acordado", "valor_primer_pago", "fecha_primera_reunion", "fecha_primer_pago", "fecha_entrega", "observaciones"]
    //     ];
    //     return Inertia::render('Projects/Index', [ //carpeta
    //         'title'                 => __('app.label.Project'),
    //         'filters'               => $request->all(['search', 'field', 'order']),
    //         'perPage'               => (int) $perPage,
    //         'fromController'   => $Projects->paginate($perPage),
    //         'breadcrumbs' => [['label' => __('app.label.Projects'), 'href' => route('projects.index')]],
    //         'nombresTabla'=> $nombresTabla,
    //     ]);
    // }

    // public function create() { }

    // /**
    //      * Store a newly created resource in storage.
    //      *
    //      * @param  ProjectRequest  $request
    //      * @return \Illuminate\Http\Response
    //  */
    // public function store(ProjectRequest $request)
    // {
    //     DB::beginTransaction();

    //     try {
    //         $project = new Project;
    //         $project->nombre = $request->nombre;
    //         $project->cliente = $request->cliente;
    //         $project->num_modulos = 1;
    //         $project->valor_tentativo = $request->valor_tentativo;
    //         $project->valor_acordado = $request->valor_acordado;
    //         $project->valor_primer_pago = $request->valor_primer_pago;
    //         $project->fecha_primera_reunion = $request->fecha_primera_reunion;
    //         $project->fecha_primer_pago = $request->fecha_primer_pago;
    //         $project->fecha_entrega = $request->fecha_entrega;
    //         $project->observaciones = $request->observaciones;
    //         $project->save();
    //         DB::commit();
    //         return back()->with('success', __('app.label.created_successfully', ['name' => $project->nombre]));
    //     } catch (\Throwable $th) {
    //         DB::rollback();
    //         return back()->with('error', __('app.label.created_error', ['name' => __('app.label.project')]) . $th->getMessage());
    //     }
    // }

    // /**
    //  * Display the specified resource.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function show($id)
    // {
    //     $project = Project::findOrFail($id);
    //     return Inertia::render('projects.show',['project'=>$project]);
    // }

    // /**
    //  * Show the form for editing the specified resource.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function edit($id)
    // {
    //     $project = Project::findOrFail($id);
    //     return Inertia::render('projects.edit',['project'=>$project]);
    // }

    // /**
    //  * Update the specified resource in storage.
    //  *
    //  * @param  ProjectRequest  $request
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function update(ProjectRequest $request, $id)
    // {
    //      DB::beginTransaction();

    //     try {
    //         $project = Project::findOrFail($id);
    //         $project->nombre = $request->input('nombre');
    //         $project->cliente = $request->input('cliente');
    //         $project->num_modulos = 3;
    //         $project->valor_tentativo = $request->input('valor_tentativo');
    //         $project->valor_acordado = $request->input('valor_acordado');
    //         $project->valor_primer_pago = $request->input('valor_primer_pago');
    //         $project->fecha_primera_reunion = $request->input('fecha_primera_reunion');
    //         $project->fecha_primer_pago = $request->input('fecha_primer_pago');
    //         $project->fecha_entrega = $request->input('fecha_entrega');
    //         $project->observaciones = $request->input('observaciones');
    //         $project->save();
    //         DB::commit();
    //         return back()->with('success', __('app.label.created_successfully', ['name' => $project->nombre]));
    //     } catch (\Throwable $th) {
    //         DB::rollback();
    //         return back()->with('error', __('app.label.created_error', ['name' => __('app.label.project')]) . $th->getMessage());
    //     }
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function destroy($id)
    // {
    //     $project = Project::findOrFail($id);
    //     $project->delete();

    //     return redirect()->route('projects.index');
    // }
}
