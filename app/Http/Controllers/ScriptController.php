<?php

namespace App\Http\Controllers;

use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandSymfony;

class ScriptController extends Controller
{
    public function JustDeploy($pruebas = 0): int
    {
        if($pruebas == 0){
            $scriptPath = '/home/wwecno/scr/deploy.sh';
        }else{
            $scriptPath = '/home/wwecno/scr/PruebasDeploy.sh';
        }

        if (!file_exists($scriptPath)) {
            $this->error("El script no existe en: $scriptPath");
            return CommandSymfony::FAILURE;
        }

        $output = shell_exec("bash $scriptPath 2>&1");

        if ($output === null) {
            $this->error('Hubo un error al ejecutar el script.');
            return CommandSymfony::FAILURE;
        }

        $this->info("Script ejecutado correctamente:\n$output");
        return CommandSymfony::SUCCESS;
    }
}
