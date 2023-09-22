<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:' . User::class,
            'cedula'        => 'required|numeric|min:6',
            // 'password'      => ['required', 'confirmed', Password::defaults()],
            'role'          => ['required'],
            'salario'       => ['required'],
            'telefono'      => ['nullable'],
            'celular'       => ['required'],
            'fecha_de_ingreso' => ['required'],
            'sexo'          => ['required'],
            'centroid'          => ['nullable'],
        ];
    }
}
