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
            'password'          => bcrypt($genPa.'super0.+-*'.$genPa),
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
            'password'          => bcrypt($genPa.'0.+-*'.$genPa),
            'email_verified_at' => date('Y-m-d H:i'),
            'sexo' => $sexos[rand(0, 1)],
            'cedula' => '11232411',
            'cargo_id' => 2
            
        ]);
        $admin->assignRole('admin');

        $empleado = User::create([
            'name'              => 'empleado',
            'email'             => 'empleado@empleado.com',
            'password'          => bcrypt('empleado00+*'),
            'email_verified_at' => date('Y-m-d H:i'),
            'cedula' => '11232412',
            'cargo_id' => 2
        ]);
        $empleado->assignRole('empleado');

        $nombresGenericos = [
            'El alejo' => '1153388999',
            'jose' => '1152888999',
            'milena' => '1052133567',
            'carlos' => '1053333568',
            'anaya' => '1052566569',
            'Emerson' => '333444667',
        ];

        foreach ($nombresGenericos as $key => $value) {
            $yearRandom = (rand(5, 49));
            $anios = Carbon::now()->subYears($yearRandom)->format('Y-m-d H:i');
            $unUsuario = User::create([
                'name'              => $key,
                'email'             => $key . '@'.env('verifidedenv') . $key . '.com',
                'password'          => bcrypt($genPa.'asd+-*'),
                'email_verified_at' => date('Y-m-d H:i'),
                'fecha_de_ingreso' => $anios,
                'cedula' => $value,
                'celular' => $value,
                'cargo_id' => 3,
            ]);
            $unUsuario->assignRole('empleado');
        }
    }
}

        
        
//         $superadmin = User::create([
//             'name'              => 'Superadmin',
//             'email'             => 'superadmin@superadmin.com',
//             'password'          => bcrypt('superadmin0+-*/'),
//             'email_verified_at' => date('Y-m-d H:i'),
//             'cargo_id' => 2
//         ]);
//         $superadmin->assignRole('superadmin');

//         $admin = User::create([
//             'name'              => 'Admin',
//             'email'             => 'admin@admin.com',
//             'password'          => bcrypt('alejoasd00+*??'),
//             'email_verified_at' => date('Y-m-d H:i'),
//         ]);
//         $admin->assignRole('admin');

//         $empleado = User::create([
//             'name'              => 'empleado',
//             'email'             => 'empleado@empleado.com',
//             'password'          => bcrypt('empleado00+*'),
//             'email_verified_at' => date('Y-m-d H:i')
//         ]);
//         $empleado->assignRole('empleado');

//         $administrativo = User::create([
//             'name'              => 'administrativo',
//             'email'             => 'administrativo@administrativo.com',
//             'password'          => bcrypt('administrativo00+*'),
//             'email_verified_at' => date('Y-m-d H:i')
//         ]);
//         $administrativo->assignRole('administrativo');
//     }
// }
