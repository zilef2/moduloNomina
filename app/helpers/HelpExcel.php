<?php

namespace App\helpers;

use App\Models\Reporte;
use DateTime;

// use Hamcrest\Type\IsInteger;

class HelpExcel{


    public static function cumplioQuincena(&$users,$key,$ini,$fin,$empleado,&$reportes, &$salario_hora, &$salario_quincena, &$cumplioQuicena ) {
        define('DIAS_NECESARIAS_QUINCENA', 13);
        define('HORAS_NECESARIAS_QUINCENA', 96);
        
        $reportes = Reporte::where('user_id', $empleado->id)
            ->where('valido', 1)
            ->whereBetween('fecha_ini', [$ini, $fin]);


        $NumReportes = $reportes->count();
        $users[$key]->Completa = $NumReportes >= 13 ? 'Si, ' . $NumReportes . ' dias' : 'No, ' . $NumReportes . ' dias';
        $users[$key]->Empleado = $empleado->name;

        $elSalario = intval($empleado->salario);
        // $salario_dia = round($elSalario/30, 0, PHP_ROUND_HALF_UP);
        $salario_hora = $elSalario / (30 * 8);
        $salario_quincena = round($elSalario / (2), 0, PHP_ROUND_HALF_UP);
        $cumplioQuicena = $NumReportes >= DIAS_NECESARIAS_QUINCENA;

        return $NumReportes;
    }

    public static function getFechaExcel($lafecha) {
        //the date fix
        if(is_numeric($lafecha)){ //toproof
            $unixDate = ($lafecha - 25568) * 86400;
            // $unixDate = ($lafecha - 25569) * 86400;
            $readableDate = date('Y/m/d', $unixDate);
            $fechaResult = DateTime::createFromFormat('Y/m/d', $readableDate);

            if($fechaResult === false){
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
        }else{
            $fechaResult = DateTime::createFromFormat('Y/m/d', $lafecha);
            if ($fechaResult === false) {
                $fechaResult = DateTime::createFromFormat('d/m/Y', $lafecha);
                if ($fechaResult === false) {
                    throw new \Exception('Fecha inválida 2'.$lafecha);
                    return null;
                }
            }
        }
        return $fechaResult;
    }
}

// :class="  { 'bg-sky-600 dark:bg-sky-600': route().current('user.uploadexcel') }"