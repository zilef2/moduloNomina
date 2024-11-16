<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\Models\CentroCosto;
use App\Models\Parametro;
use App\Models\Reporte;
use App\Models\User;
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


    private function TheQuery($id, $request, $plata = false)
    {
        if ($request->fecha_ini && $request->quincena) {
            $esteMes = $request->fecha_ini;
            $diaquincena = $request->quincena;
            if (!is_string($diaquincena)) $diaquincena = $request->quincena['value'];
            $esteMes['month'] = intval($esteMes['month']) + 1;

        } else {
            $esteMes = [
                'month' => date("m"),
                'year' => date("Y")
            ];
            $diaquincena = date("d");
        }

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
        if ($diaquincena == 1) {
            $centroCostos = Reporte::select($elSelect)
                ->whereYear('fecha_ini', $esteMes['year'])
                ->whereMonth('fecha_ini', $esteMes['month'])
                ->whereDay('fecha_ini', '<=', 15)
                ->where('centro_costo_id', $id)
                ->groupBy('user_id')
                ->get();
        } else {
            if ($diaquincena == 2) {
                $centroCostos = Reporte::select($elSelect)
                    ->whereYear('fecha_ini', $esteMes['year'])
                    ->whereMonth('fecha_ini', $esteMes['month'])
                    ->whereDay('fecha_ini', '>', 15)
                    ->where('centro_costo_id', $id)
                    ->groupBy('user_id')
                    ->get();
            } else {
//        if ($diaquincena == 3) {
                $centroCostos = Reporte::select($elSelect)
                    ->whereYear('fecha_ini', $esteMes['year'])
                    ->whereMonth('fecha_ini', $esteMes['month'])
                    ->where('centro_costo_id', $id)
                    ->groupBy('user_id')
                    ->get();
            }
        }

        if ($plata) {
            $centroCostos = $this->MultiplicarPorSalario($centroCostos, $plata);
        } else {
            $centroCostos->map(function ($reporteu) {
                $reporteu->usera = $reporteu->user->name;
                return $reporteu;
            })->filter();
        }

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

        return [$perPage, $paginated];
    }


    public function table(Request $request, $id)
    {
        $permissions = Myhelp::EscribirEnLog($this, ' |reportes table| ');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);
        $Authuser = Myhelp::AuthU();
        $titulo = __('app.label.Reportes');

        $nombresTabla = $this->noumbresTabla($numberPermissions);

        [$perPage, $paginated] = $this->TheQuery($id, $request, $request->plata);

        return Inertia::render('CentroCostos/table', [ //carpeta
            'elIDD' => $id,
            'title' => CentroCosto::find($id)->nombre,
//            'title' => __('app.label.CentroCostos'),
            'filters'        =>  $request->all(['fecha_ini', 'quincena', 'plata']),
            'perPage' => (int)$perPage,
            'fromController' => $paginated,
//            'breadcrumbs'    =>  [['label' => __('app.label.CentroCostos'), 'href' => route('CentroCostos.table')]],
            'nombresTabla' => $nombresTabla,
        ]);
    }

    private function MultiplicarPorSalario($centroCostos, $plata)
    {
        $parametros = Parametro::find(1);
        $vectorSuma = [];
        $centroCostos->map(function ($reporteu) use ($plata, $parametros, $vectorSuma) {
            $user = User::find($reporteu->user_id);
            if ($user) {
                $sal = $user->salario / 235;
                $porcentaje_diurno = $parametros->porcentaje_diurno * $sal;
                $porcentaje_nocturno = $parametros->porcentaje_nocturno * $sal;
                $porcentaje_extra_diurno = $parametros->porcentaje_extra_diurno * $sal;
                $porcentaje_extra_nocturno = $parametros->porcentaje_extra_nocturno * $sal;
                $porcentaje_dominical_diurno = $parametros->porcentaje_dominical_diurno * $sal;
                $porcentaje_dominical_nocturno = $parametros->porcentaje_dominical_nocturno * $sal;
                $porcentaje_dominical_extra_diurno = $parametros->porcentaje_dominical_extra_diurno * $sal;
                $porcentaje_dominical_extra_nocturno = $parametros->porcentaje_dominical_extra_nocturno * $sal;

                $vardiurnas = ((double)$reporteu->diurnas) * $porcentaje_diurno;
                $varnocturnas = ((double)$reporteu->nocturnas) * $porcentaje_nocturno;
                $varextra_diurnas = ((double)$reporteu->extra_diurnas) * $porcentaje_extra_diurno;
                $varextra_nocturnas = ((double)$reporteu->extra_nocturnas) * $porcentaje_extra_nocturno;
                $vardominical_diurno = ((double)$reporteu->dominical_diurno) * $porcentaje_dominical_diurno;
                $vardominical_nocturno = ((double)$reporteu->dominical_nocturno) * $porcentaje_dominical_nocturno;
                $vardominical_extra_diurno = ((double)$reporteu->dominical_extra_diurno) * $porcentaje_dominical_extra_diurno;
                $vardominical_extra_nocturno = ((double)$reporteu->dominical_extra_nocturno) * $porcentaje_dominical_extra_nocturno;

                $decoratotal = ($vardiurnas + $varnocturnas + $varextra_diurnas + $varextra_nocturnas + $vardominical_diurno + $vardominical_nocturno + $vardominical_extra_diurno + $vardominical_extra_nocturno);
                $decoradiurnas = $vardiurnas;
                $decoranocturnas = $varnocturnas;
                $decoraextra_diurnas = $varextra_diurnas;
                $decoraextra_nocturnas = $varextra_nocturnas;
                $decoradominical_diurno = $vardominical_diurno;
                $decoradominical_nocturno = $vardominical_nocturno;
                $decoradominical_extra_diurno = $vardominical_extra_diurno;
                $decoradominical_extra_nocturno = $vardominical_extra_nocturno;

                $reporteu->horas_trabajadas = $decoratotal;
                $reporteu->diurnas = $decoradiurnas;
                $reporteu->nocturnas = $decoranocturnas;
                $reporteu->extra_diurnas = $decoraextra_diurnas;
                $reporteu->extra_nocturnas = $decoraextra_nocturnas;
                $reporteu->dominical_diurno = $decoradominical_diurno;
                $reporteu->dominical_nocturno = $decoradominical_nocturno;
                $reporteu->dominical_extra_diurno = $decoradominical_extra_diurno;
                $reporteu->dominical_extra_nocturno = $decoradominical_extra_nocturno;
                $vectorSuma = [

                ];
            }
            $reporteu->usera = $reporteu->user->name;

            return $reporteu;
        })->filter();

        return $centroCostos;
    }
}
