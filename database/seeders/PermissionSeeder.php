<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'isSuper']);
        Permission::create(['name' => 'isAdmin']);
        Permission::create(['name' => 'isValidador']);

        
        Permission::create(['name' => 'delete user']);
        Permission::create(['name' => 'update user']);
        Permission::create(['name' => 'read user']);
        Permission::create(['name' => 'create user']);

        Permission::create(['name' => 'delete role']);
        Permission::create(['name' => 'update role']);
        Permission::create(['name' => 'read role']);
        Permission::create(['name' => 'create role']);

        //reporte
        $vectorModelo = ['permission','reporte','centroCostos','parametros'];
        $vectorCRUD = ['create', 'update','read','delete'];
        foreach ($vectorCRUD as $value) {
            foreach ($vectorModelo as $model) {
                Permission::create(['name' => $value.' '.$model]);
            }
        }
        Permission::create(['name' => 'updateCorregido reporte']);

    }
}
