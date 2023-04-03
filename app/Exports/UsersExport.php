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
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {
        $users = User::WhereHas("roles", function($q){
             $q->Where("name", "operator");
        })->get();

        $param = Parametro::find(1);
        $maxSalarioParametros = 2000* 1000;
        $S_Transporte = $param->subsidio_de_transporte;

        foreach ($users as $key => $value) {
            $users[$key]->cedula = $value->cedula;
            $users[$key]->Empleado = $value->nombre;

            $reportes = Reporte::where('user_id',$value->id)
                ->whereBetween('fecha_ini',[Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()]);
            $cargo = Cargo::find($value->cargo_id);
            // dd($cargo,$value);

            $salario = intval($reportes->sum('horas_trabajadas')) * doubleval($cargo->salario_hora);

            $users[$key]->salario = $salario;
            $salario_hora = $cargo->salario_hora;
            $horasExtras = intval($reportes->sum('diurnas')) * doubleval($salario_hora);
            $Ex_nocturnas = intval($reportes->sum('nocturnas')) * doubleval($salario_hora);
            $Ex_dominicales = intval($reportes->sum('dominicales')) * doubleval($salario_hora);
            $Ex_extra_diurnas = intval($reportes->sum('extra_diurnas')) * doubleval($salario_hora);
            $Ex_extra_nocturnas = intval($reportes->sum('extra_nocturnas')) * doubleval($salario_hora);
            $Ex_extra_dominicales = intval($reportes->sum('extra_dominicales')) * doubleval($salario_hora);
            
            $ExtraTotal = $horasExtras + $Ex_nocturnas
            + $Ex_dominicales + $Ex_extra_diurnas
            + $Ex_extra_nocturnas + $Ex_extra_dominicales;
            $users[$key]->Horas_Extras = $ExtraTotal;

            $users[$key]->Salud = 10;
            $users[$key]->Pension = 10;

            if ($salario >= $maxSalarioParametros) {
                
                $users[$key]->S_Transporte = 0;
            } else {
                $users[$key]->S_Transporte = $S_Transporte;
                
            }
            
            $users[$key]->Prima = 10;
            $users[$key]->Vacaciones = 1;
            $users[$key]->Cesantias = 2;
            $users[$key]->Intereses = 3;
            $users[$key]->Prestamo = 4;
            $users[$key]->Anticipo = 5;
            $users[$key]->Auxilio = 6;
            $users[$key]->Bonificacion = 7;
            $users[$key]->Reintegro = 8;
            $users[$key]->Abono_Prestamo = 9;
            $users[$key]->Otras_Deducciones = 10;
            $users[$key]->Total_pagado = $salario + $S_Transporte + $ExtraTotal;
        }
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
