<?php

namespace App\Mail;

use App\Models\Reservation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Reservation $reservation,
        public ?string $customMessage = null
    ) {}

    public function envelope(): Envelope
    {
        $company = setting('empresa_nombre', config('app.name', 'Aloja360'));
        return new Envelope(
            subject: "Comprobante de Reserva #{$this->reservation->code} - {$company}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reservation_invoice',
        );
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('pdf.reservation', ['reservation' => $this->reservation]);

        return [
            Attachment::fromData(fn () => $pdf->output(), "Reserva-{$this->reservation->code}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
