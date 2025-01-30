<?php

namespace App\Http\Controllers;

use App\Models\consignarViatico;
use App\helpers\Myhelp;
use App\helpers\MyModels;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ConsignarViaticoController extends Controller
{
    public array $thisAtributos;
    public string $FromController = 'consignarViatico';


    //<editor-fold desc="Construc | mapea | filtro and dependencia">
    public function __construct() {
//        $this->middleware('permission:create consignarViatico', ['only' => ['create', 'store']]);
//        $this->middleware('permission:read consignarViatico', ['only' => ['index', 'show']]);
//        $this->middleware('permission:update consignarViatico', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:delete consignarViatico', ['only' => ['destroy', 'destroyBulk']]);
        $this->thisAtributos = (new consignarViatico())->getFillable(); //not using
    }


    public function Mapear($consignarViaticos)
    {
        $consignarViaticos = $consignarViaticos->get()->map(function ($consignarViatico) {
//            $consignarViaticodep = $consignarViatico->user;
//            if ($consignarViaticodep) $consignarViatico->user_id['nombre'] = $consignarViatico->user->nombre;
//            else $consignarViatico->user_id['nombre'] = '';
            return $consignarViatico;
        });
        return $consignarViaticos;
    }
    
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
    
    public function Filtros(&$consignarViaticos,$request){
        if ($request->has('search')) {
            $consignarViaticos = $consignarViaticos->where(function ($query) use ($request) {
                $query->where('nombre', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('codigo', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
                ;
            });
        }

        if ($request->has(['field', 'order'])) {
            $consignarViaticos = $consignarViaticos->orderBy($request->field, $request->order);
        }else
            $consignarViaticos = $consignarViaticos->orderBy('updated_at', 'DESC');
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

    public function index(Request $request) {
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' consignarViaticos '));
        $consignarViaticos = $this->Mapear();
        $this->Filtros($consignarViaticos,$request);
//        $losSelect = $this->Dependencias();


        $perPage = $request->has('perPage') ? $request->perPage : 10;
        return Inertia::render($this->FromController.'/Index', [
            'fromController' => $this->PerPageAndPaginate($request,$consignarViaticos),
            'total'                 => $consignarViaticos->count(),

            'breadcrumbs'           => [['label' => __('app.label.'.$this->FromController), 'href' => route($this->FromController.'.index')]],
            'title'                 => __('app.label.'.$this->FromController),
            'filters'               => $request->all(['search', 'field', 'order']),
            'perPage'               => (int) $perPage,
            'numberPermissions'     => $numberPermissions,
            'losSelect'             => $losSelect ?? [],
        ]);
    }

    public function create(){}

    //! STORE - UPDATE - DELETE
    //! STORE functions

    public function store(Request $request): RedirectResponse{
        $permissions = Myhelp::EscribirEnLog($this, ' Begin STORE:consignarViaticos');
        DB::beginTransaction();
//        $no_nada = $request->no_nada['id'];
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $consignarViatico = consignarViatico::create($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'STORE:consignarViaticos EXITOSO', 'consignarViatico id:' . $consignarViatico->id . ' | ' . $consignarViatico->nombre, false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $consignarViatico->nombre]));
    }
    //fin store functions

    public function show($id){}public function edit($id){}

    public function update(Request $request, $id): RedirectResponse{
        $permissions = Myhelp::EscribirEnLog($this, ' Begin UPDATE:consignarViaticos');
        DB::beginTransaction();
        $consignarViatico = consignarViatico::findOrFail($id);
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $consignarViatico->update($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'UPDATE:consignarViaticos EXITOSO', 'consignarViatico id:' . $consignarViatico->id . ' | ' . $consignarViatico->nombre , false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $consignarViatico->nombre]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy($consignarViaticoid){
        $permissions = Myhelp::EscribirEnLog($this, 'DELETE:consignarViaticos');
        $consignarViatico = consignarViatico::find($consignarViaticoid);
        $elnombre = $consignarViatico->nombre;
        $consignarViatico->delete();
        Myhelp::EscribirEnLog($this, 'DELETE:consignarViaticos', 'consignarViatico id:' . $consignarViatico->id . ' | ' . $consignarViatico->nombre . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
    }

    public function destroyBulk(Request $request){
        $consignarViatico = consignarViatico::whereIn('id', $request->id);
        $consignarViatico->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.user')]));
    }
    //FIN : STORE - UPDATE - DELETE

}
