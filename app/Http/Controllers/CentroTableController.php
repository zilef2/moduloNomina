<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\Models\Reporte;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CentroTableController extends Controller
{

    private function noumbresTabla($numberPermissions)
    {
        if ($numberPermissions < 2) { //admin | administrativo
            $nombresTabla = [//[0]: como se ven //[1] como es la BD
                ["#", "nombre"],
                [null, "nombre"]
            ];
        } else {
            $nombresTabla = [//[0]: como se ven //[1] como es la BD
                ["Acciones", "#", "nombre", "Mano obra estimada", "usuarios", "Supervisor", 'activo', 'descripcion', 'clasificacion'],
                [null, null, "nombre", "mano_obra_estimada", null, null, 'activo', 'descripcion', 'clasificacion']
            ];
        }

        return $nombresTabla;
    }


    private function TheQuery($id){
        $esteMes = date("m");
        $diaquincena = date("d");

        $elSelect = [
            'user_id',
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(horas_trabajadas) as horas_trabajadas'),
            DB::raw('SUM(almuerzo) as almuerzo'),
            DB::raw('SUM(diurnas) as diurnas'),
            DB::raw('SUM(nocturnas) as nocturnas'),
            DB::raw('SUM(extra_diurnas) as extra_diurnas'),
            DB::raw('SUM(extra_nocturnas) as extra_nocturnas'),
            DB::raw('SUM(dominical_diurno) as dominical_diurno'),
            DB::raw('SUM(dominical_nocturno) as dominical_nocturno'),
            DB::raw('SUM(dominical_extra_diurno) as dominical_extra_diurno'),
            DB::raw('SUM(dominical_extra_nocturno) as dominical_extra_nocturno'),
        ];
        if($diaquincena <= 15){
            $centroCostos = Reporte::select($elSelect)
                ->whereMonth('fecha_ini', $esteMes)
                ->whereDay('fecha_ini', '<=', 15)
                ->where('centro_costo_id', $id)
                ->groupBy('user_id')
                ->get();
        }else{
            $centroCostos = Reporte::select($elSelect)
                ->whereMonth('fecha_ini', $esteMes)
                ->whereDay('fecha_ini', '<', 15) //cambiar esto por debug >
                ->where('centro_costo_id', $id)
                ->groupBy('user_id')
                ->get();
        }
        $centroCostos->map(function ($reporteu){
            $reporteu->usera = $reporteu->user->name;
            return $reporteu;
        })->filter();

//        dd($centroCostos);
        $page = request('page', 1); // Current page number
//        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $perPage = 10;
        $total = $centroCostos->count();
        $paginated = new LengthAwarePaginator(
            $centroCostos->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        return [$centroCostos, $perPage, $paginated];
    }


    public function table(Request $request, $id)
    {
        $permissions = Myhelp::EscribirEnLog($this, ' |reportes table| ');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);
        $Authuser = Myhelp::AuthU();
        $titulo = __('app.label.Reportes');

        $nombresTabla = $this->noumbresTabla($numberPermissions);

        [$centroCostos, $perPage, $paginated] = $this->TheQuery($id);

        return Inertia::render('CentroCostos/table', [ //carpeta
            'title' => __('app.label.CentroCostos'),
//            'filters'        =>  $request->all(['search', 'field', 'order']),
            'perPage' => (int)$perPage,
            'fromController' => $paginated,
//            'breadcrumbs'    =>  [['label' => __('app.label.CentroCostos'), 'href' => route('CentroCostos.table')]],
            'nombresTabla' => $nombresTabla,
        ]);
    }
}
