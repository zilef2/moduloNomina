<?php

namespace App\Imports;

use App\helpers\HelpExcel;
use App\helpers\Myhelp;
use App\Models\Cargo;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Contracts\Queue\ShouldQueue;

class UsersImport implements ToModel,WithChunkReading,ShouldQueue, WithValidation, WithHeadingRow {

    use Importable;

    // public function rules(): array
    // {
    //     return [
    //         // '1' => [Rule::unique(['users', 'email'])],
    //         // 'correo' => 'required|email|min:5',
    //         // 'cedula' => 'required|min:6',
    //         'usuario' => 'min:2'
    //     ];
    // }

    public function rules(): array
    {
        return [
            'correo' => 'required|email|min:5',
            'usuario' => 'min:2'
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'correo' => 'Correo invalido. Fila :attribute.',
            'usuario' => 'Nombre corto. Fila :attribute.',
        ];
    }

    public function chunkSize(): int { return 11000; }

    public function model(array $row)
    {
        $countfilas = session('CountFilas',0);
        $countNoleidos = session('countNoleidos',0);
        $countSex = session('countSex',0);
        $countNoCargo = session('countNoCargo',0);
        $countCedulaRepetida = session('countCedulaRepetida',0);

        if (strtolower(trim($row['nombre'])) == 'nombre') {
            session(['countNoleidos' => $countNoleidos+1]);
            return null;
        }

        $laCedula = trim(intval($row['cedula']));
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
