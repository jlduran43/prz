<?php

namespace App\Mail;

use App\Models\Cotizacion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CotizacionGeneradaMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Cotizacion $cotizacion, public string $urlConvertir) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cotización ' .
                $this->cotizacion->folio .
                ' - Parque Pedro del Río Zañartu'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.cotizacion-generada',
            with: [
                'cotizacion' => $this->cotizacion,
                'urlConvertir' => $this->urlConvertir,
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
        $configuracion =
            \App\Models\ConfiguracionCotizacion::first();

        $this->cotizacion->load([
            'tipoCliente',
            'region',
            'comuna',
            'servicios',
        ]);

        $pdf = Pdf::loadView(
            'cotizaciones.pdf',
            [
                'cotizacion' => $this->cotizacion,
                'configuracion' => $configuracion,
            ]
        );

        $pdf->setPaper(
            'a4',
            'portrait'
        );

        return [
            Attachment::fromData(
                fn() => $pdf->output(),
                $this->cotizacion->folio . '.pdf'
            )->withMime(
                'application/pdf'
            ),
        ];
    }
}
