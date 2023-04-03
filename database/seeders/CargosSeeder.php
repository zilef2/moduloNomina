<?php

namespace Database\Seeders;

use App\Models\Cargo;
use App\Models\Parametro;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CargosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Cargo::create([ 'nombre' => 'Servicios generales' , 'salario_hora' => 8000, 'salario_total'=> 1280000 ]);//1
        Cargo::create([ 'nombre' => 'Gerente General' , 'salario_hora' => 8000, 'salario_total'=> 1280000 ]);
        Cargo::create([ 'nombre' => 'Ingeniero de proyectos' , 'salario_hora' => 8000, 'salario_total'=> 1280000 ]);//3
        Cargo::create([ 'nombre' => 'Auxiliar de ingeniería' , 'salario_hora' => 8000, 'salario_total'=> 1280000 ]);
        Cargo::create([ 'nombre' => 'Supervisor de obra' , 'salario_hora' => 8000, 'salario_total'=> 1280000 ]);//5
        Cargo::create([ 'nombre' => 'Encargado de obra' , 'salario_hora' => 8000, 'salario_total'=> 1280000 ]);
        Cargo::create([ 'nombre' => 'Oficial electricista' , 'salario_hora' => 8000, 'salario_total'=> 1280000 ]);//7
        Cargo::create([ 'nombre' => 'Ayudante electricista' , 'salario_hora' => 8000, 'salario_total'=> 1280000 ]);
        Cargo::create([ 'nombre' => 'Jefe de compras' , 'salario_hora' => 8000, 'salario_total'=> 1280000 ]);//9
        Cargo::create([ 'nombre' => 'Jefe almacén y logística' , 'salario_hora' => 8000, 'salario_total'=> 0]);
        Cargo::create([ 'nombre' => 'Coordinadora SGI' , 'salario_hora' => 8000, 'salario_total'=> 1280000 ]);//11
        Cargo::create([ 'nombre' => 'Analista SST' , 'salario_hora' => 8000, 'salario_total'=> 1280000 ]);
        Cargo::create([ 'nombre' => 'Analista HSEQ' , 'salario_hora' => 8000, 'salario_total'=> 1280000 ]);//12
    }
}
