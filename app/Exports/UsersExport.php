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
    public $ini, $fin, $NumeroDiasFestivos,$ArrayDatesFest;
    public function __construct($ini, $fin, $NumeroDiasFestivos,$ArrayDatesFest) {
        $this->ini = $ini;
        $this->fin = $fin;
        $this->NumeroDiasFestivos = $NumeroDiasFestivos;
        $this->ArrayDatesFest = $ArrayDatesFest;
    }

    //this function returns hours (the money is &$variables)
    public function CalculoHorasExtrasDominicalesTodo($reportes, $cumplioQuicena, $salario_hora, $paramBD, &$H_diurno, &$nocturnas, &$extra_diurnas, &$extra_nocturnas, &$dominical_diurno, &$dominical_nocturno, &$dominical_extra_diurno, &$dominical_extra_nocturno) {
        $H_diurno = round((int)($reportes->sum('diurnas')) * (double)($salario_hora));

        $recargoNocturno = $paramBD->porcentaje_nocturno;
        // $recargoNocturno = $cumplioQuicena ? $paramBD->porcentaje_nocturno - 1 : $paramBD->porcentaje_nocturno;
        $nocturnas = (int)($reportes->sum('nocturnas')) * (double)($salario_hora) * ($recargoNocturno - 1);
        // extras simples
        $extra_diurnas = (int)($reportes->sum('extra_diurnas')) * (double)($salario_hora) * $paramBD->porcentaje_extra_diurno;
        $extra_nocturnas = (int)($reportes->sum('extra_nocturnas')) * (double)($salario_hora) * $paramBD->porcentaje_extra_nocturno;
        // dominicales
        $dominical_diurno = (int)($reportes->sum('dominical_diurno')) * (double)($salario_hora) * $paramBD->porcentaje_dominical_diurno;
        $dominical_nocturno = (int)($reportes->sum('dominical_nocturno')) * (double)($salario_hora) * $paramBD->porcentaje_dominical_nocturno;
        // dominicales extras
        $dominical_extra_diurno = (int)($reportes->sum('dominical_extra_diurno')) * (double)($salario_hora) * $paramBD->porcentaje_dominical_extra_diurno;
        $dominical_extra_nocturno = (int)($reportes->sum('dominical_extra_nocturno')) * (double)($salario_hora) * $paramBD->porcentaje_dominical_extra_nocturno;

        return [
            (int)($reportes->sum('extra_diurnas')) +
            (int)($reportes->sum('extra_nocturnas')) +
            (int)($reportes->sum('dominical_diurno')) +
            (int)($reportes->sum('dominical_nocturno')) +
            (int)($reportes->sum('dominical_extra_diurno')) +
            (int)($reportes->sum('dominical_extra_nocturno')),

            (int)($reportes->sum('extra_diurnas')),
            (int)($reportes->sum('extra_nocturnas')),
            (int)($reportes->sum('dominical_diurno')),
            (int)($reportes->sum('dominical_nocturno')),
            (int)($reportes->sum('dominical_extra_diurno')),
            (int)($reportes->sum('dominical_extra_nocturno'))
        ];
    }


    public function unsetAllunnesesary(&$users, $keyp) {
        $unecesary = ['id', 'name', 'cargo_id', 'salario'];
        foreach ($unecesary as $value) {
            unset($users[$keyp]->{$value});
        }
    }

    /**/
    public function CalculoDomingoGanadosTodo($empleado) {
        $HORAS_NECESARIAS_SEMANA = (int)(Parametro::find(1)->HORAS_NECESARIAS_SEMANA);
        $HORAS_NECESARIAS_QUINCENA = $HORAS_NECESARIAS_SEMANA * 2;

        $reportes = Reporte::Where('user_id', $empleado->id)
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
            $ValidarReportes = Reporte::Where('user_id', $empleado->id)
                ->where('valido', 1)
                ->whereBetween('fecha_ini', [$ElLunes, $ElSabado])
                ->sum('horas_trabajadas');

            //? NOTE:: values 15-30
            $HORAS_NECESARIAS_SEMANA -= $this->NumeroDiasFestivos * 8;
            $domingosGanados += (int)($ValidarReportes) >= ($HORAS_NECESARIAS_SEMANA) ? 1 : 0;
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
        $H_diurno = 0;$nocturnas = 0;$extra_diurnas = 0;$extra_nocturnas = 0;$dominical_diurno = 0;$dominical_nocturno = 0;$dominical_extra_diurno = 0;$dominical_extra_nocturno = 0;
        $paramBD = Parametro::find(1);
        //traer todos los empleado
        $usersEmpleados = User::Select('id','name','numero_contrato', 'cedula', 'cargo_id', 'salario')->WhereHas("roles", function ($q) {
            $q->Where("name", "empleado");
            $q->orWhere("name", "supervisor");
        })->get();


        foreach ($usersEmpleados as $key => $value) {
            $DiasTrabajados = HelpExcel::cumplioQuincena(
                $usersEmpleados, $key, $this->ini, $this->fin,
                $value, $reportes, $salario_hora,
                $salario_quincena, $cumplioQuicena, $paramBD,
                $this->NumeroDiasFestivos,$this->ArrayDatesFest
            );

            //todo: debugging
//            if(substr($usersEmpleados[$key]->Completa,0,3) === 'Si '){
//                dd(
//                    substr($usersEmpleados[$key]->Completa,0,2),
//                    ($usersEmpleados[$key])
//                );
//            }
            $HorasExtrasyDominicales = $this->CalculoHorasExtrasDominicalesTodo($reportes, $cumplioQuicena, $salario_hora, $paramBD, $H_diurno, $nocturnas, $extra_diurnas, $extra_nocturnas, $dominical_diurno, $dominical_nocturno, $dominical_extra_diurno, $dominical_extra_nocturno);

//            if ($cumplioQuicena) { // cumplio con los dias de la quincena
                $usersEmpleados[$key]->Salario = $salario_quincena;
//            } else {
//                $usersEmpleados[$key]->Salario = $H_diurno;
//            }


//            $domingosGanados = $this->CalculoDomingoGanadosTodo($value);
//            $DiasTrabajados += $domingosGanados; //se usa para el subsidio de transporte

            $usersEmpleados[$key]->SalarioHora = $salario_hora;
            $usersEmpleados[$key]->diurnas = (int)($reportes->sum('diurnas'));$usersEmpleados[$key]->nocturnas = (int)($reportes->sum('nocturnas'));$usersEmpleados[$key]->extra_diurnas = $HorasExtrasyDominicales[1] .' = ' .$extra_diurnas;$usersEmpleados[$key]->extra_nocturnas = $HorasExtrasyDominicales[2].' = ' .$extra_nocturnas;$usersEmpleados[$key]->dominical_diurno = $HorasExtrasyDominicales[3].' = ' .$dominical_diurno;$usersEmpleados[$key]->dominical_nocturno = $HorasExtrasyDominicales[4].' = ' .$dominical_nocturno;$usersEmpleados[$key]->dominical_extra_diurno = $HorasExtrasyDominicales[5].' = ' .$dominical_extra_diurno;$usersEmpleados[$key]->dominical_extra_nocturno = $HorasExtrasyDominicales[6].' = ' .$dominical_extra_nocturno;$usersEmpleados[$key]->extrasYDominicales = $HorasExtrasyDominicales[0];

            $Total_Horas = $usersEmpleados[$key]->diurnas + $usersEmpleados[$key]->nocturnas + $usersEmpleados[$key]->extrasYDominicales;
            $usersEmpleados[$key]->Total_Horas = $Total_Horas;

            //! borrando atributos que no se necesitan en el reporte
            $this->unsetAllunnesesary($usersEmpleados, $key);

            //!De aqui, empieza a calcular dinero. Hacia arriba solo son horas
            $ExtraTotal = $extra_diurnas + $extra_nocturnas + $dominical_diurno + $dominical_nocturno + $dominical_extra_diurno + $dominical_extra_nocturno;
            $usersEmpleados[$key]->RecargoNocturno = $nocturnas;//this is money
            $usersEmpleados[$key]->Valor_Horas_Extras = $ExtraTotal;//this is money

            // $salYextras es la variable que tiene todas las horas, diurnas, noc, extras,dominicales
            //# SALUD Y PENSION
//            $salYextras = ($H_diurno + $nocturnas + $ExtraTotal);
            if ($Total_Horas != 0) {
                 $salYextras = $usersEmpleados[$key]->Salario + $nocturnas + $ExtraTotal;
                $saludPension = round($salYextras * 0.04);
                $usersEmpleados[$key]->Salud = $saludPension;
                $usersEmpleados[$key]->Pension = $saludPension;

                $S_Transporte = ($usersEmpleados[$key]->Salario * 2) >= ($paramBD->valor_maximo_subsidio_de_transporte) ?
                    0 : 15 * $paramBD->subsidio_de_transporte_dia;

                $usersEmpleados[$key]->S_Transporte = round($S_Transporte);
                // # Total
//            $usersEmpleados[$key]->salYextras = $usersEmpleados[$key]->Salario + $ExtraTotal;
                $usersEmpleados[$key]->SalarioNocExtras = $salYextras;
                $usersEmpleados[$key]->Total_pagado = round(($salYextras + $S_Transporte) - (2 * $saludPension));
            }else { //no cumplio la quincena
                $usersEmpleados[$key]->Salud = 0;
                $usersEmpleados[$key]->Pension = 0;
                $usersEmpleados[$key]->S_Transporte = 0;
                // # Total
                $usersEmpleados[$key]->SalarioNocExtras = 0;
                $usersEmpleados[$key]->Total_pagado = 0;
            }
//            }else{ //no cumplio la quincena
//                // $salYextras = ($H_diurno + $ExtraTotal);
//                $saludPension = 0;
//                $usersEmpleados[$key]->Salud = 0;
//                $usersEmpleados[$key]->Pension = 0;
//                //# Subsidio de transporte (por dias)
//                $S_Transporte = ($usersEmpleados[$key]->Salario * 2) >= ($paramBD->valor_maximo_subsidio_de_transporte) ? 0 : $DiasTrabajados * $paramBD->subsidio_de_transporte_dia;
//            }

            // # Novedades
            // $usersEmpleados[$key]->Prima = '0'; $usersEmpleados[$key]->Vacaciones = '0'; $usersEmpleados[$key]->Cesantias = '0'; $usersEmpleados[$key]->Intereses = '0'; $usersEmpleados[$key]->Prestamo = '0'; $usersEmpleados[$key]->Anticipo = '0'; $usersEmpleados[$key]->Auxilio = '0'; $usersEmpleados[$key]->Bonificacion = '0'; $usersEmpleados[$key]->Reintegro = '0'; $usersEmpleados[$key]->Abono_Prestamo = '0'; $usersEmpleados[$key]->Otras_Deducciones = '0';
        }

        //<editor-fold desc="administrativos e ingenieros no ganan extras">
        //WhereNotIn('id',[2,1])
        $usersAdministrativos = User::Select('id', 'name','numero_contrato', 'cedula', 'cargo_id', 'salario')->WhereNotIn('id',[2,1])
            ->WhereHas("roles", function ($q) {
                $q->Where("name", "administrativo");
                $q->orWhere("name", "ingeniero");
            })->get();
        $key++;

        foreach ($usersAdministrativos as $key2 => $user) {
            $usersEmpleados[$key2+$key] = $user;

            $salario = $user->salario/2;
            $usersEmpleados[$key2+$key]->Completa = 'Administrativo o ingeniero';
            $usersEmpleados[$key2+$key]->Num = 0;
            $usersEmpleados[$key2+$key]->Empleado = $user->name;
            $this->unsetAllunnesesary($usersAdministrativos, ($key2));
            $transporte = round($paramBD->subsidio_de_transporte_dia*15);
            $usersEmpleados[$key2+$key]->Salario = $salario;
            $salario_hora = $salario / (235);// 30 * 7.8333
            $usersEmpleados[$key2+$key]->SalarioHora = $salario_hora;
            $usersEmpleados[$key2+$key]->diurnas = 0;$usersEmpleados[$key2+$key]->nocturnas = 0;$usersEmpleados[$key2+$key]->extra_diurnas = 0; $usersEmpleados[$key2+$key]->extra_nocturnas = 0; $usersEmpleados[$key2+$key]->dominical_diurno = 0; $usersEmpleados[$key2+$key]->dominical_nocturno = 0; $usersEmpleados[$key2+$key]->dominical_extra_diurno = 0; $usersEmpleados[$key2+$key]->dominical_extra_nocturno = 0;
            $usersEmpleados[$key2+$key]->extrasYDominicales = 0;
//            $usersEmpleados[$key2+$key]->DerechoDomingo = 0;
            $usersEmpleados[$key2+$key]->Total_Horas = 0;
            $usersEmpleados[$key2+$key]->Valor_Horas_Extras = 0;

            $saludPension = round($salario * 0.04);
            $usersEmpleados[$key2+$key]->Salud = $saludPension;
            $usersEmpleados[$key2+$key]->Pension = $saludPension;
            $usersEmpleados[$key2+$key]->S_Transporte = $transporte;
            $usersEmpleados[$key2+$key]->RecargoNocturno = $nocturnas;//this is money
            $usersEmpleados[$key2+$key]->SalarioNocExtras = $salario;
            $usersEmpleados[$key2+$key]->Total_pagado = round((($salario + $transporte) - (2 * $saludPension)));
        }
        //</editor-fold>

        //# prod =>  5:jessica 3:jose
//        $resetKeysArray = array_values($usersEmpleados->toArray());
//        $secondElement = $resetKeysArray[1] ?? '';
//        $s3econdElement = $resetKeysArray[2] ?? '';
//        $s4econdElement = $resetKeysArray[3] ?? '';
//        $s5econdElement = $resetKeysArray[4] ?? '';
//        $s6econdElement = $resetKeysArray[6] ?? '';
//        $s7econdElement = $resetKeysArray[8] ?? '';
//        $s8econdElement = $resetKeysArray[10] ?? '';
//        $s9econdElement = $resetKeysArray[12] ?? '';
//        $s10econdElement = $resetKeysArray[14] ?? '';
//        dd(
//            $secondElement,
//            $s3econdElement,
//            $s4econdElement,
//            $s5econdElement,
//            $s6econdElement,
//            $s7econdElement,
//            $s8econdElement,
//            $s9econdElement,
//            $s10econdElement,
//        );
        return $usersEmpleados;
//        return array_merge($usersEmpleados, $usersAdministrativos);
    }

    public function headings(): array {
        return [
            // 'Año',
            // 'Quincena',
            'Numero contrato',
            'Cedula',
            'Quincena Completa',
            'Num reportes',
            'Empleado',

            'Salario (Quincena)',
            'Salario (Hora)',
            'diurnas', 'nocturnas',
            'extra diurnas', 'extra nocturnas', 'dominical diurno', 'dominical nocturno', 'dominical extra diurno', 'dominical extra nocturno',
            'extrasYDominicales',
//            'Domingos',
            'Total Horas',

            'Recargo nocturno',
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
            'Salario Noc extras',
            'Total pagado',
        ];
    }
}
