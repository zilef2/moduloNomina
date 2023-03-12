<?php

namespace Database\Seeders;

use App\Models\CentroCosto;
use Carbon\Carbon;
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
        CentroCosto::create([

            'nombre' => 'centro'.rand(10,10000),
        ]);

        // $operator = Project::create([ 'password'          => bcrypt('operator'), ]);
        // $operator->assignRole('operator');
    }
}
