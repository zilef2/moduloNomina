<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserIndexRequest extends FormRequest
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
            'field' => ['in:name,email,created_at,updated_at,cedula,cargo_id,celular,telefono,fecha_de_ingreso,centro_costo_id'],
            'order' => ['in:asc,desc'],
            'perPage' => ['numeric'],
        ];
    }
}
