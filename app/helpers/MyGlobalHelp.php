<?php

namespace App\helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

//JUST THIS PROJECT
//STRING S
//LARAVELFunctions
//dateFunctions
//arrayFunctions 

class MyGlobalHelp {

    public static function PrimerasPalabras()
    {
        $TheUser = Auth::user();
        if($TheUser){
            return $TheUser;
        }
        return redirect()->to('/');
    }

    public static function formatFechaColombia2($fechaISO8601)
    {
        $fecha = new \DateTime($fechaISO8601);
        $fecha->setTimezone(new \DateTimeZone('America/Bogota'));
        return $fecha->format('d-m h:i A');
//        return $fecha->format('d-m h:i A');
//        return $fecha->format('d-m-Y H:i:s');
    }
    public static function formatFechaColombia($fechaISO8601)
    {
        $fecha = Carbon::parse($fechaISO8601);

        // Establecer el timezone a Colombia
        $fecha->setTimezone('America/Bogota');

        // Establecer el locale a español (debes asegurarte de que el locale es_ES.UTF-8 esté disponible en tu sistema)
        $fecha->locale('es');

        
        return $fecha->translatedFormat('d \\d\\e F h:i A');
//        return $fecha->translatedFormat('d \\d\\e F \\d\\e Y h:i:s A');
    }
    
}?>