<?php

namespace App\Http\Controllers;

use App\Models\CentroCosto;
use App\Models\User;
use App\Models\viatico;
use App\helpers\Myhelp;
use App\helpers\MyModels;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ViaticoController extends Controller
{
    public array $thisAtributos;
    public string $FromController = 'viatico';


    //<editor-fold desc="Construc | mapea | filtro and dependencia">
    public function __construct()
    {
//        $this->middleware('permission:create viatico', ['only' => ['create', 'store']]);
//        $this->middleware('permission:read viatico', ['only' => ['index', 'show']]);
//        $this->middleware('permission:update viatico', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:delete viatico', ['only' => ['destroy', 'destroyBulk']]);
        $this->thisAtributos = (new viatico())->getFillable(); //not using
    }


//    public function Mapear($viaticos)
//    {
//        $viaticos = $viaticos->get()->map(function ($viatico) {
////            $viaticodep = $viatico->user();
////            $viatico['userino']['nombre'] = $viaticodep ? $viatico->user->name : '';
//
//            return $viatico;
//        });
//        return $viaticos;
//    }

    public function PerPageAndPaginate($request,$cotizacions)
    {
        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $page = request('page', 1); // Current page number
        $paginated = new LengthAwarePaginator(
            $cotizacions->forPage($page, $perPage),
            $cotizacions->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );
        return $paginated;
    }
    
    public function Filtros($request): Builder
    {
        $viaticos = Viatico::query();
        if ($request->has('search')) {
            $viaticos = $viaticos->where(function ($query) use ($request) {
                $query->where('descripcion', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('codigo', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
                ;
            });
        }

        if ($request->has(['field', 'order'])) {
            $viaticos = $viaticos->orderBy($request->field, $request->order);
        } else
            $viaticos = $viaticos->orderBy('updated_at', 'DESC');
        
        return $viaticos;
    }
    
    

    public function Dependencias()
    {
        $Empleados = User::select('id', 'name')->whereHas('roles', function ($query) {
            return $query->WhereIn('name', ['supervisor', 'administrativo', 'ingeniero', 'empleado']);
        })->get()->toArray();
        $centroSelect = CentroCosto::all('id', 'nombre as name')->toArray();
        array_unshift($Empleados, ["name" => "Seleccione una persona", 'id' => 0]);
        array_unshift($centroSelect, ["name" => "Seleccione un centro de costo", 'id' => 0]);
        return [$Empleados, $centroSelect];
    }

    //</editor-fold>

    public function index(Request $request): Response
    {
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' viaticos '));
//        $viaticos = $this->Mapear($this->Filtros($request));
        $viaticos = $this->Filtros($request)->get();
        $losSelect = $this->Dependencias();

        $perPage = $request->has('perPage') ? $request->perPage : 10;
        return Inertia::render($this->FromController . '/Index', [
            'fromController' => $this->PerPageAndPaginate($request,$viaticos),
            'total' => $viaticos->count(),

            'breadcrumbs' => [['label' => __('app.label.' . $this->FromController), 'href' => route($this->FromController . '.index')]],
            'title' => __('app.label.' . $this->FromController),
            'filters' => $request->all(['search', 'field', 'order']),
            'perPage' => (int)$perPage,
            'numberPermissions' => $numberPermissions,
            'losSelect' => $losSelect ?? [],
        ]);
    }

    public function create()
    {
    }

    //! STORE - UPDATE - DELETE
    //! STORE functions

    public function store(Request $request): RedirectResponse
    {
        $permissions = Myhelp::EscribirEnLog($this, ' Begin STORE:viaticos');
        DB::beginTransaction();
//        $no_nada = $request->no_nada['id'];
        $request->merge(['user_id' => Myhelp::AuthUid()]);
        $request->merge(['centro_costo_id' => $request->centro_costo_id['id']]);
        $request->merge(['fecha_legalizacion' => Carbon::now()]);
        $viatico = viatico::create($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'STORE:viaticos EXITOSO', 'viatico id:' . $viatico->id . ' | ' . $viatico->nombre, false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $viatico->nombre]));
    }

    //fin store functions

    public function show($id) {}public function edit($id) {}

    public function update(Request $request, $id): RedirectResponse {
        $permissions = Myhelp::EscribirEnLog($this, ' Begin UPDATE:viaticos');
        DB::beginTransaction();
        $viatico = viatico::findOrFail($id);
        
        if(gettype($request->user_id) === 'integer'){
            $foreign1 = $request->user_id;
        }
        else{
            $foreign1 = $request->user_id['id'];
        }
        
        if(gettype($request->centro_costo_id) === 'integer'){
            $foreign2 = $request->centro_costo_id;
        }
        else{
            $foreign2 = $request->centro_costo_id['id'];
        }
        $request->merge(['user_id' => $foreign1]);
        $request->merge(['centro_costo_id' => $foreign2]);
        $viatico->update($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'UPDATE:viaticos EXITOSO', 'viatico id:' . $viatico->id . ' | ' . $viatico->nombre, false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $viatico->nombre]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */

    public function destroy($viaticoid)
    {
        $permissions = Myhelp::EscribirEnLog($this, 'DELETE:viaticos');
        $viatico = viatico::find($viaticoid);
        $elnombre = $viatico->nombre;
        $viatico->delete();
        Myhelp::EscribirEnLog($this, 'DELETE:viaticos', 'viatico id:' . $viatico->id . ' | ' . $viatico->nombre . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
    }

    public function destroyBulk(Request $request)
    {
        $viatico = viatico::whereIn('id', $request->id);
        $viatico->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.user')]));
    }
    //FIN : STORE - UPDATE - DELETE

}
