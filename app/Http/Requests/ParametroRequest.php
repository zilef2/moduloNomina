<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ParametroRequest extends FormRequest
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
            'subsidio_de_transporte_dia' => 'required',
            'salario_minimo' => 'required',
            'porcentaje_diurno' => 'required|numeric',
            'porcentaje_nocturno' => 'required|numeric',
            'porcentaje_extra_diurno' => 'required|numeric',
            'porcentaje_extra_nocturno' => 'required|numeric',
            'porcentaje_dominical_diurno' => 'required|numeric',
            'porcentaje_dominical_nocturno' => 'required|numeric',
            'porcentaje_dominical_extra_diurno' => 'required|numeric',
            'porcentaje_dominical_extra_nocturno' => 'required|numeric',
        ];
    }
}
