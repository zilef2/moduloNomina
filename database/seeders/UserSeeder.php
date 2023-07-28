<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superadmin = User::create([
            'name'              => 'Superadmin',
            'email'             => 'superadmin@superadmin.com',
            'password'          => bcrypt('superadmin0+-*/'),
            'email_verified_at' => date('Y-m-d H:i'),
            'cargo_id' => 2
        ]);
        $superadmin->assignRole('superadmin');

        $admin = User::create([
            'name'              => 'Admin',
            'email'             => 'admin@admin.com',
            'password'          => bcrypt('alejoasd00+*??'),
            'email_verified_at' => date('Y-m-d H:i'),
        ]);
        $admin->assignRole('admin');

        $empleado = User::create([
            'name'              => 'empleado',
            'email'             => 'empleado@empleado.com',
            'password'          => bcrypt('empleado00+*'),
            'email_verified_at' => date('Y-m-d H:i')
        ]);
        $empleado->assignRole('empleado');

        $administrativo = User::create([
            'name'              => 'administrativo',
            'email'             => 'administrativo@administrativo.com',
            'password'          => bcrypt('administrativo00+*'),
            'email_verified_at' => date('Y-m-d H:i')
        ]);
        $administrativo->assignRole('administrativo');
    }
}
