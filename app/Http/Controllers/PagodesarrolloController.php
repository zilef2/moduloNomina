<?php

namespace App\Http\Controllers;

use App\Models\pagodesarrollo;
use App\helpers\Myhelp;
use App\helpers\MyModels;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PagodesarrolloController extends Controller
{
    public array $thisAtributos;
    public string $FromController = 'pagodesarrollo';


    //<editor-fold desc="Construc | mapea | filtro and dependencia">
    public function __construct() {
//        $this->middleware('permission:create pagodesarrollo', ['only' => ['create', 'store']]);
//        $this->middleware('permission:read pagodesarrollo', ['only' => ['index', 'show']]);
//        $this->middleware('permission:update pagodesarrollo', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:delete pagodesarrollo', ['only' => ['destroy', 'destroyBulk']]);
        $this->thisAtributos = (new pagodesarrollo())->getFillable(); //not using
    }


    public function Mapear()
    {
        $pagodesarrollos = pagodesarrollo::query();
        $pagodesarrollos = $pagodesarrollos->get()->map(function ($pagodesarrollo) {
//            $pagodesarrollodep = $pagodesarrollo->user;
//            if ($pagodesarrollodep) $pagodesarrollo->user_id['nombre'] = $pagodesarrollo->user->nombre;
//            else $pagodesarrollo->user_id['nombre'] = '';
            return $pagodesarrollo;
        });
        return $pagodesarrollos;
    }
    
     public function PerPageAndPaginate($request,$pagodesarrollos)
    {
        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $page = request('page', 1); // Current page number
        $paginated = new LengthAwarePaginator(
            $pagodesarrollos->forPage($page, $perPage),
            $pagodesarrollos->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );
        return $paginated;
    }
    
    public function Filtros($request): Builder {
        $pagodesarrollos = pagodesarrollo::query();
        if ($request->has('search')) {
            $pagodesarrollos = $pagodesarrollos->where(function ($query) use ($request) {
                $query->where('nombre', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('codigo', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
                ;
            });
        }

        if ($request->has(['field', 'order'])) {
            $pagodesarrollos = $pagodesarrollos->orderBy($request->field, $request->order);
        }else
            $pagodesarrollos = $pagodesarrollos->orderBy('updated_at', 'DESC');
        return $pagodesarrollos;
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
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' pagodesarrollos '));
        $pagodesarrollos = $this->Filtros($request);
//        $losSelect = $this->Dependencias();


        $perPage = $request->has('perPage') ? $request->perPage : 10;
        return Inertia::render($this->FromController.'/Index', [
            'fromController' => $this->PerPageAndPaginate($request,$pagodesarrollos),
            'total'                 => $pagodesarrollos->count(),

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
        $permissions = Myhelp::EscribirEnLog($this, ' Begin STORE:pagodesarrollos');
        DB::beginTransaction();
//        $no_nada = $request->no_nada['id'];
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $pagodesarrollo = pagodesarrollo::create($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'STORE:pagodesarrollos EXITOSO', 'pagodesarrollo id:' . $pagodesarrollo->id . ' | ' . $pagodesarrollo->nombre, false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $pagodesarrollo->nombre]));
    }
    //fin store functions

    public function show($id){}public function edit($id){}

    public function update(Request $request, $id): RedirectResponse{
        $permissions = Myhelp::EscribirEnLog($this, ' Begin UPDATE:pagodesarrollos');
        DB::beginTransaction();
        $pagodesarrollo = pagodesarrollo::findOrFail($id);
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $pagodesarrollo->update($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'UPDATE:pagodesarrollos EXITOSO', 'pagodesarrollo id:' . $pagodesarrollo->id . ' | ' . $pagodesarrollo->nombre , false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $pagodesarrollo->nombre]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy($pagodesarrolloid){
        $permissions = Myhelp::EscribirEnLog($this, 'DELETE:pagodesarrollos');
        $pagodesarrollo = pagodesarrollo::find($pagodesarrolloid);
        $elnombre = $pagodesarrollo->nombre;
        $pagodesarrollo->delete();
        Myhelp::EscribirEnLog($this, 'DELETE:pagodesarrollos', 'pagodesarrollo id:' . $pagodesarrollo->id . ' | ' . $pagodesarrollo->nombre . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
    }

    public function destroyBulk(Request $request){
        $pagodesarrollo = pagodesarrollo::whereIn('id', $request->id);
        $pagodesarrollo->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.user')]));
    }
    //FIN : STORE - UPDATE - DELETE

}
