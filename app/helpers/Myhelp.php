<?php

namespace App\helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Normalizer;

// use Hamcrest\Type\IsInteger;

class Myhelp{

    const MyRoles =[
        'empleado' => 1,
        'administrativo' => 2,
        'supervisor' => 3,
        'admin' => 9,
        'superadmin' => 10
    ];

    //************************logs************************\\
    
    public static function getPermissionToNumber($permissions) {

        // $valorReturn = 0;
        // if(in_array($permissions, constant('MyRoles'))){
        //     $valorReturn = constant('MyRoles')[$permissions];
        // }
        // dd($valorReturn);
        // return $valorReturn;
        
        if ($permissions == 'empleado') return 1;
        if ($permissions == 'administrativo') return 2;
        if ($permissions == 'supervisor') return 3;
        if ($permissions == 'admin') return 9;
        if ($permissions == 'superadmin') return 10;
        return 0;
    }
    public static function EscribirEnLog($thiis, $clase = '', $mensaje = '', $returnPermission = true, $critico = false) {
        $permissions = $returnPermission ? auth()->user()->roles->pluck('name')[0] : null;
        $ListaControladoresYnombreClase = (explode('\\', get_class($thiis)));
        $nombreC = end($ListaControladoresYnombreClase);
        if (!$critico) {
            $Elpapa = (explode('\\', get_parent_class($thiis)));
            $nombreP = end($Elpapa);

            $ElMensaje = $mensaje != '' ? ' Mensaje: ' . $mensaje : '';
            $ElMensaje = 'Vista: ' . $nombreC . ' Padre: ' . $nombreP . 'U:' . Auth::user()->name . ' | clase: ' . $clase . '|| ' . ' Mensaje: ' . $ElMensaje;
            if ($permissions == 'admin' || $permissions == 'superadmin') {
                Log::channel('soloadmin')->info($ElMensaje);
            } else {
                if($permissions == 'isadministrativo'){
                    Log::channgel('soloadministrativo')->info($ElMensaje);
                }else{
                    if($permissions == 'issupervisor'){
                        Log::channgel('issupervisor')->info($ElMensaje);
                    }else{
                        Log::info($ElMensaje);
                    }
                }
            }
            return $permissions;
        } else {
            Log::critical('Vista: ' . ($nombreC??'null') . 'U:' . Auth::user()->name . ' ||' . ($clase??'null') . '|| ' . ' Mensaje: ' . ($mensaje??'null'));
        }
    }

    //************************laravel************************\\


    public function redirect($ruta,$seconds = 4) {
        sleep($seconds);
        return redirect()->to($ruta);
    }


    //************************string************************\\
    function cortarFrase($frase, $maxPalabras = 3) {
        $noTerminales = [
            "de","a","para",
            "of","by","for"
        ];

        $palabras = explode(" ", $frase);
        $numPalabras = count($palabras);
        if ($numPalabras > $maxPalabras) {
            $offset = $maxPalabras - 1;
            while (in_array($palabras[$offset], $noTerminales) && $offset < $numPalabras) {
                $offset++; 
            }
            $ultimaPalabra = $palabras[$offset];
            if((intval($ultimaPalabra)) != 0){
                session(['ultimaPalabra' => $ultimaPalabra]);
            }
            return implode(" ", array_slice($palabras, 0, $offset + 1));
        }
        return $frase;
    }

    public static function quitarTildes($palabras){
        $normalizedString = Normalizer::normalize($palabras, Normalizer::FORM_D);
        $cleanString = preg_replace('/\p{Mn}/u', '', $normalizedString);
        return $cleanString;
    }

    public function erroresExcel($errorFeo){
        // $fila = session('ultimaPalabra');
        $error1 ="PDOException: SQLSTATE[22007]: Invalid datetime format: 1292 Incorrect date";
        if($errorFeo == $error1){
            return 'Existe una fecha invalida';
        }
        return 'Error desconocido';
    }
    public function ValidarFecha($laFecha){
        if(strtotime($laFecha)){
            return $laFecha;
        }
        return '';
    }

} ?>