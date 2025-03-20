<?php

namespace App\Http\Controllers;

use App\Models\zona;

class HelperControllerSelect extends Controller {
    //todo: thisisnew!!!
    public function DependenciasCentro($selecioneUno, $elNombre = 'nombre'): array {
        $dependexsSelect = zona::all(['id as value', $elNombre])->map(function ($item) {
                return [
                    'value' => $item->value,
                    'label' => ($item->nombre ?? '')
                ];
            })->toArray();

        array_unshift($dependexsSelect, ["label" => "Seleccione $selecioneUno", 'value' => 0]);
        
        return [
            $selecioneUno => $dependexsSelect,
        ];
    }
}
