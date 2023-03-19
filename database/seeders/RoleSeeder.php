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
            'create reporte',
            'delete reporte',
            'update reporte',
            'read reporte',
            'updateCorregido reporte',

            //centroCostos
            'create centroCostos',
            'delete centroCostos',
            'update centroCostos',
            'read centroCostos',
        ]);
        $admin = Role::create([
            'name'          => 'admin'
        ]);
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

            //centroCostos
            'read centroCostos',
            'create centroCostos',
            'update centroCostos',
            'delete centroCostos',
        ]);

        $operator = Role::create([ 'name'          => 'operator' ]);
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
