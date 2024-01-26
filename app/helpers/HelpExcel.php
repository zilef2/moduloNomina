<?php

namespace App\helpers;

use App\Models\Reporte;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;

// use Hamcrest\Type\IsInteger;

class HelpExcel
{
    public static function cumplioQuincena(&$users, $key, $ini, $fin,
                                           $empleado, &$reportes, &$salario_hora, &$salario_quincena,
                                           &$cumplioQuicena, $paramBD, $NumeroDiasFestivos,
                                           $ArrayDatesFest, $sigo = null) {
        $reportes = Reporte::where('user_id', $empleado->id)
            ->where('valido', 1)
            ->whereBetween('fecha_ini', [$ini, $fin]);
        $horasTotalTrabajador = (int)($reportes->sum('horas_trabajadas'));

        $reportesAgrupados = DB::table('reportes')
            ->where('user_id', $empleado->id)
            ->where('valido', 1)
            ->whereBetween('fecha_ini', [$ini, $fin])
            ->select('fecha_ini')
            ->distinct()
            ->orderBy('fecha_ini')
            ;
        $NumReportes = $reportesAgrupados->count();

        $currentDate = clone $ini;
//        $finishDate = clone $fin;
//        $finishDate->addDay();
        $workingDays = 0;
        $SaturDays = 0;

        while ($currentDate->lte($fin)) {
            if ($currentDate->isWeekday()) {
                $workingDays++;
            }
            if ($currentDate->isSaturday()) {
                $SaturDays++;
            }
            $currentDate->addDay();
        }
        foreach ($ArrayDatesFest as $item) {
            if($item->isWeekday()) $workingDays--;
            else{
                if ($item->isSaturday()) $SaturDays--;
            }
        }

        $elSalario = (int)($empleado->salario);
        $salario_hora = $elSalario / (235);// 30 * 7.8333
        $salario_quincena = round($elSalario / (2), 0, PHP_ROUND_HALF_UP);

        $horasN = $workingDays*8 + $SaturDays*7;
//        dd($ini,$fin,$workingDays,$SaturDays, $NumeroDiasFestivos,$horasN);
        $cumplioQuicena = $horasTotalTrabajador >= $horasN;

        if ($sigo === null) {
            $users[$key]->Completa = $cumplioQuicena ? 'Si (' .$horasTotalTrabajador.' de ' . $horasN . ')' : 'No (' .$horasTotalTrabajador.' de '. $horasN . ')';
            $users[$key]->Num = $reportesAgrupados->count();
            // $users[$key]->Num = $NumReportes; //quizas se use
            $users[$key]->Empleado = $empleado->name;
        }
        return $NumReportes;
    }

    public static function getFechaExcel($lafecha){
        //the date fix
        if (is_numeric($lafecha)) { //toproof
            $unixDate = ($lafecha - 25568) * 86400;
            // $unixDate = ($lafecha - 25569) * 86400;
            $readableDate = date('Y/m/d', $unixDate);
            $fechaResult = DateTime::createFromFormat('Y/m/d', $readableDate);

            if ($fechaResult === false) {
                $fechaResult = DateTime::createFromFormat('Y/m/d', $lafecha);
                if ($fechaResult === false) {
                    $fechaResult = DateTime::createFromFormat('d/m/Y', $lafecha);
                    if ($fechaResult === false) {
                        throw new \Exception('Fecha inválida 1');
                        // throw new \Exception('Fecha inválida '.$lafecha. ' --++--');
                        return null;
                    }
                }
            }
        } else {
            $fechaResult = DateTime::createFromFormat('Y/m/d', $lafecha);
            if ($fechaResult === false) {
                $fechaResult = DateTime::createFromFormat('d/m/Y', $lafecha);
                if ($fechaResult === false) {
                    throw new \Exception('Fecha inválida 2' . $lafecha);
                    return null;
                }
            }
        }
        return $fechaResult;
    }
}

// :class="  { 'bg-sky-600 dark:bg-sky-600': route().current('user.uploadexcel') }"
