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

class UsersExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    public $ini, $fin;
    public function __construct($ini, $fin)
    {
        $this->ini = $ini;
        $this->fin = $fin;
    }

    public function CalculoHorasExtrasDominicalesTodo($reportes, $cumplioQuicena, $salario_hora, $paramBD, &$H_diurno, &$nocturnas, &$extra_diurnas, &$extra_nocturnas, &$dominical_diurno, &$dominical_nocturno, &$dominical_extra_diurno, &$dominical_extra_nocturno) {
        $H_diurno = round(intval($reportes->sum('diurnas')) * doubleval($salario_hora), 0, PHP_ROUND_HALF_UP);

        $recargoNocturno = $cumplioQuicena ? $paramBD->porcentaje_nocturno - 1 : $paramBD->porcentaje_nocturno;
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

    public function SalarioHoras_OR_Dias($cumplioQuicena, $salario_quincena, &$users, $key, $H_diurno, $NumReportes)
    {
        // if($Total_Horas >= $HORAS_NECESARIAS_QUINCENA){ // usuario cumplio con sus horas quincenales
        if ($cumplioQuicena) { // cumplio con los dias de la quincena
            $users[$key]->Salario = $salario_quincena;
            $diasEfectivos = 15;
            $cumplioQuicena = true;
        } else {
            $users[$key]->Salario = $H_diurno;
            $diasEfectivos = $NumReportes;
        }
        return $diasEfectivos;
    }

    public function unsetAllunnesesary(&$users, $key)
    {
        $unecesary = ['id', 'name', 'cargo_id', 'salario'];
        foreach ($unecesary as $value) {
            unset($users[$key]->{$value});
        }
    }

    public function LunesSabadoSemana($ElLunes, $primeraQuincena, $value) {
        
    }

    public function CalculoDomingoGanadosTodo($value) {
        // $reportesOrdenados = $reportes->orderby('fecha_ini','asc');

        $reportes = Reporte::Where('user_id', $value->id)
            ->where('valido', 1)
            ->whereBetween('fecha_ini', [$this->ini, $this->fin])->orderby('fecha_ini', 'asc');

        if($reportes->count() == 0) return 0;

        $ElLunes = Carbon::parse($reportes->first()->fecha_ini)->startOfWeek();
        $esPrimeraQuincena = Carbon::parse($this->ini)->day == 1;

        //con el lunes de la quincena, comenzamos a recorrer las semanas contando los domingos validos
        $valido = true;
        $domingosGanados = 0;
        $ElSabado = Carbon::parse($ElLunes)->endOfWeek(-1);
        while($valido) {
            if ($esPrimeraQuincena) {
                $valido = ($ElSabado->day) < 15;
            } else {
                $valido = ($ElSabado->day) < $ElLunes->endOfMonth(-1)->day;
            }
            if (!$valido) {
                break;
            }

            $ValidarReportes = Reporte::Where('user_id', $value->id)
                ->where('valido', 1)
                ->whereBetween('fecha_ini', [$ElLunes, $ElSabado])
                ->count();
            $domingosGanados += intval($ValidarReportes == 6 ? 1 : 0);
            $ElLunes->addDays(7);
            $ElSabado = clone $ElLunes;
            $ElSabado->endOfWeek(-1);

        }
        return $domingosGanados;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $H_diurno = 0;
        $nocturnas = 0;
        $extra_diurnas = 0;
        $extra_nocturnas = 0;
        $dominical_diurno = 0;
        $dominical_nocturno = 0;
        $dominical_extra_diurno = 0;
        $dominical_extra_nocturno = 0;

        $paramBD = Parametro::find(1);

        define('DIAS_NECESARIAS_QUINCENA', 13);
        define('HORAS_NECESARIAS_QUINCENA', 96);

        //traer todos los empleado
        $users = User::Select('id', 'name', 'cedula', 'cargo_id', 'salario')->WhereHas("roles", function ($q) {
            $q->Where("name", "empleado");
            $q->orWhere("name", "administrativo");
        })->get();

        $pruebasCon = 0; //debug
        foreach ($users as $key => $value) {
            // $reportes = Reporte::where('user_id', $value->id)
            //     ->where('valido', 1)
            //     ->whereBetween('fecha_ini', [$this->ini, $this->fin]);


            // $NumReportes = $reportes->count();
            // $users[$key]->Completa = $NumReportes >= 13 ? 'Si, ' . $NumReportes . ' dias' : 'No, ' . $NumReportes . ' dias';
            // $users[$key]->Empleado = $value->name;

            // $elSalario = intval($value->salario);


            // // $salario_dia = round($elSalario/30, 0, PHP_ROUND_HALF_UP);
            // $salario_hora = $elSalario / (30 * 8);
            // $salario_quincena = round($elSalario / (2), 0, PHP_ROUND_HALF_UP);
            // $cumplioQuicena = $NumReportes >= DIAS_NECESARIAS_QUINCENA;
            $NumReportes = HelpExcel::cumplioQuincena($users,$key,$this->ini,$this->fin,$value,$reportes, $salario_hora, $salario_quincena, $cumplioQuicena );

            
            $extrasyDominicales = $this->CalculoHorasExtrasDominicalesTodo($reportes, $cumplioQuicena, $salario_hora, $paramBD, $H_diurno, $nocturnas, $extra_diurnas, $extra_nocturnas, $dominical_diurno, $dominical_nocturno, $dominical_extra_diurno, $dominical_extra_nocturno);
            $diasEfectivos = $this->SalarioHoras_OR_Dias($cumplioQuicena, $salario_quincena, $users, $key, $H_diurno, $NumReportes);
            $domingosGanados = $this->CalculoDomingoGanadosTodo($value);
            $diasEfectivos += $domingosGanados;
            // if($pruebasCon ==0)dd($users[$key]->Salario,$cumplioQuicena); $pruebasCon ++; //debug

            $users[$key]->diurnas = intval($reportes->sum('diurnas'));
            $users[$key]->nocturnas = intval($reportes->sum('nocturnas'));
            $users[$key]->extra_diurnas = $extrasyDominicales[1];
            $users[$key]->extra_nocturnas = $extrasyDominicales[2];
            $users[$key]->dominical_diurno = $extrasyDominicales[3];
            $users[$key]->dominical_nocturno = $extrasyDominicales[4];
            $users[$key]->dominical_extra_diurno = $extrasyDominicales[5];
            $users[$key]->dominical_extra_nocturno = $extrasyDominicales[6];

            $users[$key]->extrasYDominicales = intval($extrasyDominicales[0]);
            $users[$key]->DerechoDomingo = intval($domingosGanados);

            $Total_Horas = $users[$key]->diurnas + $users[$key]->nocturnas + $users[$key]->extrasYDominicales;
            $users[$key]->Total_Horas = $Total_Horas;


            $this->unsetAllunnesesary($users, $key);

            $ExtraTotal = $nocturnas + $extra_diurnas + $extra_nocturnas + $dominical_diurno + $dominical_nocturno + $dominical_extra_diurno + $dominical_extra_nocturno;
            $users[$key]->Horas_Extras = $ExtraTotal;
            $salYextras = $users[$key]->Salario + $ExtraTotal;

            //# SALUD Y PENSION
            if ($NumReportes > 0) { // cumplio con los dias de la quincena
                $saludPension = round($salYextras * 0.04, 0, PHP_ROUND_HALF_UP); //QUEMADO: salud y la pension = salario total * 4%
                $users[$key]->Salud = $saludPension;
                $users[$key]->Pension = $saludPension;
            } else {
                $saludPension = 0;
                $users[$key]->Salud = 0;
                $users[$key]->Pension = 0;
            }

            //# Subsidio de transporte
            $S_Transporte = ($users[$key]->Salario * 2) >= ($paramBD->valor_maximo_subsidio_de_transporte) ? 0 : $diasEfectivos * $paramBD->subsidio_de_transporte_dia;
            $users[$key]->S_Transporte = round($S_Transporte, 0, PHP_ROUND_HALF_UP);

            // # Novedades
            $users[$key]->Prima = '0';
            $users[$key]->Vacaciones = '0';
            $users[$key]->Cesantias = '0';
            $users[$key]->Intereses = '0';
            $users[$key]->Prestamo = '0';
            $users[$key]->Anticipo = '0';
            $users[$key]->Auxilio = '0';
            $users[$key]->Bonificacion = '0';
            $users[$key]->Reintegro = '0';
            $users[$key]->Abono_Prestamo = '0';
            $users[$key]->Otras_Deducciones = '0';

            // # Total
            $users[$key]->Total_pagado = round(($salYextras + $S_Transporte) - (2 * $saludPension), 0, PHP_ROUND_HALF_UP);
            // dd($users[$key]->getAttributes());
        }
        return $users;
    }

    public function headings(): array { 
        return [
            // 'Año',
            // 'Quincena',
            'Cedula',
            'Quincena Completa',
            'Empleado',
            'Salario (Quincena)',
            'diurnas', 'nocturnas',
            'extra_diurnas','extra_nocturnas','dominical_diurno','dominical_nocturno','dominical_extra_diurno','dominical_extra_nocturno',
            'extrasYDominicales',
            'Domingos',
            'Total_Horas',

            'Horas_Extras', 
            'Salud',
            'Pension',
            'S_Transporte',
            'Prima',
            'Vacaciones',
            'Cesantias',
            'Intereses',
            'Prestamo',
            'Anticipo',
            'Auxilio',
            'Bonificacion',
            'Reintegro',
            'Abono_Prestamo',
            'Otras_Deducciones',
            'Total_pagado',
            // "name",
            // "email",
            // "created_at",
            // "updated_at",
        ];
    }
}
