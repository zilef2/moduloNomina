<?php

namespace App\Jobs;

use App\Mail\MailViaticoGenerado;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class EnviarViaticoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $datos;
    protected $correo;

    public function __construct($correo, $datos)
    {
        $this->correo = $correo;
        $this->datos = $datos;
    }

    public function handle(): void
    {
        Mail::to($this->correo)->send(new MailViaticoGenerado($this->datos));
    }
}
