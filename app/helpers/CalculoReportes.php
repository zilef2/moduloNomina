<?php

namespace App\helpers;

use App\Http\Controllers\ReportesController;
use App\Models\Reporte;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CalculoReportes {

//    const MyRoles = [ //not using
//        'empleado' => 1, 'administrativo' => 2, 'supervisor' => 3, 'admin' => 9, 'superadmin' => 10];

    const int SEMANAS_A_FUTURO = 20;

    public static function CalcularHorasDeCadaSemana(int $AuthuserId): array {

        //calcula primer y ultimo dia de las x proximas semanas
        $vector = self::HorasDeLasSemanasProximas();
        $horasemana[0] = Carbon::now()->weekOfYear;

        $reportesControler = new ReportesController();
        $Ids_CentrosNoFactura = $reportesControler->DoArrayCentrosNoFactura();

        $idsString = !empty($Ids_CentrosNoFactura)
            ? implode(',', $Ids_CentrosNoFactura)
            : 'NULL';  // Manejar caso vacío para evitar IN ()

        foreach ($vector as $vec) {
//            $horasemana[$vec['numero_semana']] = (int)Reporte::Where('user_id', $Authuser->id)
//                ->WhereBetween('fecha_ini', [$vec['primer_dia_semana'], $vec['ultimo_dia_semana']])
//                ->selectRaw('fecha_ini, (diurnas + nocturnas) as ordinarias')
//                ->get()->sum('ordinarias');

            $horasemana[$vec['numero_semana']] = (int)Reporte::where('user_id', $AuthuserId)
                ->whereBetween('fecha_ini', [$vec['primer_dia_semana'], $vec['ultimo_dia_semana']])
                ->sum(DB::raw(" CASE 
                WHEN centro_costo_id IN ($idsString) 
                THEN LEAST(diurnas + nocturnas, 8)
                ELSE diurnas + nocturnas
            END"));
        }
        return $horasemana;
    }

    private static function HorasDeLasSemanasProximas(): array {//calcula primer y ultimo dia de las semanas
        $vectorSemanas = [];
        $fechaActual = Carbon::now()->addMonths(2);

        for ($i = 0; $i < self::SEMANAS_A_FUTURO; $i++) {
            // Calcular el primer día de la semana
            $primerDiaSemana = $fechaActual->startOfWeek();
            $ultimoDiaSemana = clone $primerDiaSemana;
            $ultimoDiaSemana = $ultimoDiaSemana->endOfWeek();

            // Almacenar el número de la semana y el primer día de la semana en el vector
            $vectorSemanas[] = [
                'numero_semana' => $primerDiaSemana->weekOfYear,
                'anio' => $primerDiaSemana->year,
                'primer_dia_semana' => $primerDiaSemana->toDateString(),
                'ultimo_dia_semana' => $ultimoDiaSemana->toDateString()
            ];
            // Moverse a la semana anterior
            $fechaActual->subWeek();
        }
        return $vectorSemanas;
    }
}