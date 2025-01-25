<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\MyModels;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class UbicacionController extends Controller
{
    public function index2(Request $r): void
    {
        $ubicacion = Myhelp::AuthU();
        if (Schema::hasTable('ubicacion')) {
            DB::table('ubicacion')->insert([
                'ubicacion' => $r->ciudad,
                'valido' => 1,
                'ubicacionid' => $ubicacion->id,
                'name' => $ubicacion->name,
                'email' => $ubicacion->email,
                'created_at' => Carbon::now()
            ]);
        }
    }

    public function __construct()
    {
    }

    //empieza el index
    public function MapearClasePP(&$ubicacions, $numberPermissions)
    {
        $ubicacions = $ubicacions->get()->map(function ($ubicacion) {
            return $ubicacion;
        })->filter();
    }

    public function Busqueda(Request $request, $isTrashed = false)
    {
        $ubicacions = DB::table('ubicacion');
        if ($request->has('search')) {
            $ubicacions->where('ubicacion', 'LIKE', '%' . $request->search . '%');
            $ubicacions->orWhere('name', 'LIKE', '%' . $request->search . '%');
        }
        if ($request->has('search2')) {
            $ubicacions->where('ubicacion', 'NOT LIKE', '%' . $request->search . '%');
        }
        
        $ubicacions->orderBy('created_at', 'desc');
        return $ubicacions;
    }

    public function index(Request $request)
    {
        $permissions = Myhelp::EscribirEnLog($this, ' ubicacions');
        $numberPermissions = MyModels::getPermissionToNumber($permissions);
        $ubicacions = $this->Busqueda($request);

        $perPage = $request->has('perPage') ? $request->perPage : 5;

        $this->MapearClasePP($ubicacions, $numberPermissions);

        $page = request('page', 1); // Current page number
        $total = $ubicacions->count();
        $paginated = new LengthAwarePaginator(
            $ubicacions->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        return Inertia::render('Ubicacion/Index', [
            'title' => __('app.label.ubicacion'),
            'filters' => $request->all(['search', 'field', 'order']),
            'perPage' => (int)$perPage,
            'ubicacions' => $paginated,
            'breadcrumbs' => [['label' => __('app.label.ubicacion'), 'href' => route('ubicacion.index')]],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){} public function show($id){}public function edit($id){}

    public function update(Request $request, $id)
    {
        Myhelp::EscribirEnLog($this, 'ubicacions');
        DB::beginTransaction();
        try {
            $ubicacion = DB::table('ubicacion')->where('id', $id)->first();

            if (!$ubicacion) {
                abort(404, 'Ubicación no encontrada');
            }
            $ubicacion->update([
                'ubicacion' => $request->ubicacion,
                'valido' => $request->valido,
                'ubicacionid' => $request->ubicacionid,
                'name' => $request->name,
                'email' => $request->email,
                'created_at' => $request->created_at,
            ]);
            DB::commit();

            return back()->with('success', __('app.label.updated_successfully', ['name' => $ubicacion->name]));
        } catch (\Throwable $th) {
            DB::rollback();

            return back()->with('error', __('app.label.updated_error', ['name' => $ubicacion->name]) . $th->getMessage());
        }
    }

}
