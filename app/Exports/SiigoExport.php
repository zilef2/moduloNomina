<?php

namespace App\Exports;

use App\helpers\HelpExcel;
use App\Models\Parametro;
use App\Models\Reporte;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SiigoExport implements FromCollection,ShouldAutoSize,WithHeadings
{
    public $ini,$fin, $NumeroDiasFestivos;
    public function __construct($ini,$fin, $NumeroDiasFestivos) {
        $this->ini = $ini;
        $this->fin = $fin;
        $this->NumeroDiasFestivos = $NumeroDiasFestivos;
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

            intval($reportes->sum('extra_diurnas')) , //2
            intval($reportes->sum('extra_nocturnas')) , //3
            intval($reportes->sum('dominical_diurno')) , //4
            intval($reportes->sum('dominical_nocturno')) , //5
            intval($reportes->sum('dominical_extra_diurno')) , //6
            intval($reportes->sum('dominical_extra_nocturno')) //7
        ];
    }

    public function unsetAllunnesesary(&$users) {
        $unecesary = ['name','cargo_id','salario'];
        foreach ($users as $user) {
            foreach ($unecesary as $empleado) {
                unset($user->{$empleado});
            }
        }
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {
        //traer todos los empleado
        $users = User::Select('id','name','cedula','cargo_id','salario')->WhereHas("roles", function($q){
            $q->Where("name", "empleado");
            $q->orWhere("name", "supervisor");
        })->get();
        $paramBD = Parametro::find(1);
        $mensajeSigo = [
            '', //0
            '10- Horas extras diurnas 125%- Ingreso', //1 extra diurna
            '11- Horas extras nocturnas 175%- Ingreso', //2 extra noc

            // '25- Recargo dominical o festivo- Ingreso', //3 dom diurna
            '08- Hora extra recargo dominical o festivo- Ingreso', //3 dom diurna
            '06- Hora dominical o festiva nocturna- Ingreso', //4 dominical noc

            '07- Hora extra diurna dominical o festiva- Ingreso', //5 dom extra diurna
            '12- Horas extras nocturnas dominical o festiva- Ingreso', //6 dom extra noc

            '26- Recargo nocturno- Ingreso', //7 noc
        ];

        $this->unsetAllunnesesary($users);
        foreach ($users as $key => $empleado) {

            $empleado->Contrato = $empleado->cedula;

            HelpExcel::cumplioQuincena(
                $users,$key,$this->ini,$this->fin,
                $empleado,$reportes, $salario_hora,
                $salario_quincena, $cumplioQuicena,$paramBD,
                $this->NumeroDiasFestivos,session('datesFest'),'siigo');
            $ArrayExtrasyDominicales = $this->CalculoHorasExtrasDominicalesTodo($reportes, $cumplioQuicena, $salario_hora, $paramBD, $H_diurno, $nocturnas, $extra_diurnas, $extra_nocturnas, $dominical_diurno, $dominical_nocturno, $dominical_extra_diurno, $dominical_extra_nocturno);
            // $Num_extra_diurnas = $ArrayExtrasyDominicales[1]; $Num_extra_nocturnas = $ArrayExtrasyDominicales[2]; $Num_dominical_diurno = $ArrayExtrasyDominicales[3]; $Num_dominical_nocturno = $ArrayExtrasyDominicales[4]; $Num_dominical_extra_diurno = $ArrayExtrasyDominicales[5]; $Num_dominical_extra_nocturno = $ArrayExtrasyDominicales[6];
            $ArrayExtrasyDominicales[7] = intval($reportes->sum('nocturnas'));
            $Novedad = 0;
            unset($empleado->id);
            // $empleado->diasnohabiles = "0";

            for ($i=1; $i < 8; $i++) {
                if($ArrayExtrasyDominicales[$i] > 0){
                    if($Novedad != 0){
                        $nuevoReporte = clone $empleado;
                        $nuevoReporte->Quenov = $mensajeSigo[$i];
                        $nuevoReporte->diasValornovedad = $ArrayExtrasyDominicales[$i];
                        $users->splice(($key+1),0,[$nuevoReporte]);
                    }else{
                        $empleado->Quenov = $mensajeSigo[$i];
                        $empleado->tiponov = 'Horas';
                        $empleado->diasValornovedad = $ArrayExtrasyDominicales[$i];
                        $empleado->fechaininov = '';
                        $empleado->fechafinnov = '';

                    }
                    $Novedad++;//novedades de un usuarios
                }
            }

            if($Novedad === 0){ //Sin novedades
                unset($users[$key]);
//                $empleado->Quenov = '';
//                $empleado->tiponov = '';
//                $empleado->diasValornovedad = '';
//                $empleado->fechaininov = '';
//                $empleado->fechafinnov = '';
            }
        }

        // dd($users);
        return $users;
    }

    public function headings() :array {
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

