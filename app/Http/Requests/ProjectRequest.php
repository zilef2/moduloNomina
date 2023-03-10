<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
{
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
			'nombre' => 'required',
			// 'cliente' => 'required',
			// 'num_modulos' => 'required',
			// 'valor_tentativo' => 'required',
			'valor_acordado' => 'required',

			'valor_primer_pago' => 'required',
			'fecha_primera_reunion' => 'required',
			// 'fecha_primer_pago' => 'required',
			'fecha_entrega' => 'required',

			'observaciones' => 'required',
        ];
    }
}
