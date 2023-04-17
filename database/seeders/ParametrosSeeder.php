<?php

namespace Database\Seeders;

use App\Models\Parametro;
use Illuminate\Database\Seeder;

class ParametrosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Parametro::create([

            //cada que va a la obra (almenos 1 hora)
            'subsidio_de_transporte_dia' => 4686.86667,//140606, => divide por 30  ->  dia

            
            'salario_minimo' => 1300606,
                'valor_maximo_subsidio_de_transporte' => 2601212,
            'porcentaje_diurno' => 1,
            'porcentaje_nocturno' => 1.35,

            'porcentaje_extra_diurno' => 1.25,
            'porcentaje_extra_nocturno' => 1.75,

            'porcentaje_dominical_diurno' => 1.75,
            'porcentaje_dominical_nocturno' => 2.1,
            
            'porcentaje_dominical_extra_diurno' => 2,
            'porcentaje_dominical_extra_nocturno' => 2.5,

            //horario inicia diurno
            //horario finaliza diurno
        ]);
    }
}
