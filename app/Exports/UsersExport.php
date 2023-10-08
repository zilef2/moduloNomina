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


//Exportar el informe 
class UsersExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    public $ini, $fin, $NumeroDiasFestivos;
    public function __construct($ini, $fin, $NumeroDiasFestivos) {
        $this->ini = $ini;
        $this->fin = $fin;
        $this->NumeroDiasFestivos = $NumeroDiasFestivos;
    }

    //this function returns hours (the money is &$variables)
    public function CalculoHorasExtrasDominicalesTodo($reportes, $cumplioQuicena, $salario_hora, $paramBD, &$H_diurno, &$nocturnas, &$extra_diurnas, &$extra_nocturnas, &$dominical_diurno, &$dominical_nocturno, &$dominical_extra_diurno, &$dominical_extra_nocturno) {
        $H_diurno = round(intval($reportes->sum('diurnas')) * doubleval($salario_hora), 0, PHP_ROUND_HALF_UP);
        
        $recargoNocturno = $paramBD->porcentaje_nocturno;
        // $recargoNocturno = $cumplioQuicena ? $paramBD->porcentaje_nocturno - 1 : $paramBD->porcentaje_nocturno;
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

            intval($reportes->sum('extra_diurnas')),
            intval($reportes->sum('extra_nocturnas')),
            intval($reportes->sum('dominical_diurno')),
            intval($reportes->sum('dominical_nocturno')),
            intval($reportes->sum('dominical_extra_diurno')),
            intval($reportes->sum('dominical_extra_nocturno'))
        ];
    }

    public function SalarioHoras_OR_Dias($cumplioQuicena, $salario_quincena, &$users, $key, $H_diurno, $NumReportes) {
        // if($Total_Horas >= $HORAS_NECESARIAS_SEMANA){ // usuario cumplio con sus horas quincenales
        if ($cumplioQuicena) { // cumplio con los dias de la quincena
            $users[$key]->Salario = $salario_quincena;
            $diasEfectivos = 15;
        } else {
            $users[$key]->Salario = $H_diurno;
            //todo: no puede contar mas de un reporte por dia
            $diasEfectivos = $NumReportes;
        }
        return $diasEfectivos;
    }

    public function unsetAllunnesesary(&$users, $key) {
        $unecesary = ['id', 'name', 'cargo_id', 'salario'];
        foreach ($unecesary as $value) {
            unset($users[$key]->{$value});
        }
    }

    public function LunesSabadoSemana($ElLunes, $primeraQuincena, $value) {
        //?validar si trabajo de lunes a sabado
    }

    public function CalculoDomingoGanadosTodo($value) {
        $HORAS_NECESARIAS_SEMANA = intval(Parametro::find(1)->HORAS_NECESARIAS_SEMANA);
        $HORAS_NECESARIAS_QUINCENA = $HORAS_NECESARIAS_SEMANA * 2;

        $reportes = Reporte::Where('user_id', $value->id)
            ->where('valido', 1)
            ->whereBetween('fecha_ini', [$this->ini, $this->fin])->orderby('fecha_ini', 'asc');

        if ($reportes->count() == 0) return 0;

        $ElLunes = Carbon::parse($reportes->first()->fecha_ini)->startOfWeek();
        $esPrimeraQuincena = Carbon::parse($this->ini)->day == 1;

        //ANTES: con el lunes de la quincena, comenzamos a recorrer las semanas contando los domingos validos
        $domingosGanados = 0;
        $valido = true;

        $ElSabado = Carbon::parse($ElLunes)->endOfWeek(-1);
        while($valido){
            $ValidarReportes = Reporte::Where('user_id', $value->id)
                ->where('valido', 1)
                ->whereBetween('fecha_ini', [$ElLunes, $ElSabado])
                ->sum('horas_trabajadas');

            //? NOTE:: values 15-30
            //todo : esta malo, necesito saber si en la semana hubo festivo
            $HORAS_NECESARIAS_QUINCENA -= $this->NumeroDiasFestivos * 8;
            $domingosGanados += intval($ValidarReportes) >= ($HORAS_NECESARIAS_QUINCENA) ? 1 : 0;
            $ElLunes->addDays(7);
            $ElSabado = clone $ElLunes;
            $ElSabado->endOfWeek(-1);

            if($esPrimeraQuincena){
                $valido = $ElSabado->day < 16;
            }else{
                $valido = $ElSabado->day > 14;
            }
        }
        return $domingosGanados;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection() {
        $H_diurno = 0;
        $nocturnas = 0;
        $extra_diurnas = 0;
        $extra_nocturnas = 0;
        $dominical_diurno = 0;
        $dominical_nocturno = 0;
        $dominical_extra_diurno = 0;
        $dominical_extra_nocturno = 0;

        $paramBD = Parametro::find(1);

        //traer todos los empleado
        $users = User::Select('id', 'name', 'cedula', 'cargo_id', 'salario')->WhereHas("roles", function ($q) {
            $q->Where("name", "empleado");
            $q->orWhere("name", "administrativo");
        })->get();

        $pruebasCon = 0; //debug
        foreach ($users as $key => $value) {
            $NumReportes = HelpExcel::cumplioQuincena($users, $key, $this->ini, $this->fin, $value, $reportes, $salario_hora, $salario_quincena, $cumplioQuicena, $paramBD, $this->NumeroDiasFestivos);
            
            //? sumamos las horas que trabajo, y lo multiplicamos por el valor del tipo de hora
            $extrasyDominicales = $this->CalculoHorasExtrasDominicalesTodo($reportes, $cumplioQuicena, $salario_hora, $paramBD, $H_diurno, $nocturnas, $extra_diurnas, $extra_nocturnas, $dominical_diurno, $dominical_nocturno, $dominical_extra_diurno, $dominical_extra_nocturno);
            
            //? vemos si se paga por horas o por dias
            $diasEfectivos = $this->SalarioHoras_OR_Dias($cumplioQuicena, $salario_quincena, $users, $key, $H_diurno, $NumReportes);

            /**
            cambiar en |create reporte| cuando el reporte anterior finaliza en 11:59pm, entonces esas horas trabajadas se le suman al siguiente reporte
            */ 

            $domingosGanados = $this->CalculoDomingoGanadosTodo($value);
            
            $diasEfectivos += $domingosGanados; //se usa para el subsidio de transporte
            
            // if($pruebasCon ==0)dd($users[$key]->Salario,$cumplioQuicena); $pruebasCon ++; //debug
            if($salario_quincena == 0)dd(''.$value->name.' no tiene salario registrado');

            $users[$key]->Salario = $salario_quincena;
            $users[$key]->SalarioHora = $salario_hora;
            $users[$key]->diurnas = intval($reportes->sum('diurnas'));
            $users[$key]->nocturnas = intval($reportes->sum('nocturnas'));

            $users[$key]->extra_diurnas = $extrasyDominicales[1]; $users[$key]->extra_nocturnas = $extrasyDominicales[2]; $users[$key]->dominical_diurno = $extrasyDominicales[3]; $users[$key]->dominical_nocturno = $extrasyDominicales[4]; $users[$key]->dominical_extra_diurno = $extrasyDominicales[5]; $users[$key]->dominical_extra_nocturno = $extrasyDominicales[6];

            $users[$key]->extrasYDominicales = intval($extrasyDominicales[0]);
            $users[$key]->DerechoDomingo = intval($domingosGanados);

            $Total_Horas = $users[$key]->diurnas + $users[$key]->nocturnas + $users[$key]->extrasYDominicales;
            $users[$key]->Total_Horas = $Total_Horas;

            $this->unsetAllunnesesary($users, $key);

            $ExtraTotal = $extra_diurnas + $extra_nocturnas + $dominical_diurno + $dominical_nocturno + $dominical_extra_diurno + $dominical_extra_nocturno;
            $users[$key]->Valor_Horas_Extras = $ExtraTotal;//this is money
            
            // $salYextras es la variable que tiene todas las horas, diurnas, noc, extras,dominicales
            //# SALUD Y PENSION
                $salYextras = ($H_diurno + $nocturnas + $ExtraTotal);
            if ($cumplioQuicena) {
                // $salYextras = $users[$key]->Salario + $ExtraTotal;
                // $saludPension = round($salYextras * 0.04, 0, PHP_ROUND_HALF_UP); //QUEMADO: salud y la pension = salario total * 4%
                $saludPension = round($users[$key]->Salario * 0.04, 0, PHP_ROUND_HALF_UP);
                $users[$key]->Salud = $saludPension;
                $users[$key]->Pension = $saludPension;
            }else{
                // $salYextras = ($H_diurno + $ExtraTotal);
                $saludPension = 0;
                $users[$key]->Salud = 0;
                $users[$key]->Pension = 0;
            }

            //# Subsidio de transporte (por dias)
            $S_Transporte = ($users[$key]->Salario * 2) >= ($paramBD->valor_maximo_subsidio_de_transporte) ? 0 : $diasEfectivos * $paramBD->subsidio_de_transporte_dia;
            $users[$key]->S_Transporte = round($S_Transporte, 0, PHP_ROUND_HALF_UP);

            // # Novedades
            // $users[$key]->Prima = '0'; $users[$key]->Vacaciones = '0'; $users[$key]->Cesantias = '0'; $users[$key]->Intereses = '0'; $users[$key]->Prestamo = '0'; $users[$key]->Anticipo = '0'; $users[$key]->Auxilio = '0'; $users[$key]->Bonificacion = '0'; $users[$key]->Reintegro = '0'; $users[$key]->Abono_Prestamo = '0'; $users[$key]->Otras_Deducciones = '0';

            // # Total
            $users[$key]->salYextras = $salYextras;
            $users[$key]->Total_pagado = round(($salYextras + $S_Transporte) - (2 * $saludPension), 0, PHP_ROUND_HALF_UP);
        }
        dd(
            // $users[0],//empleado
            // $users[1],//elalejo
            // $users[2],//Alejo
            // $users[3],//jose
            $users[2]->toArray(),
        );
        return $users;
    }

    public function headings(): array {
        return [
            // 'Año',
            // 'Quincena',
            'Cedula',
            'Quincena Completa',
            'Num reportes',
            'Empleado',
            'Salario (Quincena)',
            'Salario (Hora)',
            'diurnas', 'nocturnas',
            'extra diurnas', 'extra nocturnas', 'dominical diurno', 'dominical nocturno', 'dominical extra diurno', 'dominical extra nocturno',
            'extrasYDominicales',
            'Domingos',
            'Total Horas',

            'Valor Horas Extras',
            'Salud',
            'Pension',
            'S Transporte',
            // 'Prima',
            // 'Vacaciones',
            // 'Cesantias',
            // 'Intereses',
            // 'Prestamo',
            // 'Anticipo',
            // 'Auxilio',
            // 'Bonificacion',
            // 'Reintegro',
            // 'Abono Prestamo',
            // 'Otras Deducciones',
            'Salario Y extras',
            'Total pagado',
        ];
    }
}
