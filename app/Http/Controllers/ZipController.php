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
            }else{
                $extractTo = '/home/wwecno/repo/modnom';
            }
            $zipFile = $extractTo.'/moduloNomina.zip';

            $zip = new ZipArchive;
            if ($zip->open($zipFile) === TRUE) {
                $zip->extractTo($extractTo);
                $zip->close();
                return $extensionActiva. ' Descompresión exitosa';
            } else {
                return $extensionActiva. ' Falló la apertura del archivo zip';
            }
        } else {
            return 'Zip extension is not loaded.';
        }
    }
}
