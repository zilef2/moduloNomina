<?php

namespace App\Jobs;

use App\helpers\MyModels;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LogZilefMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $mensaje;

    /**
     * Create a new job instance.
     *
     * @param  string  $mensaje
     * @return void
     */
    public function __construct(string $mensaje)
    {
        $this->mensaje = $mensaje;
    }

    /**
     * Execute the job.
     *
     * @return int
     */
    public function handle()
    {
        $permissions = auth()->check() ? auth()->user()->roles->pluck('name')->first() : null;

        if ($permissions) {
            Log::channel(MyModels::getPermissiToLog($permissions))->info($this->mensaje);
            return MyModels::getPermissionToNumber($permissions);
        }

        // Manejar el caso en que no hay usuario autenticado o no tiene roles
        Log::channel('default')->warning('No se pudo determinar el rol para el log: ' . $this->mensaje);
        return 0; // O algún otro valor por defecto
    }
}
