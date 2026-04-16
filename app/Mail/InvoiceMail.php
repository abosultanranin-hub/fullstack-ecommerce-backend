<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;

class InvoiceMail extends Mailable
{

    public Invoice $invoice;
    public string $pdfPath;

    /**
     * Create a new message instance.
     */
    public function __construct(Invoice $invoice, string $pdfPath)
    {
        $this->invoice = $invoice;
        $this->pdfPath = $pdfPath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'فاتورة رقم ' . $this->invoice->invoice_number,
            from: env('MAIL_FROM_ADDRESS', 'noreply@ecommerce.com'),
        );
    }

    /**
     * Attach invoice metadata so listeners can safely track send status.
     */
    public function headers(): Headers
    {
        return new Headers(
            text: [
                'X-Invoice-Id' => (string) $this->invoice->id,
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice',
            with: [
                'invoice' => $this->invoice,
                'user' => $this->invoice->user,
                'order' => $this->invoice->order,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pdfPath)
                ->as($this->invoice->invoice_number . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
