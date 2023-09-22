<?php

namespace Database\Seeders;

use App\Models\CentroCosto;
use Illuminate\Database\Seeder;

class CentroCostoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CentroCosto::create([ 'nombre' => 'centro '.rand(1,10), ]);
        CentroCosto::create([ 'nombre' => 'Unicentro']);
        // CentroCosto::create([ 'nombre' => 'centro'.rand(10001,20000), ]);

    }
}
