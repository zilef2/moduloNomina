<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CopyUserPages extends Command
{
    protected $signature = 'copy:u {folderName} {depende?}';
    protected $description = 'Copia la carpeta designada a una ubicación específica';

    public function handle(){
        $plantillaActual = 'generic';
        $mensajeA = 'La genericacion de ';
        $mensajeExito = ' fue realizada con exito ';
        $mensajeFallo = ' fallo';

        $RealizoVueConExito = $this->MakeVuePages($plantillaActual);
        $mensaje = $RealizoVueConExito ? $mensajeA.'los componentes de Vuejs'.$mensajeExito
            : $mensajeA.'los componentes de Vuejs'.$mensajeFallo;
        $this->info($mensaje);
//        sleep(1);

        $RealizoControllerConExito = $this->MakeControllerPages($plantillaActual);
        $mensaje = $RealizoControllerConExito ? $mensajeA.'el controlador'.$mensajeExito
            : $mensajeA.' el controlador '.$mensajeFallo;
        $this->info($mensaje);

        if($RealizoControllerConExito || $RealizoVueConExito)
            $this->replaceWordInFiles($plantillaActual,
            [
                'vue' => $RealizoVueConExito,
                'controller' => $RealizoControllerConExito
            ]);

        $this->info("Fin de la operacion");
    }


    private function MakeControllerPages($plantillaActual){
        $folderName = $this->argument('folderName');
        $folderMayus = ucfirst($folderName);
        $sourcePath = base_path('app/Http/Controllers/'.$plantillaActual.'Controller.php');
        $destinationPath = base_path("app/Http/Controllers/".$folderMayus."Controller.php");

        if (File::exists($destinationPath)) {
            $this->warn("La carpeta de destino '{$destinationPath}' ya existe.");
            return false;
        }
        File::copyDirectory($sourcePath, $destinationPath);
        $this->info("info:  ".$sourcePath);
        $this->info("info:  ".$destinationPath);

        return true;
    }

    private function MakeVuePages($plantillaActual){
        $folderName = $this->argument('folderName');

        $sourcePath = base_path('resources/js/Pages/'.$plantillaActual);
        $destinationPath = base_path("resources/js/Pages/{$folderName}");

        if (File::exists($destinationPath)) {
            $this->warn("La carpeta de destino '{$folderName}' ya existe.");
            return false;
        }
        File::copyDirectory($sourcePath, $destinationPath);
        return true;
    }

    private function replaceWordInFiles($oldWord,$permiteRemplazo){
        $newWord = $this->argument('folderName');
        $folderMayus = ucfirst($newWord);
        $files = File::allFiles(base_path("resources/js/Pages/{$newWord}"));
        $controller = base_path("app/Http/Controllers/{$folderMayus}".'Controller.php');
        $depende = $this->argument('depende') ?? '';

        $depende = $depende == '' || $depende == null ? 'no_nada' : $depende;

        if($permiteRemplazo['vue']){
            foreach ($files as $key => $file) {

                $content = file_get_contents($file);
                $content = str_replace(array($oldWord, ucfirst($oldWord),'geeneric'),//ojo aqui, es estatico
                                            [$newWord, $folderMayus,$folderMayus],
                                            $content
                );
                file_put_contents($file, $content);
            }
        }

        //reemplazo de controlador
        if($permiteRemplazo['controller']){
                $sourcePath = base_path('app/Http/Controllers/'.ucfirst($oldWord).'Controller.php');
                $content = file_get_contents($sourcePath);
                $content = str_replace(array($oldWord, 'dependex','deependex','geeneric'),//ojo aqui, es estatico
                                       array($newWord,  $depende ,ucfirst($depende),ucfirst($newWord)),
                                       $content
                );

                file_put_contents($controller, $content);
        }
    }
}
