<?php

namespace App\Http\Controllers;

use App\Models\peusuario;
use App\helpers\Myhelp;
use App\helpers\MyModels;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PeusuarioController extends Controller {
    public array $thisAtributos;
    public string $FromController = 'peusuario';


    //<editor-fold desc="Construc | filtro and dependencia">
    public function __construct() {
//        $this->middleware('permission:create peusuario', ['only' => ['create', 'store']]);
//        $this->middleware('permission:read peusuario', ['only' => ['index', 'show']]);
//        $this->middleware('permission:update peusuario', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:delete peusuario', ['only' => ['destroy', 'destroyBulk']]);
        $this->thisAtributos = (new peusuario())->getFillable(); //not using
    }

    public function index(Request $request) {
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' peusuarios '));
        $peusuarios = $this->Filtros($request)->get();
//        $losSelect = $this->Dependencias();


        $perPage = $request->has('perPage') ? $request->perPage : 10;
        return Inertia::render($this->FromController . '/Index', [
            'fromController' => $this->PerPageAndPaginate($request, $peusuarios),
            'total' => $peusuarios->count(),

            'breadcrumbs' => [
                [
                    'label' => __('app.label.' . $this->FromController),
                    'href' => route($this->FromController . '.index')
                ]
            ],
            'title' => __('app.label.' . $this->FromController),
            'filters' => $request->all([
                                           'search',
                                           'field',
                                           'order'
                                       ]),
            'perPage' => (int)$perPage,
            'numberPermissions' => $numberPermissions,
            'losSelect' => $losSelect ?? [],
        ]);
    }

    public function Filtros($request): Builder {
        $peusuarios = peusuario::query();
        if ($request->has('search')) {
            $peusuarios = $peusuarios->where(function ($query) use ($request) {
                $query->where('nombre', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('codigo', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
                ;
            });
        }

        if ($request->has(['field', 'order'])) {
            $peusuarios = $peusuarios->orderBy($request->field, $request->order);
        } else
            $peusuarios = $peusuarios->orderBy('updated_at', 'DESC');
        return $peusuarios;
    }

//    public function Dependencias()
//    {
//        $no_nadasSelect = No_nada::all('id','nombre as name')->toArray();
//        array_unshift($no_nadasSelect,["name"=>"Seleccione un no_nada",'id'=>0]);

//        $ejemploSelec = CentroCosto::all('id', 'nombre as name')->toArray();
//        array_unshift($ejemploSelec, ["name" => "Seleccione un ejemploSelec", 'id' => 0]);
//        return [$no_nadasSelect];
//        return [$no_nadasSelect,$ejemploSelec];
//    }

    //</editor-fold>

    public function PerPageAndPaginate($request, $peusuarios) {
        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $page = request('page', 1); // Current page number
        $paginated = new LengthAwarePaginator(
            $peusuarios->forPage($page, $perPage),
            $peusuarios->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );
        return $paginated;
    }

    public function store(Request $request): RedirectResponse {
        $permissions = Myhelp::EscribirEnLog($this, ' Begin STORE:peusuarios');
        DB::beginTransaction();
        $request->merge(['clasificacion' => $request->clasificacion['id']]);
        $peusuario = peusuario::create($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'STORE:peusuarios EXITOSO', 'peusuario id:' . $peusuario->id . ' | ' . $peusuario->nombre, false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $peusuario->nombre]));
    }

    //! STORE - UPDATE - DELETE
    //! STORE functions

    public function create() {
    }

    //fin store functions

    public function show($id) {
    }

    public function edit($id) {
    }

    public function update(Request $request, $id): RedirectResponse {
        $permissions = Myhelp::EscribirEnLog($this, ' Begin UPDATE:peusuarios');
        DB::beginTransaction();
        $peusuario = peusuario::findOrFail($id);
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $peusuario->update($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'UPDATE:peusuarios EXITOSO', 'peusuario id:' . $peusuario->id . ' | ' . $peusuario->nombre, false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $peusuario->nombre]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy($peusuarioid) {
        $permissions = Myhelp::EscribirEnLog($this, 'DELETE:peusuarios');
        $peusuario = peusuario::find($peusuarioid);
        $elnombre = $peusuario->nombre;
        $peusuario->delete();
        Myhelp::EscribirEnLog($this, 'DELETE:peusuarios', 'peusuario id:' . $peusuario->id . ' | ' . $peusuario->nombre . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
    }

    public function destroyBulk(Request $request) {
        $peusuario = peusuario::whereIn('id', $request->id);
        $peusuario->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.user')]));
    }
    //FIN : STORE - UPDATE - DELETE

}
