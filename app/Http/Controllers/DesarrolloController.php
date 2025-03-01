<?php

namespace App\Http\Controllers;

use App\Models\desarrollo;
use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\Models\pagodesarrollo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DesarrolloController extends Controller {
    public array $thisAtributos;
    public string $FromController = 'desarrollo';
    public array $estados = [
        'Cotizando',
        'Desarrollando',
        'Esperando pago parcial',
        'Pagada totalmente',
        'Vencida',
        'Finalizada'
    ];


    //<editor-fold desc="Construc | mapea | filtro and dependencia">
    public function __construct() {
//        $this->middleware('permission:create desarrollo', ['only' => ['create', 'store']]);
//        $this->middleware('permission:read desarrollo', ['only' => ['index', 'show']]);
//        $this->middleware('permission:update desarrollo', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:delete desarrollo', ['only' => ['destroy', 'destroyBulk']]);
        $this->thisAtributos = (new desarrollo())->getFillable(); //not using
    }

    public function index(Request $request) {
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' desarrollos '));
        $desarrollos = $this->Filtros($request);
//        $losSelect = $this->Dependencias();


        $perPage = $request->has('perPage') ? $request->perPage : 100;
        return Inertia::render($this->FromController . '/Index', [
            'fromController' => $this->PerPageAndPaginate($request, $desarrollos),
            'total' => $desarrollos->count(),

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

    public function Filtros($request) {
        $desarrollos = desarrollo::query();

        if ($request->has('search')) {
            $desarrollos = $desarrollos->where(function ($query) use ($request) {
                $query->where('nombre', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('codigo', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
                ;
            });
        }

        if ($request->has([
                              'field',
                              'order'
                          ])) {
            $desarrollos = $desarrollos->orderBy($request->field, $request->order);
        } else $desarrollos = $desarrollos->orderBy('updated_at', 'DESC');

        $desarrollos = $this->Mapear($desarrollos);

        return $desarrollos;
    }

    public function Mapear($desarrollos) {
        $desarrollos = $desarrollos->WhereNot('estado', 'Finalizada');
        return $desarrollos->get()->map(function ($desarrollo) {
//            $desarrollodep = $desarrollo->user;
//            if ($desarrollodep) $desarrollo->user_id['nombre'] = $desarrollo->user->nombre;
//            else $desarrollo->user_id['nombre'] = '';
            return $desarrollo;
        });
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

    public function PerPageAndPaginate($request, $desarrollos) {
        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $page = request('page', 1); // Current page number
        $paginated = new LengthAwarePaginator(
            $desarrollos->forPage($page, $perPage),
            $desarrollos->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );
        return $paginated;
    }

    public function store(Request $request): RedirectResponse {
        $permissions = Myhelp::EscribirEnLog($this, ' Begin STORE:desarrollos');
        DB::beginTransaction();
        $request->merge(['estado' => 'Cotizando']);
        $desarrollo = desarrollo::create($request->all());


        DB::commit();
        Myhelp::EscribirEnLog($this, 'STORE:desarrollos EXITOSO', 'desarrollo id:' . $desarrollo->id . ' | ' . $desarrollo->nombre, false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $desarrollo->nombre]));
    }

    //! STORE - UPDATE - DELETE
    //! STORE functions

    public function create() {
    }//fin store functions public function show($id) { } public function edit($id) { }

    public function updatePago(Request $request, $id): RedirectResponse {
        $permissions = Myhelp::EscribirEnLog($this, ' Begin UPDATE:desarrollos');
        DB::beginTransaction();
        $desarrollo = desarrollo::findOrFail($id);
        if (gettype($request->estado) !== 'string' && $request->estado['value'])
            $request->merge(['estado' => $request->estado['value']]);

        $desarrollo->update($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'UPDATE:desarrollos EXITOSO', 'desarrollo id:' . $desarrollo->id . ' | ' . $desarrollo->nombre, false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $desarrollo->nombre]));
    }


    //paso 2
    public function update(Request $request, $id): RedirectResponse {
        $permissions = Myhelp::EscribirEnLog($this, ' Begin UPDATE:desarrollos');
        DB::beginTransaction();
        $desarrollo = desarrollo::findOrFail($id);

        pagodesarrollo::create(
            [
                'valor' => $request->valor,
                'fecha' => $request->fecha,
                'cuota' => $request->cuota,
                'final' => 0,
                'desarrollo_id' => $desarrollo->id,
            ]
        );

        DB::commit();
        Myhelp::EscribirEnLog($this, 'UPDATE:desarrollos EXITOSO', 'desarrollo id:' . $desarrollo->id . ' | ' . $desarrollo->nombre, false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $desarrollo->nombre]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy($desarrolloid) {
        $permissions = Myhelp::EscribirEnLog($this, 'DELETE:desarrollos');
        $desarrollo = desarrollo::find($desarrolloid);
        $elnombre = $desarrollo->nombre;
        $desarrollo->delete();
        Myhelp::EscribirEnLog($this, 'DELETE:desarrollos', 'desarrollo id:' . $desarrollo->id . ' | ' . $desarrollo->nombre . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
    }

    public function destroyBulk(Request $request) {
        $desarrollo = desarrollo::whereIn('id', $request->id);
        $desarrollo->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.user')]));
    }
    //FIN : STORE - UPDATE - DELETE

}
