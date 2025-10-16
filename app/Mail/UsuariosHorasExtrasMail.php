<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class UsuariosHorasExtrasMail extends Mailable
{
    use Queueable, SerializesModels;

    public Collection $usuarios;
    public String $fecha;
    public int $MaxDiasSemanales;

    public function __construct($usuarios, $fecha,$MaxDiasSemanales = 44)
    {
        $this->usuarios = $usuarios;
        $this->fecha = $fecha;
        $this->MaxDiasSemanales = $MaxDiasSemanales;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Usuarios con Horas Extras — ' . $this->fecha,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.usuarios-horas-extras',
        );
    }
}