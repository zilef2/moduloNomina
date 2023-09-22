<?php

namespace App\helpers;

use App\Models\Reporte;
use DateTime;
use Illuminate\Support\Facades\DB;

// use Hamcrest\Type\IsInteger;

class HelpExcel
{
    //todo: domingosGanados
    public static function cumplioQuincena(&$users, $key, $ini, $fin, $empleado, &$reportes, &$salario_hora, &$salario_quincena, &$cumplioQuicena, $paramBD, $NumeroDiasFestivos, $sigo = null) {
        $reportes = Reporte::where('user_id', $empleado->id)
            ->where('valido', 1)
            ->whereBetween('fecha_ini', [$ini, $fin]);

        $reportesAgrupados = DB::table('reportes')
            ->where('user_id', $empleado->id)
            ->where('valido', 1)
            ->whereBetween('fecha_ini', [$ini, $fin])
            ->select('fecha_ini')
            ->distinct()
            ->orderBy('fecha_ini')
            ;

        $NumReportes = $reportes->count();

        $elSalario = intval($empleado->salario);
        $salario_hora = $elSalario / (30 * 8);
        $salario_quincena = round($elSalario / (2), 0, PHP_ROUND_HALF_UP);


        $horasN = (intval($paramBD->HORAS_NECESARIAS_SEMANA) * 2) - ($NumeroDiasFestivos * 8); //por defecto $horasN es 47, pero si hay festivos se le tiene que restar
        $horasTotalTrabajador = intval($reportes->sum('horas_trabajadas'));
        $cumplioQuicena = $horasTotalTrabajador >= $horasN;

        if ($sigo === null) {
            $users[$key]->Completa = $cumplioQuicena ? 'Si (' . $horasN . ')' : 'No (' . $horasN . ')';
            $users[$key]->Num = $reportesAgrupados->count();
            // $users[$key]->Num = $NumReportes; //quizas se use
            $users[$key]->Empleado = $empleado->name;
        }
        return $NumReportes;
    }

    public static function getFechaExcel($lafecha)
    {
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