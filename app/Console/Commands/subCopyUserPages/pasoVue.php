<?php

namespace App\Console\Commands\subCopyUserPages;

use Illuminate\Console\Command;
use App\Console\Commands\Constants;
use Illuminate\Support\Facades\File;

class pasoVue extends Command
{
    use Constants;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copy:vue';
    protected $description = 'Command description';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $generando = self::getMessage('generando');
        $exito = self::getMessage('exito');
        $fallo = self::getMessage('fallo');
        
        $RealizoVueConExito = $this->MakeVuePages('generic', 'cotizacion');
        $mensaje = $RealizoVueConExito ? $generando . ' Vuejs' . $exito
            : $exito . ' Vuejs' . self::$fallo;
        
        $this->info($mensaje);
    }
    
    private function MakeVuePages($plantillaActual, $modelName): bool
    {
        $sourcePath = base_path('resources/js/Pages/' . $plantillaActual);
        $destinationPath = base_path("resources/js/Pages/{$modelName}");

        if (File::exists($destinationPath)) {
            $this->warn("La carpeta de destino '{$modelName}' ya existe.");
            return false;
        }
        File::copyDirectory($sourcePath, $destinationPath);
        return true;
    }
}
