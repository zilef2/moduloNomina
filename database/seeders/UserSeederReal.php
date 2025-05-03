<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UserSeederReal extends Seeder {
	
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$sexos = ['Masculino', 'Femenino'];
		$rolesUser = [
			'Administrativo',
			'Supervisor',
			'Ingeniero',
			'Empleado',
		];
		
		$unUsuario = User::create(['name'              => 'Jessica Maria Perez Meza',
		                           'email'             => 'perezmezajessica@gmail.com',
		                           'password'          => \bcrypt('1193231624+-'),
		                           'email_verified_at' => date('Y-m-d H:i'),
		                           'fecha_de_ingreso'  => date('Y-m-d', strtotime('01/08/2023')),
		                           'cedula'            => '1193231624',
		                           'sexo'              => $sexos[1],
		                           'celular'           => '3012124273',
		                           'salario'           => 1600000,
		                           'cargo_id'          => 14, //Coordinadora de gestion humana
			                          // 'centro_costo_id' => 1,
		                          ]);
		$unUsuario->assignRole($rolesUser[0]);
		$unUsuario = User::create(['name'              => 'Michael Edison Cruz Herrera',
		                           'email'             => 'MichaelHerrera@example.com',
		                           'password'          => \bcrypt('123456777+-'),
		                           'email_verified_at' => date('Y-m-d H:i'),
		                           'fecha_de_ingreso'  => date('Y-m-d', strtotime('09/04/2025')),
		                           'cedula'            => '123456777',
		                           'sexo'              => $sexos[1],
		                           'celular'           => '3123123123',
		                           'salario'           => 16000000,
		                           'cargo_id'          => 1, //Coordinadora de gestion humana
		                           'centro_costo_id' => 1,
		                          ]);
		$unUsuario->assignRole($rolesUser[1]);
	}
}
