<?php

namespace Database\Seeders;

use App\Models\Cargo;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeederIngeniero extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $superadmin = Role::Where('name','superadmin')->first();
        $superadmin->givePermissionTo([
            'isIngeniero',
        ]);

        $inge = Role::create(['name' => 'ingeniero']);
        $inge->givePermissionTo([
            'isIngeniero',
            //#user
            'read user',

            //#reporte
            'read reporte',
            // 'create reporte',
            'update reporte',
            'delete reporte',
            'updateCorregido reporte',

            //#centroCostos
            'read centroCostos',
            'update centroCostos',
        ]);

        $unUsuario = User::create([
            'name'              => 'Empleado Pruebas',
            'email'             => 'EmpleadoPruebas@gmail.com',
            'password'          => bcrypt('hola+-'),
            'email_verified_at' => date('Y-m-d H:i'),
            'fecha_de_ingreso' => date('Y-m-d',strtotime('01/08/2023')),
            'cedula' => '3332221111',
            'sexo' => 'femenino',
            'celular' => '3012124273',
            'salario' => 1600000,
            'cargo_id' => 14, //Coordinadora de gestion humana
            // 'centro_costo_id' => 1,
        ]);
        $unUsuario->assignRole('empleado');


        Cargo::create([ 'nombre' => 'Asistente Administrativa' , 'salario_hora' => 8000, 'salario_total'=> 1280000 ]);//1
        Cargo::create([ 'nombre' => 'Coordinadora Administrativa' , 'salario_hora' => 8000, 'salario_total'=> 1280000 ]);//1
        Cargo::create([ 'nombre' => 'Coordinadora SST' , 'salario_hora' => 8000, 'salario_total'=> 1280000 ]);//1
    }
    //php artisan db:seed --class=RoleSeederIngeniero
}
