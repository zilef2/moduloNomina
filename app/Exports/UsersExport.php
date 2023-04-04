<?php

namespace App\Exports;

use App\Models\Cargo;
use App\Models\Parametro;
use App\Models\Reporte;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection,ShouldAutoSize,WithHeadings
{
    public $ini,$fin;
    public function __construct($ini,$fin)
    {
        $this->ini = $ini;
        $this->fin = $fin;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {
        $users = User::Select('id','name','cedula','cargo_id')
            ->WhereHas("roles", function($q){
                $q->Where("name", "operator");
            })->get();

        $param = Parametro::find(1);
        $sub_traporte_hora = $param->subsidio_de_transporte_hora;

        foreach ($users as $key => $value) {
            $reportes = Reporte::where('user_id',$value->id)
            ->whereBetween('fecha_ini',[$this->ini,$this->fin]); //toerase
            
            $users[$key]->Empleado = $value->name;
            $cargo = Cargo::find($value->cargo_id);
            unset($users[$key]->id);
            unset($users[$key]->name);
            unset($users[$key]->cargo_id);
            // $salario = intval($reportes->sum('horas_trabajadas')) * doubleval($cargo->salario_hora);

            $salario_hora = $cargo->salario_hora;
            $horasNormales = intval($reportes->sum('diurnas'));
            $diurno =  $horasNormales * doubleval($salario_hora);
            $users[$key]->salario = $diurno;
            $nocturnas = intval($reportes->sum('nocturnas')) * doubleval($salario_hora) * $param->porcentaje_nocturno;
            $extra_diurnas = intval($reportes->sum('extra_diurnas')) * doubleval($salario_hora) * $param->porcentaje_extra_diurno;
            $extra_nocturnas = intval($reportes->sum('extra_nocturnas')) * doubleval($salario_hora) * $param->porcentaje_extra_nocturno;

            $dominical_diurno = intval($reportes->sum('dominical_diurno')) * doubleval($salario_hora) * $param->porcentaje_dominical_diurno;
            $dominical_nocturno = intval($reportes->sum('dominical_nocturno')) * doubleval($salario_hora) * $param->porcentaje_dominical_nocturno;
            $dominical_extra_diurno = intval($reportes->sum('dominical_extra_diurno')) * doubleval($salario_hora) * $param->porcentaje_dominical_extra_diurno;
            $dominical_extra_nocturno = intval($reportes->sum('dominical_extra_nocturno')) * doubleval($salario_hora) * $param->porcentaje_dominical_extra_nocturno;
            
            $ExtraTotal = $nocturnas + $extra_diurnas + $extra_nocturnas + $dominical_diurno 
                + $dominical_nocturno + $dominical_extra_diurno + $dominical_extra_nocturno;
            $users[$key]->Horas_Extras = $ExtraTotal;
            $salYextras = $diurno + $ExtraTotal;

            $saludPension = round($salYextras*0.04, 0, PHP_ROUND_HALF_UP);
            $users[$key]->Salud = $saludPension;
            $users[$key]->Pension = $saludPension;

            if ($diurno >= ($param->valor_maximo_subsidio_de_transporte)) {
                $S_Transporte = 0;
            } else {
                $S_Transporte = $sub_traporte_hora * $horasNormales; //toask
            }
            $users[$key]->S_Transporte = $S_Transporte;
            
            $users[$key]->Prima = 0;
            $users[$key]->Vacaciones = 0;
            $users[$key]->Cesantias = 0;
            $users[$key]->Intereses = 0;
            $users[$key]->Prestamo = 0;
            $users[$key]->Anticipo = 0;
            $users[$key]->Auxilio = 0;
            $users[$key]->Bonificacion = 0;
            $users[$key]->Reintegro = 0;
            $users[$key]->Abono_Prestamo = 0;
            $users[$key]->Otras_Deducciones = 0;
            $users[$key]->Total_pagado = $salYextras + $S_Transporte + (2*$saludPension);
        }
        // dd($users[0],
        //     $this->ini,
        //     $this->fin,
        // );
        return $users;
    }

    public function headings() :array
    {
        return [
            // 'Año',
            // 'Quincena',
            'Cedula',
            'Empleado',
            'Salario',
            'Horas Extras',
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
