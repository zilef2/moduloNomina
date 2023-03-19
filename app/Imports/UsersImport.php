<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Contracts\Queue\ShouldQueue;

class UsersImport implements ToModel,WithChunkReading,ShouldQueue, WithValidation, WithHeadingRow
{
    use Importable;

    public function rules(): array
    {
        return [
            // '1' => [Rule::unique(['users', 'email'])],
            'correo' => 'required|email|min:5',
            // 'cedula' => 'required|min:6',
            'usuario' => 'min:2'
        ];
    }

    public function chunkSize(): int { return 11000; }

    public function model(array $row)
    {
        // Validator::make($row, [
        //     '*.1' => 'email',
        // ])->validate();
        // firstOrCreate
        if (User::where('email', $row['correo'])->exists()) {
            return null;
        }
        $countfilas = session('CountFilas',0); session(['CountFilas' => $countfilas+1]);
        return new User([
            'name'     => $row['usuario'],
            'email'    => $row['correo'], 
            'password' => Hash::make($row['usuario']),
        ]);
    }
}
