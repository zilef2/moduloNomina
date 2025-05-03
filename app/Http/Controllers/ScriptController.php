<?php

namespace App\Http\Controllers;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Command\Command as CommandSymfony;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ScriptController extends Controller {
	
	public function JustDeploy($pruebas = 0): int|string {
		$request = request();
//		try {
//		    $process = new Process(['whoami']);
//			$process->run();
//			if (!$process->isSuccessful()) {
//				throw new ProcessFailedException($process);
//			}
//			$user = trim($process->getOutput());
//			//			dd(function_exists('proc_open'), $user);
//			
//		} catch (ProcessFailedException $exception) {
//			echo $exception->getMessage();
//			dd(function_exists('proc_open'), null); // Indicar fallo
//		}
		
		if ($pruebas == 0) {
			$scriptPath = '/home/wwecno/pruebas/deploy.sh';
		}
		else {
			$scriptPath = '/home/wwecno/repo/modnom2/deploy.sh';
		}
		
		if (!file_exists($scriptPath)) {
			$this->error("El script no existe en: $scriptPath");
			
			
			return CommandSymfony::FAILURE;
		}
		Log::info('Valor recibido en X-DEPLOY-KEY:', ['key' => request()->header('X-DEPLOY-KEY')]);
		Log::info('Valor recibido en DEPLOY_KEY2:', ['key' => config('deploy.key')]);
		
		if ($request->header('X-DEPLOY-KEY') !== config('deploy.key')) abort(403, 'No autorizado');
		
		$process = new Process(['bash', '/home/wwecno/pruebas/deploy.sh']);
		$process->run();
		
		if (!$process->isSuccessful()) {
			return response()->json(['status' => 'error', 'output' => $process->getErrorOutput()], 500);
		}
		
		
		return response()->json(['status' => 'ok', 'output' => $process->getOutput()]);
	}
}
