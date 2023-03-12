<?php

namespace Database\Seeders;

use App\Models\Reporte;
use Illuminate\Database\Seeder;

class ReporteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Reporte::create([
            'fecha_ini' => date("Y-m-d H:i:s"),
            'fecha_fin' => date("Y-m-d H:i:s"),

            'observaciones' => 'generico'.rand(12,543210),

            'centro_costo_id' => 1,
            'user_id' => 1,
        ]);
    }
}
