<?php

namespace App\Http\Controllers;

use App\Models\zona;

class HelperControllerSelect extends Controller {
    
    public function DependenciasCentro($selecioneUno): array {
        $dependexsSelect = zona::all(['id as value', 'nombre'])->map(function ($item) {
                return [
                    'value' => $item->value,
                    'label' => ($item->nombre ?? '')
                ];
            })->toArray();

        array_unshift($dependexsSelect, ["label" => "Seleccione un$selecioneUno", 'value' => 0]);
        
        return [
            'zona' => $dependexsSelect,
        ];
    }
}
