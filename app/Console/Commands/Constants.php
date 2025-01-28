<?php

namespace App\Console\Commands;

trait Constants
{
    public static function getMessage($type): string
    {
        return match ($type) {
            'generando' => 'La generación del componente: ',
            'exito' => ' fue realizada con éxito ',
            
            'fallo' => ' Fallo',
            
            
            'plantillaActual' => 'generic',
        };
    }
}
