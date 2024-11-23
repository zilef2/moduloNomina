<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function guardarCiudad(Request $r)
    {
        if (Schema::hasTable('ubicacion')) {
            DB::table('ubicacion')->insert([
                'ubicacion' => $r->ciudad,
                'valido' => 1,
                'userid' => $r->userid,
            ]);
        }
    }
}
