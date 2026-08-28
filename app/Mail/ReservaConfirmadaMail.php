<?php

namespace App\Mail;

use App\Models\Reserva;
use App\Services\ReservaQrService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservaConfirmadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public Reserva $reserva;

    public function __construct(
        Reserva $reserva
    ) {
        $this->reserva = $reserva;
    }

    public function envelope(): Envelope
    {
        $folio = 'RES-' .
            str_pad(
                $this->reserva->id,
                6,
                '0',
                STR_PAD_LEFT
            );

        return new Envelope(
            subject: 'Reserva confirmada ' . $folio,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reserva-confirmada',
            with: [
                'reserva' => $this->reserva,
            ],
        );
    }

    public function attachments(): array
    {
        $reserva = $this->reserva->loadMissing([
            'servicios',
            'tipoCliente',
        ]);

        $qrService = app(
            ReservaQrService::class
        );

        $qrRelativo = $qrService->generar(
            $reserva
        );

        $qrPath = storage_path(
            'app/public/' . $qrRelativo
        );

        $folio = 'RES-' .
            str_pad(
                $reserva->id,
                6,
                '0',
                STR_PAD_LEFT
            );

        $pdf = Pdf::loadView(
            'reservas.comprobante-pdf',
            [
                'reserva' => $reserva,
                'qrPath' => $qrPath,
            ]
        )->setPaper(
            'a4',
            'landscape'
        );

        return [
            Attachment::fromData(
                fn() => $pdf->output(),
                'ticket-' . $folio . '.pdf'
            )->withMime(
                'application/pdf'
            ),
        ];
    }
}