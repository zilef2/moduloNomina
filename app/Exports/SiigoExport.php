<?php

namespace App\Exports;

use App\helpers\HelpExcel;
use App\Models\Parametro;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SiigoExport implements FromCollection,ShouldAutoSize,WithHeadings
{
    public $ini,$fin;
    public function __construct($ini,$fin)
    {
        $this->ini = $ini;
        $this->fin = $fin;
    }

    public function CalculoHorasExtrasDominicalesTodo($reportes,$cumplioQuicena,$salario_hora,$paramBD, &$H_diurno, &$nocturnas, &$extra_diurnas, &$extra_nocturnas, &$dominical_diurno, &$dominical_nocturno, &$dominical_extra_diurno, &$dominical_extra_nocturno){
        
        $H_diurno = round(intval($reportes->sum('diurnas')) * doubleval($salario_hora),0,PHP_ROUND_HALF_UP);

        $recargoNocturno = $cumplioQuicena ? $paramBD->porcentaje_nocturno-1 : $paramBD->porcentaje_nocturno;
        $nocturnas = intval($reportes->sum('nocturnas')) * doubleval($salario_hora) * $recargoNocturno;

        // extras simples
        $extra_diurnas = intval($reportes->sum('extra_diurnas')) * doubleval($salario_hora) * $paramBD->porcentaje_extra_diurno;
        $extra_nocturnas = intval($reportes->sum('extra_nocturnas')) * doubleval($salario_hora) * $paramBD->porcentaje_extra_nocturno;
        // dominicales 
        $dominical_diurno = intval($reportes->sum('dominical_diurno')) * doubleval($salario_hora) * $paramBD->porcentaje_dominical_diurno;
        $dominical_nocturno = intval($reportes->sum('dominical_nocturno')) * doubleval($salario_hora) * $paramBD->porcentaje_dominical_nocturno;
        // dominicales extras
        $dominical_extra_diurno = intval($reportes->sum('dominical_extra_diurno')) * doubleval($salario_hora) * $paramBD->porcentaje_dominical_extra_diurno;
        $dominical_extra_nocturno = intval($reportes->sum('dominical_extra_nocturno')) * doubleval($salario_hora) * $paramBD->porcentaje_dominical_extra_nocturno;


        return [
            intval($reportes->sum('extra_diurnas')) +
            intval($reportes->sum('extra_nocturnas')) +
            intval($reportes->sum('dominical_diurno')) +
            intval($reportes->sum('dominical_nocturno')) +
            intval($reportes->sum('dominical_extra_diurno')) +
            intval($reportes->sum('dominical_extra_nocturno')),

            intval($reportes->sum('extra_diurnas')) ,
            intval($reportes->sum('extra_nocturnas')) ,
            intval($reportes->sum('dominical_diurno')) ,
            intval($reportes->sum('dominical_nocturno')) ,
            intval($reportes->sum('dominical_extra_diurno')) ,
            intval($reportes->sum('dominical_extra_nocturno'))
        ];
    }

    public function unsetAllunnesesary(&$users,$contadorKey){
        $unecesary = ['id','name','cargo_id','salario'];
        foreach ($unecesary as $empleado) {
            unset($users[$contadorKey]->{$empleado});
        }
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {
        //traer todos los empleado
        $users = User::Select('id','name','cedula','cargo_id','salario')->WhereHas("roles", function($q){
            $q->Where("name", "empleado");
            $q->orWhere("name", "administrativo");
        })->get();
        $paramBD = Parametro::find(1);

        $contadorKey = 0;
        foreach ($users as $key => $empleado) {

            $users[$contadorKey]->Empleado = $empleado->cedula;
            $users[$contadorKey]->Empleado = $empleado->cedula;

            $SinNovedad = true;
            $tieneExtrasDiurnas = true;
            $NumReportes = HelpExcel::cumplioQuincena($users,$key,$this->ini,$this->fin,$empleado,$reportes, $salario_hora, $salario_quincena, $cumplioQuicena );

            $ArrayExtrasyDominicales = $this->CalculoHorasExtrasDominicalesTodo($reportes, $cumplioQuicena, $salario_hora, $paramBD, $H_diurno, $nocturnas, $extra_diurnas, $extra_nocturnas, $dominical_diurno, $dominical_nocturno, $dominical_extra_diurno, $dominical_extra_nocturno);
            
            $Num_nocturnas = intval($reportes->sum('nocturnas'));
            $Num_extra_diurnas = $ArrayExtrasyDominicales[1];
            $Num_extra_nocturnas = $ArrayExtrasyDominicales[2];
            $Num_dominical_diurno = $ArrayExtrasyDominicales[3];
            $Num_dominical_nocturno = $ArrayExtrasyDominicales[4];
            $Num_dominical_extra_diurno = $ArrayExtrasyDominicales[5];
            $Num_dominical_extra_nocturno = $ArrayExtrasyDominicales[6];

            $Total_ExtrasyDominicales = intval($ArrayExtrasyDominicales[0]);
dd($Num_extra_diurnas);
            if($Num_extra_diurnas > 0){
                $SinNovedad = false;
                $users[$contadorKey]->Quenov = '10- Horas extras diurnas 125%- Ingreso';
                $users[$contadorKey]->tiponov = 'Horas';
                $users[$contadorKey]->diasValornovedad = '';

            }

            if($SinNovedad){
                //si no se cumple ninguna condicion
                $users[$contadorKey]->Quenov = '';
                $users[$contadorKey]->tiponov = '';
                $users[$contadorKey]->diasValornovedad = '';
                $users[$contadorKey]->fechaininov = '';
                $users[$contadorKey]->fechafinnov = '';
                $users[$contadorKey]->diasnohabiles = '';
            }
            
            $this->unsetAllunnesesary($users,$contadorKey);

            $contadorKey++;
        }
        return $users;
    }

    public function headings() :array
    {
        return [
            '#Contrato del empleado',
            'Identificación del empleado',
            '¿Qué novedad le vas a cargar?',
            'Tipo de Novedad ',
            'Asigna la cantidad de Días/Horas o el valor de la novedad',
            '¿Cuál es la fecha de inicio de la novedad? (DD/MM/AAAA)',
            '¿Cuál es la fecha de fin de la novedad?  (DD/MM/AAAA)',
            'Indica en número, los días no hábiles (si no aplica coloca 0)'
        ];
    }
}

