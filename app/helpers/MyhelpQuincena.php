<?php

namespace App\helpers;

use App\Models\Reporte;
use Illuminate\Support\Facades\Auth;

class MyhelpQuincena{



    public static function CalcularTituloQuincena($permissions) {
        $esteMes = date("m");
        $diaquincena = date("d");
        if($permissions === "empleado") { //NO admin | administrativo
            $userid = Auth::user()->id;
            if($diaquincena <= 15){
                $horasTrabajadas = Reporte::WhereMonth('fecha_ini',$esteMes)->WhereDay('fecha_ini','<=',15)
                    ->where('user_id',$userid)
                    ->sum('horas_trabajadas');
            }else{
                $horasTrabajadas = Reporte::WhereMonth('fecha_ini',$esteMes)->WhereDay('fecha_ini','>',15)
                    ->where('user_id',$userid)
                    ->sum('horas_trabajadas');
            }
        }else{
            if($diaquincena <= 15){
                $horasTrabajadas = Reporte::WhereMonth('fecha_ini',$esteMes)->WhereDay('fecha_ini','<=',15)
                    ->sum('horas_trabajadas');
            }else{
                $horasTrabajadas = Reporte::WhereMonth('fecha_ini',$esteMes)->WhereDay('fecha_ini','>',15)
                    ->sum('horas_trabajadas');
            }
        }

        return 'Horas trabajadas quincena: '.$horasTrabajadas;
    }


} ?>
