<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCostoRequest extends FormRequest
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
    public function rules(){
        return [
            'nombre' => 'required',
        ];
    }
//       public function rules()
//    {
//        $costoId = $this->route('CentroCostos.update');
//        return [
//            'nombre' => 'required|unique:centro_costos,nombre,' . $costoId,
//        ];
//    }

//    public function messages()
//    {
//        return [
//            'nombre.required' => 'Debe digitar el centro de costo',
//            'nombre.unique' => 'El nombre ya está en uso.2',
//        ];
//    }

    public function messages(){
        return [
            'nombre.required' => 'Debe digitar el centro de costo',
        ];
    }
}
