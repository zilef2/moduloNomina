<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
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
        $sexos = ['Masculino', 'Femenino'];
        $genPa = env('sap_gen');


        $superadmin = User::create([
            'name'              => 'Superadmin',
            'email'             => 'superadminmodnom@superadmin.com',
            'password'          => bcrypt($genPa.'superadmin00.+-*'.$genPa), 
            // 'password'          => bcrypt('superadmin0+-*/'),
            'email_verified_at' => date('Y-m-d H:i'),
            'cedula' => '11232454',
            'cargo_id' => 2
        ]);
        $superadmin->assignRole('superadmin');

        $nombreAdmin = 'Admin';
        $App = env('APP_NAME');
        $admin = User::create([
            'name'              => "$nombreAdmin $App",
            'email'             => "$nombreAdmin$App"."@gmail.com",
            'password'          => bcrypt($genPa.'**¿_?**'.$genPa),//1234_modnom**¿_?**1234_modnom
            'email_verified_at' => date('Y-m-d H:i'),
            'sexo' => $sexos[rand(0, 1)],
            'cedula' => '11232411',
            'cargo_id' => 2,
            
        ]);
        $admin->assignRole('admin');

        $empleado = User::create([
            'name'              => 'empleado',
            'email'             => 'empleado@empleado.com',
            'password'          => bcrypt('empleado1234+*'),
            'email_verified_at' => date('Y-m-d H:i'),
            'cedula' => '11232412',
            'cargo_id' => 2,
            'centro_costo_id' => 1,
        ]);
        $empleado->assignRole('empleado');

        $nombresGenericos = [
            'Elalejo' => '1153388999',
            'Alejo' => '123',
            'Jose' => '1152888999',
            'Anaya' => '1052566569',
        ];

        foreach ($nombresGenericos as $key => $value) {
            $yearRandom = (rand(5, 49));
            $anios = Carbon::now()->subYears($yearRandom)->format('Y-m-d H:i');
            $unUsuario = User::create([
                'name'              => $key,
                'email'             => $key . '@'.env('verifidedenv') . $key . '.com',
                'password'          => bcrypt($genPa.'qwe+-*'),
                'email_verified_at' => date('Y-m-d H:i'),
                'fecha_de_ingreso' => $anios,
                'cedula' => $value,
                'sexo' => 'masculino',
                'celular' => $value,
                'salario' => 1160000,
                'cargo_id' => 3,
                'centro_costo_id' => 1,
            ]);
            $unUsuario->assignRole('empleado');
        }


        //users real

        $unUsuario = User::create([
            'name'              => 'Jessica Maria Perez Meza',
            'email'             => 'perezmezajessica@gmail.com',
            'password'          => bcrypt('1193231624+-'),
            'email_verified_at' => date('Y-m-d H:i'),
            'fecha_de_ingreso' => date('Y-m-d',strtotime('01/08/2023')),
            'cedula' => '1193231624',
            'sexo' => 'femenino',
            'celular' => '3012124273',
            'salario' => 1600000,
            'cargo_id' => 14, //Coordinadora de gestion humana
            // 'centro_costo_id' => 1,
        ]);
        $unUsuario->assignRole('Administrativo');
    }
}