<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificacionCotizacionConAdjunto extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $pdfData;

    /**
     * Create a new message instance.
     */
    public function __construct(array $data, string $pdfData)
    {
        $this->data = $data;
        $this->pdfData = $pdfData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notificación de Cotización de Prueba con Adjunto',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mails.cotizacion_notificacion_test',
            with: [
                'cuantosViaticos' => $this->data['cuantosViaticos'],
                'total' => $this->data['total'],
                'solicitante' => $this->data['solicitante'],
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn() => $this->pdfData, 'cotizacion.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
