<?php

namespace App\Mail;

use App\Models\Quote;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuoteInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Quote $quote,
        public ?string $customMessage = null
    ) {}

    public function envelope(): Envelope
    {
        $company = setting('empresa_nombre', config('app.name', 'Aloja360'));
        return new Envelope(
            subject: "Cotización #{$this->quote->code} - {$company}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.quote_invoice',
        );
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('pdf.quote', ['quote' => $this->quote]);

        return [
            Attachment::fromData(fn () => $pdf->output(), "Cotizacion-{$this->quote->code}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
