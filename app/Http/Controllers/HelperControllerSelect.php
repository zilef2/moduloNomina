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
	
	public function DependenciasParaVselec($model, $selecioneUno, $elNombre = 'nombre'): array {
		// Construye el namespace completo del modelo si no lo es
		if (!class_exists($model)) {
			$model2 = "\\App\\Models\\$model";
		}
		
		// Obtener los datos del modelo dinámicamente
		$dependexsSelect = $model2::all(['id as value', $elNombre])->map(function ($item) use ($elNombre) {
			return [
				'value' => $item->value,
				'label' => ($item->$elNombre ?? '')
			];
		})->toArray();
		
		// Agrega la opción por defecto
		array_unshift($dependexsSelect, ["label" => "Seleccione $selecioneUno", 'value' => 0]);
		
		
		return [
			$model => $dependexsSelect,
		];
	}
	
}
