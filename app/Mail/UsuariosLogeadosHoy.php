<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UsuariosLogeadosHoy extends Mailable
{
    use Queueable, SerializesModels;

    public $usuarios;
    public $fecha;

    public function __construct($usuarios,$fecha)
    {
        $this->usuarios = $usuarios;
        $this->fecha = $fecha;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Usuarios de Hoy',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.usuarios-logeados',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
