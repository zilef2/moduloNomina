<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReporteRequest extends FormRequest
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
			'fecha_ini' => 'required',
			'fecha_fin' => 'required',
			'horas_trabajadas' => 'required|min:1',
			'centro_costo_id' => 'required',
			// 'valido' => 'required',
			// 'observaciones' => 'required',
            'diurnas' => 'required',
            'nocturnas' => 'required',
            'extra_diurnas' => 'required',
            'extra_nocturnas' => 'required',
            'dominical_diurnas' => 'required',
            'dominical_nocturnas' => 'required',
            'dominical_extra_diurnas' => 'required',
            'dominical_extra_nocturnas' => 'required',
        ];
    }
}
