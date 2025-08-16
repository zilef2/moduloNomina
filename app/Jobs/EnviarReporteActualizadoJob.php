<?php

namespace App\Jobs;

use App\Mail\ReportUpdatedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class EnviarReporteActualizadoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $correo;
    private $data;

    public function __construct($correo, $data)
    {
        $this->correo = $correo;
        $this->data = $data;
    }

    public function handle(): void
    {
        Mail::to($this->correo)->send(new ReportUpdatedMail($this->data));
    }
}
