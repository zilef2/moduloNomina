<?php

namespace App\Http\Controllers;

use App\Models\zona;
use App\helpers\Myhelp;
use App\helpers\MyModels;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ZonaController extends Controller {
    public array $thisAtributos;
    public string $FromController = 'zona';


    //<editor-fold desc="Construc | mapea | filtro and dependencia">
    public function __construct() {
//        $this->middleware('permission:create zona', ['only' => ['create', 'store']]);
//        $this->middleware('permission:read zona', ['only' => ['index', 'show']]);
//        $this->middleware('permission:update zona', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:delete zona', ['only' => ['destroy', 'destroyBulk']]);
        $this->thisAtributos = (new zona())->getFillable(); //not using
    }


    public function Mapear() {
        $zonas = zona::query();
        $zonas = $zonas->get()->map(function ($zona) {
//            $zonadep = $zona->user;
//            if ($zonadep) $zona->user_id['nombre'] = $zona->user->nombre;
//            else $zona->user_id['nombre'] = '';
            return $zona;
        });
        return $zonas;
    }

    public function index(Request $request) {
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' zonas '));
        $zonas = $this->Filtros($request)->get();
//        $losSelect = $this->Dependencias();


        $perPage = $request->has('perPage') ? $request->perPage : 10;
        return Inertia::render($this->FromController . '/Index', [
            'fromController' => $this->PerPageAndPaginate($request, $zonas),
            'total' => $zonas->count(),

            'breadcrumbs' => [['label' => __('app.label.' . $this->FromController), 'href' => route($this->FromController . '.index')]],
            'title' => __('app.label.' . $this->FromController),
            'filters' => $request->all(['search', 'field', 'order']),
            'perPage' => (int)$perPage,
            'numberPermissions' => $numberPermissions,
            'losSelect' => $losSelect ?? [],
        ]);
    }

    public function Filtros($request): Builder {
        $zonas = zona::query();
        if ($request->has('search')) {
            $zonas = $zonas->where(function ($query) use ($request) {
                $query->where('nombre', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('codigo', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
                ;
            });
        }

        if ($request->has(['field', 'order'])) {
            $zonas = $zonas->orderBy($request->field, $request->order);
        }
        else
            $zonas = $zonas->orderBy('updated_at', 'DESC');
        return $zonas;
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

    public function PerPageAndPaginate($request, $zonas): LengthAwarePaginator {
        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $page = request('page', 1); // Current page number
        $paginated = new LengthAwarePaginator(
            $zonas->forPage($page, $perPage),
            $zonas->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );
        return $paginated;
    }

    public function store(Request $request): RedirectResponse {
        $permissions = Myhelp::EscribirEnLog($this, ' Begin STORE:zonas');
        DB::beginTransaction();
//        $no_nada = $request->no_nada['id'];
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $zona = zona::create($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'STORE:zonas EXITOSO', 'zona id:' . $zona->id . ' | ' . $zona->nombre, false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $zona->nombre]));
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
        $permissions = Myhelp::EscribirEnLog($this, ' Begin UPDATE:zonas');
        DB::beginTransaction();
        $zona = zona::findOrFail($id);
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $zona->update($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'UPDATE:zonas EXITOSO', 'zona id:' . $zona->id . ' | ' . $zona->nombre, false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $zona->nombre]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy($zonaid) {
        $permissions = Myhelp::EscribirEnLog($this, 'DELETE:zonas');
        $zona = zona::find($zonaid);
        $elnombre = $zona->nombre;
        $zona->delete();
        Myhelp::EscribirEnLog($this, 'DELETE:zonas', 'zona id:' . $zona->id . ' | ' . $zona->nombre . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
    }

    public function destroyBulk(Request $request) {
        $zona = zona::whereIn('id', $request->id);
        $zona->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.user')]));
    }
    //FIN : STORE - UPDATE - DELETE

}
