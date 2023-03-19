<?php

namespace Database\Seeders;

use App\Models\Reporte;
use Carbon\Carbon;
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
            'fecha_fin' => Carbon::tomorrow(),

            'observaciones' => 'observaciones genericas'.rand(12,543210),

            'centro_costo_id' => 1,
            'user_id' => 3,
        ]);
        Reporte::create([
            'fecha_ini' => Carbon::tomorrow(),
            'fecha_fin' => Carbon::tomorrow()->addDays(2),

            'observaciones' => 'observaciones genericas'.rand(12,543210),

            'centro_costo_id' => 2,
            'user_id' => 3,
        ]);
    }
}
