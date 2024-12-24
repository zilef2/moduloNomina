<?php

namespace App\Http\Controllers;

use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandSymfony;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

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

        $process = new Process(["bash", $scriptPath]);
        $process->run();
        // Verifica si el comando fue exitoso
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();
        return "$output";
    }
}
