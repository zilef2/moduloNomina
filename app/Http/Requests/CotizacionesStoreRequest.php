<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CotizacionesStoreRequest extends FormRequest {
    public function authorize(): bool {
        return true; // Permite que cualquier usuario envíe el request
    }

    public function rules(): array {
        return [
            'numero_cot' => 'nullable|string|min:0',
            'descripcion_cot' => 'nullable|string|min:0',
            'precio_cot' => 'nullable|numeric|min:0',
            'aprobado_cot' => 'nullable|min:0',
            'fecha_aprobacion_cot' => 'nullable|min:0',
            'centro_costo_id' => 'nullable|array',
            'fecha_solicitud' => 'required|date',
//            'factura' => 'required',
//            'fecha_factura' => 'required|date',
            'lugar' => 'required|string',
            'tipo_de_mantenimiento' => 'nullable|array',
            'por_a' => 'nullable|numeric|min:0',
            'por_i' => 'nullable|numeric|min:0',
            'por_u' => 'nullable|numeric|min:0',
            'admi' => 'nullable|numeric|min:0',
            'impr' => 'nullable|numeric|min:0',
            'util' => 'nullable|numeric|min:0',
            'subtotal' => 'nullable|numeric|min:0',
            'iva' => 'nullable|numeric|min:0',
            'total' => 'nullable|numeric|min:0',
            'cliente' => 'nullable|string|min:1',
            'persona_que_solicita_la_propuesta_economica' => 'nullable|string|min:1',
            'orden_de_compra' => 'nullable|numeric|min:0',
            'hes' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|numeric|min:0',

            'estado_cliente' => 'required|array',
            'estado' => 'required|array',
//            'estado' => [
//                'required',
//                Rule::in(['Por ejecutar', 'En ejecución', 'Ejecutado']),
//            ],

            'mes_pedido' => 'required|array',
            'tipo' => 'nullable|array',

            'zona_id' => 'required|array',
            'persona_que_realiza_la_pe' => 'nullable|array',
        ];
    }
}
