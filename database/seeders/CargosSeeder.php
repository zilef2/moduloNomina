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
        Cargo::create([ 'nombre' => 'Servicios generales' ]);//1
        Cargo::create([ 'nombre' => 'Gerente General' ]);
        Cargo::create([ 'nombre' => 'Ingeniero de proyectos' ]);//3
        Cargo::create([ 'nombre' => 'Auxiliar de ingeniería' ]);
        Cargo::create([ 'nombre' => 'Supervisor de obra' ]);//5
        Cargo::create([ 'nombre' => 'Encargado de obra' ]);
        Cargo::create([ 'nombre' => 'Oficial electricista' ]);//7
        Cargo::create([ 'nombre' => 'Ayudante electricista' ]);
        Cargo::create([ 'nombre' => 'Jefe de compras' ]);//9
        Cargo::create([ 'nombre' => 'Jefe almacén y logística' ]);
        Cargo::create([ 'nombre' => 'Coordinadora SGI' ]);//11
        Cargo::create([ 'nombre' => 'Analista SST' ]);
        Cargo::create([ 'nombre' => 'Analista HSEQ' ]);//12
    }
}
