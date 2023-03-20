<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection,ShouldAutoSize,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $users = User::Select(
            'name',
            'email',
            'cedula',
            'telefono',
            'celular',
        )->whereHas("roles", function($q){ $q->where("name", "operator"); })->get();
        // $users = User::all('name');
        // dd($users);
        return $users;
    }

    public function headings() :array
    {
        return [
            "name",
            "email",
            // "created_at",
            // "updated_at",
            "cedula",
            "telefono",
            "celular",
        ];
    }
}
