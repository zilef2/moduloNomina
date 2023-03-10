<?php

namespace Database\Seeders;

use App\Models\CentroCompra;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CentroCompraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CentroCompra::create([ 'nombre' => 'Centro de compra 1', ]);
        CentroCompra::create([ 'nombre' => 'Centro de compra 2', ]);
    }
}
