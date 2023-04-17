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

    public function CalculoHorasExtrasDominicalesTodo($reportes,$salario_hora,$param, &$diurno, &$nocturnas, &$extra_diurnas, &$extra_nocturnas, &$dominical_diurno, &$dominical_nocturno, &$dominical_extra_diurno, &$dominical_extra_nocturno){
        $diurno =  intval($reportes->sum('diurnas')) * doubleval($salario_hora);
        $nocturnas = intval($reportes->sum('nocturnas')) * doubleval($salario_hora) * $param->porcentaje_nocturno;
        $extra_diurnas = intval($reportes->sum('extra_diurnas')) * doubleval($salario_hora) * $param->porcentaje_extra_diurno;
        $extra_nocturnas = intval($reportes->sum('extra_nocturnas')) * doubleval($salario_hora) * $param->porcentaje_extra_nocturno;
        $dominical_diurno = intval($reportes->sum('dominical_diurno')) * doubleval($salario_hora) * $param->porcentaje_dominical_diurno;
        $dominical_nocturno = intval($reportes->sum('dominical_nocturno')) * doubleval($salario_hora) * $param->porcentaje_dominical_nocturno;
        $dominical_extra_diurno = intval($reportes->sum('dominical_extra_diurno')) * doubleval($salario_hora) * $param->porcentaje_dominical_extra_diurno;
        $dominical_extra_nocturno = intval($reportes->sum('dominical_extra_nocturno')) * doubleval($salario_hora) * $param->porcentaje_dominical_extra_nocturno;
    }

    public function unsetAllunnesesary(&$users,$key){
        $unecesary = ['id','name','cargo_id'];
        foreach ($unecesary as $value) {
            unset($users[$key]->{$value});
        }
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {
        $diurno = 0;
        $nocturnas = 0;
        $extra_diurnas = 0;
        $extra_nocturnas = 0;
        $dominical_diurno = 0;
        $dominical_nocturno = 0;
        $dominical_extra_diurno = 0;
        $dominical_extra_nocturno = 0;

        $param = Parametro::find(1);
        
        
        //traer todos los operator
        $users = User::Select('id','name','cedula','cargo_id')->WhereHas("roles", function($q){
            $q->Where("name", "operator");
        })->get();

        foreach ($users as $key => $value) {
            $reportes = Reporte::where('user_id',$value->id)
            ->where('valido',1)
            ->whereBetween('fecha_ini',[$this->ini,$this->fin]); //toerase

            $users[$key]->Empleado = $value->name;
            
            
            $salario_hora = Cargo::find($value->cargo_id)->salario_hora;
            $this->CalculoHorasExtrasDominicalesTodo($reportes, $salario_hora,$param, $diurno, $nocturnas, $extra_diurnas, $extra_nocturnas, $dominical_diurno, $dominical_nocturno, $dominical_extra_diurno, $dominical_extra_nocturno );
            $this->unsetAllunnesesary($users,$key);

            $users[$key]->salario = $diurno;
            
            $ExtraTotal = $nocturnas + $extra_diurnas + $extra_nocturnas + $dominical_diurno + $dominical_nocturno + $dominical_extra_diurno + $dominical_extra_nocturno;
            $users[$key]->Horas_Extras = $ExtraTotal;
            $salYextras = $diurno + $ExtraTotal;

            $saludPension = round($salYextras * 0.04, 0, PHP_ROUND_HALF_UP); //QUEMADO: salud y la pension = salario total * 4%
            $users[$key]->Salud = $saludPension;
            $users[$key]->Pension = $saludPension;

            $S_Transporte = $diurno >= ($param->valor_maximo_subsidio_de_transporte) ? 0 : $reportes->count() * $param->subsidio_de_transporte_dia;
            $users[$key]->S_Transporte = $S_Transporte;
            
            $users[$key]->Prima = '0'; $users[$key]->Vacaciones = '0'; $users[$key]->Cesantias = '0'; $users[$key]->Intereses = '0'; $users[$key]->Prestamo = '0'; $users[$key]->Anticipo = '0'; $users[$key]->Auxilio = '0'; $users[$key]->Bonificacion = '0'; $users[$key]->Reintegro = '0'; $users[$key]->Abono_Prestamo = '0'; $users[$key]->Otras_Deducciones = '0';

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
