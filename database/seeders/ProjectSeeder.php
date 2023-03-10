<?php

namespace Database\Seeders;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Project::create([
            'nombre' => 'tomaTiemposCarlos',
            'cliente' => 'CarlosAyana',
            'num_modulos' => 2,
            'valor_tentativo' => '1800000',
            'valor_acordado' => '1800000',
            'valor_primer_pago' => '450000',
            'fecha_primera_reunion' => date('Y-m-d H:i'),
            'fecha_primer_pago' => Carbon::now(),
            'fecha_entrega' => Carbon::now()->addMonth(1),
            'observaciones' => 'Primero hay que ver que les vendan un software para la parte economica',
        ]);

        // $operator = Project::create([ 'password'          => bcrypt('operator'), ]);
        // $operator->assignRole('operator');
    }
}
