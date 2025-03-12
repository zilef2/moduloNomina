<?php

namespace App\Http\Controllers;

use App\Models\legalizacionviatico;
use App\helpers\Myhelp;
use App\helpers\MyModels;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class LegalizacionviaticoController extends Controller {
    public array $thisAtributos;
    public string $FromController = 'legalizacionviatico';


    //<editor-fold desc="Construc | filtro and dependencia">
    public function __construct() {
//        $this->middleware('permission:create legalizacionviatico', ['only' => ['create', 'store']]);
//        $this->middleware('permission:read legalizacionviatico', ['only' => ['index', 'show']]);
//        $this->middleware('permission:update legalizacionviatico', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:delete legalizacionviatico', ['only' => ['destroy', 'destroyBulk']]);
        $this->thisAtributos = (new legalizacionviatico())->getFillable(); //not using
    }

    public function index(Request $request) {
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' legalizacionviaticos '));
        $legalizacionviaticos = $this->Filtros($request)->get();
//        $losSelect = $this->Dependencias();


        $perPage = $request->has('perPage') ? $request->perPage : 10;
        return Inertia::render($this->FromController . '/Index', [
            'fromController' => $this->PerPageAndPaginate($request, $legalizacionviaticos),
            'total' => $legalizacionviaticos->count(),

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
        $legalizacionviaticos = legalizacionviatico::query();
        if ($request->has('search')) {
            $legalizacionviaticos = $legalizacionviaticos->where(function ($query) use ($request) {
                $query->where('nombre', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('codigo', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
                ;
            });
        }

        if ($request->has(['field', 'order'])) {
            $legalizacionviaticos = $legalizacionviaticos->orderBy($request->field, $request->order);
        } else
            $legalizacionviaticos = $legalizacionviaticos->orderBy('updated_at', 'DESC');
        return $legalizacionviaticos;
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

    public function PerPageAndPaginate($request, $legalizacionviaticos) {
        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $page = request('page', 1); // Current page number
        $paginated = new LengthAwarePaginator(
            $legalizacionviaticos->forPage($page, $perPage),
            $legalizacionviaticos->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );
        return $paginated;
    }

    public function store(Request $request): RedirectResponse {
        $permissions = Myhelp::EscribirEnLog($this, ' Begin STORE:legalizacionviaticos');
        DB::beginTransaction();
//        $no_nada = $request->no_nada['id'];
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $legalizacionviatico = legalizacionviatico::create($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'STORE:legalizacionviaticos EXITOSO', 'legalizacionviatico id:' . $legalizacionviatico->id . ' | ' . $legalizacionviatico->nombre, false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $legalizacionviatico->nombre]));
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
        $permissions = Myhelp::EscribirEnLog($this, ' Begin UPDATE:legalizacionviaticos');
        DB::beginTransaction();
        $legalizacionviatico = legalizacionviatico::findOrFail($id);
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $legalizacionviatico->update($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'UPDATE:legalizacionviaticos EXITOSO', 'legalizacionviatico id:' . $legalizacionviatico->id . ' | ' . $legalizacionviatico->nombre, false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $legalizacionviatico->nombre]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy($legalizacionviaticoid) {
        $permissions = Myhelp::EscribirEnLog($this, 'DELETE:legalizacionviaticos');
        $legalizacionviatico = legalizacionviatico::find($legalizacionviaticoid);
        $elnombre = $legalizacionviatico->nombre;
        $legalizacionviatico->delete();
        Myhelp::EscribirEnLog($this, 'DELETE:legalizacionviaticos', 'legalizacionviatico id:' . $legalizacionviatico->id . ' | ' . $legalizacionviatico->nombre . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
    }

    public function destroyBulk(Request $request) {
        $legalizacionviatico = legalizacionviatico::whereIn('id', $request->id);
        $legalizacionviatico->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.user')]));
    }
    //FIN : STORE - UPDATE - DELETE

}
