<?php

namespace App\Console\Commands\subCopyUserPages;

use App\Console\Commands\Constants;
use Illuminate\Console\Command;

class pasoAuxiliar extends Command
{
    use Constants;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copy:aux';
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $generando = self::getMessage('generando');
        $exito = self::getMessage('exito');
        $fallo = self::getMessage('fallo');

        $directory = 'routes';
        $files = glob($directory . '/*.php');
        $resource = 'cotizacion';
        $insertable2 = "Route::resource(\"/$resource\", \\App\\Http\\Controllers\\" . ucfirst($resource) . "Controller::class);\n\t//aquipues";

        $pattern = '/\/\/aquipues/';

        $contadorVerificador = 0;
        foreach ($files as $file) {
            $content = file_get_contents($file);
            $contadorVerificador++;

            if (!str_contains($content, $pattern)) {
                $content2 = preg_replace($pattern, $insertable2, $content);
//                $content2 = preg_replace($pattern, "$0$insertable", $content);
                file_put_contents($file, $content2);
                if ($content == $content2)
                    $this->info("Routes Actualizado: $file\n");
                else
                    $this->info("Routes sin cambios: $file\n");
            } else {
                $this->error("No existe aquipues en: $file\n");
                $contadorVerificador = 0;
                break;
            }
        }
        return $contadorVerificador;
    }
}
