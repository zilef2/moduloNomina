<?php

namespace App\Imports;

use App\helpers\HelpExcel;
use App\helpers\Myhelp;
use App\Models\Cargo;
use App\Models\User;
use App\Rules\CustomEmailRule;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Contracts\Queue\ShouldQueue;

class UsersImport implements ToModel,WithChunkReading,ShouldQueue, WithValidation, WithHeadingRow {
    use Importable;
    public function rules(): array {
        return [
            'correo'  => 'min:4',
            'usuario' => 'min:2'
        ];
    }
    public function customValidationMessages() {
        return [
            'correo' => 'Correo invalido. Fila :attribute.',
            'usuario' => 'Nombre corto. Fila :attribute.',
        ];
    }
    public function chunkSize(): int { return 11000; }

    public function model(array $row) {
        $countfilas = session('CountFilas',0);
        $countNoleidos = session('countNoleidos',0);
        $countSex = session('countSex',0);
        $countNoCargo = session('countNoCargo',0);
        $countCedulaRepetida = session('countCedulaRepetida',0);

        if(!isset($row['nombre'])) {
            return null;
        }

        if (strtolower(trim($row['nombre'])) == 'nombre') {
            session(['countNoleidos' => $countNoleidos+1]);
            return null;
        }

        // Hubo 2 no leidas, 5 con cargo erroneo, 4 identificacion mal escrita
        //toask:  "rodrigo_acosta" => "Carolina Garcia Carillo "
        // Usuarios nuevos: 0. Hubo 2 no leidas, 5 con cargo erroneo, 4 identificacion mal escrita,
        $laCedula = intval(trim($row['cedula']));
        if(!is_numeric($laCedula)){
            return null;
        }
        $elSex = trim(strtolower($row['sexo']));
        if( $elSex != 'femenino' && $elSex != 'masculino'){
            session(['countSex' => $countSex+1]);
            return null;
        }
        $elRol = trim(strtolower($row['rol']));
        if( $elRol != 'empleado' && $elRol != 'administrativo' && $elRol != 'supervisor'){
            session(['countNoleidos' => $countNoleidos+1]);
            return null;
        }
        $cargo = Cargo::where('nombre', 'like','%'.trim($row['cargo']).'%')->first();
        if($cargo === null){
            session(['countNoCargo' => $countNoCargo+1]);
            return null;
        }
        $elCorreo = trim($row['correo']);
        $elCorreo = Myhelp::quitarTildes($elCorreo);
        $elCorreo = str_replace(' ', '', $elCorreo);
        // $CorreoInValido = filter_var($elCorreo, FILTER_VALIDATE_EMAIL) === false;
        
        //fin validaciones

        $fechaIngreso = HelpExcel::getFechaExcel($row['fecha_ingreso']);
        if (User::where('email', $elCorreo)->exists()) {
            
            $usuariosActualizados = session('usuariosActualizados',[]);
            foreach ($usuariosActualizados as $key => $value) {
                if($laCedula == $value) {
                    session(['countCedulaRepetida' => $countCedulaRepetida+1]);
                    return null;
                }
            }
            $usuariosActualizados[] = $laCedula;
            session(['usuariosActualizados' => $usuariosActualizados]);

            $user = User::where('email', $elCorreo)->first();
            $user->update([
                'name' => $row['nombre'],
                'cedula'    => $laCedula,
                'password' => Hash::make($laCedula.'*'),
                'telefono' => $row['telefono'],
                'celular' => $row['celular'],
                'fecha_de_ingreso' => $fechaIngreso,
                'sexo' => $elSex,
                'salario' => $row['salario'],
                'cargo_id' => $cargo->id,
            ]);

            if($elRol == 'empleado'){
                $user->syncRoles('empleado');
            }
            if($elRol == 'administrativo'){
                $user->syncRoles('administrativo');
            }
            if($elRol == 'supervisor'){
                $user->syncRoles('supervisor');
            }
        }else{
            session(['CountFilas' => $countfilas+1]);

            $user = new User([
                'name' => $row['nombre'],
                'email'    => $elCorreo, 
                'password' => Hash::make($laCedula.'*'),
                'cedula' => $laCedula,
                'telefono' => $row['telefono'],
                'celular' => $row['celular'],
                'fecha_de_ingreso' => $fechaIngreso,
                'sexo' => $elSex,
                'salario' => $row['salario'],
                'cargo_id' => $cargo->id,
            ]);

            if($elRol == 'empleado'){
                $user->syncRoles('empleado');
            }
            if($elRol == 'administrativo'){
                $user->syncRoles('administrativo');
            }
            if($elRol == 'supervisor'){
                $user->syncRoles('supervisor');
            }

            return $user;
        }
    }
}
