<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\Models\CentroCosto;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;

class DeudaSingularController extends Controller
{
     public string $FromController = 'deuda';


    //<editor-fold desc="Construc | mapea | filtro and dependencia">
    public function __construct()
    {
    }


    public function Mapear($deudas)
    {
        $deudas = $deudas->get()->map(function ($deuda) {
            $deuda->centro_costo_id2 = $deuda->centro;
            if ($deuda->centro_costo_id2) $deuda->centro_costo_id2 = $deuda->centro_costo_id2->nombre;

            return $deuda;
        });
        return $deudas;
    }

    public function Filtros(&$deudas, $request)
    {
        $deudas = deuda::query();
        if ($request->has('search')) {
            $deudas = $deudas->where(function ($query) use ($request) {
                $query->where('numero_cot', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('codigo', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
                ;
            });
        }

        if ($request->has(['field', 'order'])) {
            $deudas = $deudas->orderBy($request->field, $request->order);
        } else
            $deudas = $deudas->orderBy('updated_at', 'DESC');
    }

    public function Dependencias()
    {
//        $dependexsSelect = CentroCosto::all('id as value','nombre as label')->toArray();
        $dependexsSelect = CentroCosto::all(['id as value', 'nombre', 'descripcion'])
            ->map(function ($item) {
                return [
                    'value' => $item->value,
                    'label' => ($item->nombre ?? '') . ' - ' . mb_substr($item->descripcion ?? '', 0, 17),
                ];
            })
            ->toArray();


        array_unshift($dependexsSelect, ["label" => "Seleccione un centro de costo", 'value' => 0]);
        return $dependexsSelect;
    }

    //</editor-fold>

    public function index(Request $request)
    {
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' deudas '));
        $this->Filtros($deudas, $request);
        $deudas = $this->Mapear($deudas);
//        $losSelect = $this->Dependencias();


        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $page = request('page', 1); // Current page number
        $paginated = new LengthAwarePaginator(
            $deudas->forPage($page, $perPage),
            $deudas->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );
        return Inertia::render($this->FromController . '/Index', [
            'fromController' => $paginated,
            'total' => $deudas->count(),

            'breadcrumbs' => [['label' => __('app.label.' . $this->FromController), 'href' => route($this->FromController . '.index')]],
            'title' => __('app.label.' . $this->FromController),
            'filters' => $request->all(['search', 'field', 'order']),
            'perPage' => (int)$perPage,
            'numberPermissions' => $numberPermissions,
//            'losSelect' => $losSelect,
        ]);
    }
}
