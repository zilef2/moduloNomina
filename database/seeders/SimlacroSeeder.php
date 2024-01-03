<?php

namespace Database\Seeders;

use App\Models\Reporte;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SimlacroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        //alejo5   jose6
        $ini = Carbon::today()->firstOfMonth()->setTime(7, 0, 0);
        $fin = Carbon::today()->firstOfMonth()->setTime(15, 0, 0);
        $fin2 = Carbon::today()->firstOfMonth()->setTime(16, 0, 0);
        for ($i = 1; $i <= 15; $i++) {
            // $vector[] = $i;
            if($i == 1 || $i == 8 || $i == 15){
                $ini->addDay();
                $fin->addDay();
                continue;
            }else{
            
                Reporte::create([
                    'fecha_ini' => $ini,
                    'fecha_fin' => $fin,
                    'horas_trabajadas' => 8,
                    'almuerzo' => 1,
                    'diurnas' => 8,
                    'nocturnas' => 0,
                    'extra_diurnas' => 0,
                    'valido' => 1,//admit null
                    'centro_costo_id' => 1,
                    'user_id' => 5,
                ]);
                Reporte::create([
                    'fecha_ini' => $ini,
                    'fecha_fin' => $fin2,
                    'horas_trabajadas' => 9,
                    'almuerzo' => 1,
                    'diurnas' => 8,
                    'nocturnas' => 0,
                    'extra_diurnas' => 1,
                    'valido' => 1,//admit null
                    'centro_costo_id' => 1,
                    'user_id' => 6,
                ]);
                $ini->addDay();
                $fin->addDay();
            }

        }

    }
}
// php artisan db:seed --class=SimlacroSeeder

// INSERT INTO `reportes` (`id`, `fecha_ini`, `fecha_fin`, `horas_trabajadas`, `almuerzo`, `diurnas`, `nocturnas`, `extra_diurnas`, `extra_nocturnas`, `dominical_diurno`, `dominical_nocturno`, `dominical_extra_diurno`, `dominical_extra_nocturno`, `valido`, `observaciones`, `created_at`, `updated_at`, `centro_costo_id`, `user_id`, `deleted_at`) VALUES (NULL, '2023-10-17 01:53:39.000000', '2023-10-17 01:53:39.000000', '9', '1', '9', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, '1', '5', NULL);