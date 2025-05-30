<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
	
	/**
	 * Seed the application's database.
	 *
	 * @return void
	 */
	public function run() {
        \App\Models\Permission::create(['name' => 'isIngeniero']);
		
		$this->call([CentroCostoSeeder::class,
			            CargosSeeder::class,
			            PermissionSeeder::class,
			            RoleSeeder::class,
			            UserSeeder::class,
			            UserSeederReal::class,
			            ReporteSeeder::class,
			            ParametrosSeeder::class,
			            RoleSeeder2025::class,
		            ]);
		
		\App\Models\User::factory(10)->create();
		
		\App\Models\User::factory()->create(['name'  => 'Test User',
		                                     'email' => 'test@example.com',
		                                    ]);
	}
}
