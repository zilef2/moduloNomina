<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder2025 extends Seeder {
	
	/**
	 * Run the database seeds.
	 *
	 * php artisan db:seed --class=RoleSeeder2025
	 * @return void
	 */
	public function run(): void {
		$vectorModelo = ['cotizacion', 'viatico'];
		$vectorCRUD = ['create', 'update', 'read', 'delete', 'update2'];
		foreach ($vectorCRUD as $value) {
			foreach ($vectorModelo as $model) {
				Permission::create(['name' => $value . ' ' . $model]);
			}
		}
		Permission::create(['name' => 'update3 viatico']);
		Permission::create(['name' => 'update3 cotizacion']);
		
		$superadmin = Role::Where(['name' => 'superadmin'])->first();
		$admin = Role::Where(['name' => 'admin'])->first();
		foreach ($vectorCRUD as $value) {
			foreach ($vectorModelo as $model) {
				$superadmin->givePermissionTo([$value
				                               . ' ' . $model]);
			}
		}
		foreach ($vectorCRUD as $value) {
			foreach ($vectorModelo as $model) {
				$admin->givePermissionTo([$value
				                          . ' ' . $model]);
			}
		}
		
		//no more admins
		$empleado = Role::Where(['name' => 'empleado'])->first();
		$empleado->givePermissionTo([]);
		
		$administrativo = Role::Where(['name' => 'administrativo'])->first();
		$administrativo->givePermissionTo([
			                                  //#cotizacion
			                                  'create cotizacion',
			                                  'read cotizacion',
			                                  //            'update cotizacion',
			                                  
			                                  //#viatico
			                                  'create viatico',
			                                  'read viatico',
			                                  //            'update viatico',
			                                  //            'update2 viatico',//solo el admin
			                                  'update3 viatico',//validar el saldo
		                                  ]);
		
		$administrativo = Role::Where(['name' => 'ingeniero'])->first();
		$administrativo->givePermissionTo([
			                                  //#cotizacion
			                                  'create cotizacion',
			                                  'read cotizacion',
			                                  
			                                  //#viatico
			                                  'create viatico',
			                                  'read viatico',
		                                  ]);
		$supervisor = Role::Where(['name' => 'supervisor'])->first();
		$supervisor->givePermissionTo([
			                              //#cotizacion
			                              'create cotizacion',
			                              'read cotizacion',
			                              
			                              //#viatico
			                              'create viatico',
			                              'read viatico',
		                              ]);
		
		// $role->revokePermissionTo($permission);
		// $permission->removeRole($role);
	}
}
