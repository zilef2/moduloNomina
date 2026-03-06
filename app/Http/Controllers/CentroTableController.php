<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\Models\CentroCosto;
use App\Models\Parametro;
use App\Models\Reporte;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class CentroTableController extends Controller
{
    private function TheQuery(int $centrocostoid, Request $request, bool $acumulado = false): array
    {
        $fecha = $request->input('fecha_ini', ['month' => now()->subMonth()->month, 'year' => now()->year]);
        if ($request->input('fecha_ini.month') == 0) {
        }

        $defaultMonth = now()->subMonth()->month - 1; // 0-based index for frontend compatibility if needed, but wait, the controller logic uses month+1 later?

        $defaultDate = now()->subMonth();
        $defaultMonthIndex = $defaultDate->month - 1; // 0-indexed
        $defaultYear = $defaultDate->year;

        $fecha = $request->input('fecha_ini');
        if(!$fecha){
            $fecha = ['month' => now()->month - 1, 'year' => now()->year]; // month is 0-indexed in frontend, 1-indexed in backend usually but here we use month+1.
        }

        $fecha = $request->input('fecha_ini');
        if(!$fecha){
             $fecha = ['month' => now()->month - 1, 'year' => now()->year]; // month is 0-indexed for frontend so current month is now()->month - 1.
        }

        $opcionQuincena = 3;
        if ($request->has('quincena')) {
            $quincenaInput = $request->input('quincena');
            if (is_array($quincenaInput) && isset($quincenaInput['value'])) {
                $opcionQuincena = $quincenaInput['value'];
            } elseif (is_numeric($quincenaInput)) {
                $opcionQuincena = $quincenaInput;
            }
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

        $query = Reporte::select($elSelect)
            ->where('valido', 1)
            ->where('centro_costo_id', $centrocostoid)
            ->groupBy('user_id');

        if (!$acumulado) {
            $query->whereYear('fecha_ini', $fecha['year'])->whereMonth('fecha_ini', $fecha['month'] + 1);
            if ($opcionQuincena == 1) $query->whereDay('fecha_ini', '<=', 15);
            if ($opcionQuincena == 2) $query->whereDay('fecha_ini', '>', 15);
        }

        $reportes = $query->get();

        $reportes->each(function($r){
            if($r->user) $r->usera = $r->user->name;
            else $r->usera = 'Usuario no encontrado';
        });

        return [$reportes, $reportes->sum('horas_trabajadas')];
    }

    private function getMoneyModeReportsAndTotal($reportsCollection) {
        $parametros = Parametro::find(1);
        $totalManoObra = 0;

        $reportsMoneyMode = $reportsCollection->map(function ($reporteu) use ($parametros, &$totalManoObra) {
            $user = User::find($reporteu->user_id);
            if ($user && $user->salario > 0) {
                $salHora = $user->salario / 235;
                $reporteClonado = clone $reporteu;

                $reporteClonado->diurnas = $reporteu->diurnas * ($salHora * $parametros->porcentaje_diurno);
                $reporteClonado->nocturnas = $reporteu->nocturnas * ($salHora * $parametros->porcentaje_nocturno);
                $reporteClonado->extra_diurnas = $reporteu->extra_diurnas * ($salHora * $parametros->porcentaje_extra_diurno);
                $reporteClonado->extra_nocturnas = $reporteu->extra_nocturnas * ($salHora * $parametros->porcentaje_extra_nocturno);
                $reporteClonado->dominical_diurno = $reporteu->dominical_diurno * ($salHora * $parametros->porcentaje_dominical_diurno);
                $reporteClonado->dominical_nocturno = $reporteu->dominical_nocturno * ($salHora * $parametros->porcentaje_dominical_nocturno);
                $reporteClonado->dominical_extra_diurno = $reporteu->dominical_extra_diurno * ($salHora * $parametros->porcentaje_dominical_extra_diurno);
                $reporteClonado->dominical_extra_nocturno = $reporteu->dominical_extra_nocturno * ($salHora * $parametros->porcentaje_dominical_extra_nocturno);

                $costoReporte = $reporteClonado->diurnas + $reporteClonado->nocturnas + $reporteClonado->extra_diurnas + $reporteClonado->extra_nocturnas + $reporteClonado->dominical_diurno + $reporteClonado->dominical_nocturno + $reporteClonado->dominical_extra_diurno + $reporteClonado->dominical_extra_nocturno;

                $reporteClonado->horas_trabajadas = $costoReporte;
                $totalManoObra += $costoReporte;
                $reporteClonado->usera = $user->name;
                return $reporteClonado;
            }
            return $reporteu;
        });

        return [$reportsMoneyMode, $totalManoObra];
    }

    public function table(Request $request, $id): Response
    {
        $permissions = Myhelp::EscribirEnLog($this, ' |reportes table| ');
        $numberPermissions = MyModels::getPermissionToNumber($permissions);

        [$fromControllerHorasCollection, $totalMesHoras] = $this->TheQuery($id, $request, false);
        [$fromControllerPlataCollection, $totalMesPlata] = $this->getMoneyModeReportsAndTotal(clone $fromControllerHorasCollection);

        [$fromControllerAcumuladoCollection, ] = $this->TheQuery($id, $request, true);
        [$fromControllerAcumuladoPlataCollection, $totalAcumulado] = $this->getMoneyModeReportsAndTotal(clone $fromControllerAcumuladoCollection);

        $acumuladoTotals = $this->calculateColumnTotals($fromControllerAcumuladoPlataCollection);
        $chartData = $this->prepareChartData($acumuladoTotals, $totalAcumulado);
        $firstReportDate = Reporte::where('centro_costo_id', $id)->min('fecha_ini');
        $monthlyTrendData = $this->getMonthlyTrendData($id);
        $yearOptions = $firstReportDate ? range(Carbon::parse($firstReportDate)->year, now()->year) : [now()->year];

        // Paginate the collections
        $perPage = 200;
        $page = request('page', 1);

        $fromControllerHoras = new LengthAwarePaginator(
            $fromControllerHorasCollection->forPage($page, $perPage),
            $fromControllerHorasCollection->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );
        $fromControllerPlata = new LengthAwarePaginator(
            $fromControllerPlataCollection->forPage($page, $perPage),
            $fromControllerPlataCollection->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );
        $fromControllerAcumulado = new LengthAwarePaginator(
            $fromControllerAcumuladoPlataCollection->forPage($page, $perPage),
            $fromControllerAcumuladoPlataCollection->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );


        return Inertia::render('CentroCostos/table', [
            'elIDD' => $id,
            'title' => CentroCosto::find($id)->nombre,
            'filters' => $request->all(['fecha_ini', 'quincena', 'plata']),
            'fromControllerHoras' => $fromControllerHoras,
            'fromControllerPlata' => $fromControllerPlata,
            'fromControllerAcumulado' => $fromControllerAcumulado,
            'totalMesHoras' => $totalMesHoras,
            'totalMesPlata' => $totalMesPlata,
            'totalAcumulado' => $totalAcumulado,
            'acumuladoTotals' => $acumuladoTotals,
            'chartData' => $chartData,
            'monthlyTrendData' => $monthlyTrendData,
            'nombresTabla' => $this->noumbresTabla($numberPermissions),
            'UltimoReporteRealizado' => $this->UltimoReporteRealizadx($id),
            'firstReportDate' => $firstReportDate ? Carbon::parse($firstReportDate)->format('F Y') : null,
            'yearOptions' => $yearOptions,
        ]);
    }

    private function calculateColumnTotals($data) {
        $totals = [
            'diurnas' => 0, 'nocturnas' => 0, 'extra_diurnas' => 0, 'extra_nocturnas' => 0,
            'dominical_diurno' => 0, 'dominical_nocturno' => 0, 'dominical_extra_diurno' => 0, 'dominical_extra_nocturno' => 0,
        ];
        foreach ($data as $report) {
            foreach ($totals as $key => &$value) {
                $value += $report->$key;
            }
        }
        return $totals;
    }

    private function getMonthlyTrendData($centroCostoId) {
        $monthlyCosts = Reporte::where('centro_costo_id', $centroCostoId)
            ->select(
                DB::raw('YEAR(fecha_ini) as year'),
                DB::raw('MONTH(fecha_ini) as month')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $labels = [];
        $data = [];

        foreach ($monthlyCosts as $cost) {
            $labels[] = Carbon::createFromDate($cost->year, $cost->month, 1)->format('M Y');
            $reportesDelMes = Reporte::where('centro_costo_id', $centroCostoId)
                ->whereYear('fecha_ini', $cost->year)
                ->whereMonth('fecha_ini', $cost->month)
                ->get();
            [, $costoMensual] = $this->getMoneyModeReportsAndTotal(clone $reportesDelMes);
            $data[] = $costoMensual;
        }

        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Costo Mensual',
                'backgroundColor' => '#34d399',
                'borderColor' => '#34d399',
                'data' => $data,
                'tension' => 0.1,
            ]],
        ];
    }


    private function prepareChartData($totals, $grandTotal) {
        $labels = array_keys($totals);
        $data = array_values($totals);

        array_unshift($labels, 'Total');
        array_unshift($data, $grandTotal);

        $backgroundColors = array_map(fn($label) => $label === 'Total' ? '#ef4444' : '#f59e0b', $labels);

        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Costo Acumulado',
                'backgroundColor' => $backgroundColors,
                'data' => $data,
            ]],
        ];
    }

    public function MultiplicarPorSalario($Reportes)
    {
        $parametros = Parametro::find(1);
        $totalManoObra = 0;

        $reportesCalculados = $Reportes->map(function ($reporteu) use ($parametros, &$totalManoObra) {
            $user = User::find($reporteu->user_id);
            if ($user && $user->salario > 0) {
                $salHora = $user->salario / 235;
                $reporteClonado = clone $reporteu;

                $reporteClonado->diurnas = $reporteu->diurnas * ($salHora * $parametros->porcentaje_diurno);
                $reporteClonado->nocturnas = $reporteu->nocturnas * ($salHora * $parametros->porcentaje_nocturno);
                $reporteClonado->extra_diurnas = $reporteu->extra_diurnas * ($salHora * $parametros->porcentaje_extra_diurno);
                $reporteClonado->extra_nocturnas = $reporteu->extra_nocturnas * ($salHora * $parametros->porcentaje_extra_nocturno);
                $reporteClonado->dominical_diurno = $reporteu->dominical_diurno * ($salHora * $parametros->porcentaje_dominical_diurno);
                $reporteClonado->dominical_nocturno = $reporteu->dominical_nocturno * ($salHora * $parametros->porcentaje_dominical_nocturno);
                $reporteClonado->dominical_extra_diurno = $reporteu->dominical_extra_diurno * ($salHora * $parametros->porcentaje_dominical_extra_diurno);
                $reporteClonado->dominical_extra_nocturno = $reporteu->dominical_extra_nocturno * ($salHora * $parametros->porcentaje_dominical_extra_nocturno);

                $costoReporte = $reporteClonado->diurnas + $reporteClonado->nocturnas + $reporteClonado->extra_diurnas + $reporteClonado->extra_nocturnas + $reporteClonado->dominical_diurno + $reporteClonado->dominical_nocturno + $reporteClonado->dominical_extra_diurno + $reporteClonado->dominical_extra_nocturno;

                $reporteClonado->horas_trabajadas = $costoReporte;
                $totalManoObra += $costoReporte;
                $reporteClonado->usera = $user->name;
                return $reporteClonado;
            }
            return $reporteu;
        });

        return [$reportesCalculados, $totalManoObra];
    }


    private function noumbresTabla($numberPermissions): array
    {
        return [
            ['Acciones', '#', 'nombre', 'Mano obra estimada', 'usuarios', 'Supervisor', 'activo', 'descripcion', 'clasificacion'],
            [null, null, 'nombre', 'mano_obra_estimada', null, null, 'activo', 'descripcion', 'clasificacion'],
        ];
    }

    private function UltimoReporteRealizadx($idcentrocosto): string
    {
        $ultimorepo = Reporte::where('centro_costo_id', $idcentrocosto)->latest('fecha_ini')->first();
        return $ultimorepo ? 'La ultima modificación de este centro de costos fue: ' . Carbon::parse($ultimorepo->fecha_ini)->diffForHumans() : '_';
    }
}
