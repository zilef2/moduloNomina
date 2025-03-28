<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CentroCostoRequest extends FormRequest
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
        return
        [
			'nombre' => 'required|unique:centro_costos,nombre',
        ];
    }

    public function messages()
    {
        return [
            'nombre.unique' => 'El nombre ya está en uso.',
        ];
    }
}
