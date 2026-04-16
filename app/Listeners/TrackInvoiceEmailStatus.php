<?php

namespace App\Listeners;

use App\Models\Invoice;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Log;

class TrackInvoiceEmailStatus
{
    /**
     * Handle the event.
     */
    public function handle(MessageSent $event): void
    {
        try {
            $headers = $event->message->getHeaders();

            if (! method_exists($headers, 'get')) {
                return;
            }

            $invoiceHeader = $headers->get('X-Invoice-Id');
            if (! $invoiceHeader) {
                return;
            }

            $invoiceId = method_exists($invoiceHeader, 'getBodyAsString')
                ? $invoiceHeader->getBodyAsString()
                : null;

            if (! is_numeric($invoiceId)) {
                return;
            }

            $invoice = Invoice::find((int) $invoiceId);
            if ($invoice) {
                $invoice->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);

                Log::info('Invoice email marked as sent: ' . $invoice->invoice_number);
            }
        } catch (\Throwable $e) {
            Log::warning('Invoice email tracking skipped: ' . $e->getMessage());
        }
    }
}
