<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superadmin = Role::create([
            'name'          => 'superadmin'
        ]);
        $superadmin->givePermissionTo([
            'isSuper',
            'isAdmin',

            'delete user',
            'update user',
            'read user',
            'create user',
            'delete role',
            'update role',
            'read role',
            'create role',
            'delete permission',
            'update permission',
            'read permission',
            'create permission',

            //reporte
            'updateCorregido reporte',

        ]);
        $vectorCRUD = ['create', 'update','read','delete'];
        $vectorModelo = ['reporte','centroCostos','parametros'];
        foreach ($vectorCRUD as $value) {
            foreach ($vectorModelo as $model) {
                $superadmin->givePermissionTo([ $value.' '.$model ]);
            }
        }

        $admin = Role::create([ 'name'=> 'admin' ]);
        $admin->givePermissionTo([
            'isAdmin',

            'delete user',
            'update user',
            'read user',
            'create user',
            'read role',
            'read permission',

            //reporte
            'read reporte',
            // 'create reporte',
            'update reporte',
            // 'delete reporte',
            'updateCorregido reporte',

            //centroCostos
        ]);

        $vectorCRUDAdmin = ['create', 'update','read'];
        $vectorModelo = ['centroCostos','parametros'];

        foreach ($vectorCRUDAdmin as $value) {
            foreach ($vectorModelo as $model) {
                $admin->givePermissionTo([ $value.' '.$model ]);
            }
        }

        $operator = Role::create([ 'name' => 'operator' ]);
        $operator->givePermissionTo([
            // 'read user',
            // 'create user',
            // 'read role',
            // 'read permission',

             //reporte
            'read reporte',
            'create reporte',
            'delete reporte',

            //centroCostos
            'read centroCostos',
        ]);

        $validador = Role::create(['name' => 'validador']);

        $validador->givePermissionTo([
            'read user',
            'create user',
            'read role',

             //reporte
             'read reporte',
             'update reporte',
             'updateCorregido reporte',
 
             //centroCostos
             'read centroCostos',
             'create centroCostos',
             'update centroCostos',
             'delete centroCostos',
        ]);
        // $role->revokePermissionTo($permission);
        // $permission->removeRole($role);
    }
}
