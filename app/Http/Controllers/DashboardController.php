<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function guardarCiudad(Request $r): void
    {
        if (Schema::hasTable('ubicacion')) {
            $user = User::find($r->userid);
            DB::table('ubicacion')->insert([
                'ubicacion' => $r->ciudad,
                'valido' => 1,
                'userid' => $r->userid,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => Carbon::now()
            ]);
        }
    }
}
