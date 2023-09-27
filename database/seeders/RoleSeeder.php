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
            'isadministrativo',

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
        foreach ($vectorCRUD as $value) { foreach ($vectorModelo as $model) { $superadmin->givePermissionTo([ $value.' '.$model ]); } }

        $admin = Role::create([ 'name'=> 'admin' ]);
        $admin->givePermissionTo([
            'isAdmin',
            'isadministrativo',

            'delete user',
            'update user',
            'read user',
            'create user',
            'read role',
            'read permission',

            //#reporte
            'read reporte',
            'create reporte',
            'update reporte',
            'delete reporte',
            'updateCorregido reporte',
        ]);
        
        $modelo = 'centroCostos';
        $acciones = ['create','update','read','delete'];
        foreach ($acciones as $accion) {
            $admin->givePermissionTo([ $accion.' '.$modelo]);
        }
        $modelo = 'parametros';
        $acciones = ['update','read'];
        foreach ($acciones as $accion) {
            $admin->givePermissionTo([ $accion.' '.$modelo]);
        }

        //no more admins

        $empleado = Role::create([ 'name' => 'empleado' ]);
        $empleado->givePermissionTo([
             //#reporte
            'read reporte',
            'create reporte',
            'delete reporte',

            //#centroCostos
            'read centroCostos',
        ]);

        $administrativo = Role::create(['name' => 'administrativo']);
        $administrativo->givePermissionTo([
            'isadministrativo',

             //#user
            'read user',
            'create user',
            // 'read role',
            'update user',

             //#reporte
             'read reporte',
             'updateCorregido reporte',
 
             //#centroCostos
             'update centroCostos',
             'read centroCostos',

             //#parametros
             'read parametros',
        ]);
        $supervisor = Role::create(['name' => 'supervisor']);
        $supervisor->givePermissionTo([
            'issupervisor',

             //#user
            'read user',
            // 'create user',
            // 'read role',

             //#reporte
             'read reporte',
             'create reporte',
             'update reporte',
             'updateCorregido reporte',
 
             //#centroCostos
             'update centroCostos',
             'read centroCostos',

             //#parametros
            //  'read parametros',
        ]);




        // $role->revokePermissionTo($permission);
        // $permission->removeRole($role);
    }
}
