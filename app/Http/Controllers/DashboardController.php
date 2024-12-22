<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function guardarCiudad(Request $r): void
    {
        $user = Myhelp::AuthU();
        if (Schema::hasTable('ubicacion')) {
            DB::table('ubicacion')->insert([
                'ubicacion' => $r->ciudad,
                'valido' => 1,
                'userid' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => Carbon::now()
            ]);
        }
    }
}
