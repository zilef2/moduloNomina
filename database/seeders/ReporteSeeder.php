<?php

namespace Database\Seeders;

use App\Models\Reporte;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            // 'fecha' => '29/03/2023',
            'hora_inicio' => date("Y-m-d H:i:s"),
            'hora_fin' => date("Y-m-d H:i:s"),
            'observaciones' => '',
            'centro_compra_id' => 1,
        ]);
        Reporte::create([
            // 'fecha' => '29/03/2023',
            'hora_inicio' => date("Y-m-d H:i:s"),
            'hora_fin' => Carbon::tomorrow(),
            'observaciones' => '',
            'centro_compra_id' => 2,
        ]);

    }
}
