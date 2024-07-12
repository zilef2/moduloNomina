<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ZipArchive;

class ZipController extends Controller
{

    protected $signature = 'send:zip';

    public function DescompresionDespliegue($esAmbientePruebas = 1): string
    {
        if (extension_loaded('zip')) {
            $extensionActiva = 'Zip extension is loaded. Version: ' . phpversion('zip');

            if($esAmbientePruebas){
                $extractTo = '/home/wwecno/pruebas';
                $mensajeAmbiente = ' <br><b>Ambiente de pruebas</b>';
            }else{
                $extractTo = '/home/wwecno/repo/modnom';
                $mensajeAmbiente = ' <br><b>Ambiente de produccion</b>';
            }
            $zipFile = $extractTo.'/moduloNomina.zip';

            $zip = new ZipArchive;
            if ($zip->open($zipFile) === TRUE) {
                $zip->extractTo($extractTo);
                $zip->close();
                return $extensionActiva. ' <br>Descompresión exitosa<br>'.$mensajeAmbiente;
            } else {
                return $extensionActiva. ' Falló la apertura del archivo zip';
            }
        } else {
            return '<h1>Zip extension is not loaded.</h1>';
        }
    }
}
