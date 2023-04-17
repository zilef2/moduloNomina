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
            'password'          => bcrypt('adminasd00+*??'),
            'email_verified_at' => date('Y-m-d H:i'),
        ]);
        $admin->assignRole('admin');

        $operator = User::create([
            'name'              => 'Operator',
            'email'             => 'operator@operator.com',
            'password'          => bcrypt('operator00+*'),
            'email_verified_at' => date('Y-m-d H:i')
        ]);
        $operator->assignRole('operator');

        $validador = User::create([
            'name'              => 'validador',
            'email'             => 'validador@validador.com',
            'password'          => bcrypt('validador00+*'),
            'email_verified_at' => date('Y-m-d H:i')
        ]);
        $validador->assignRole('validador');
    }
}
