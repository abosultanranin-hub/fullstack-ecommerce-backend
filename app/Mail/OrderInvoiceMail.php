<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\orders;

class OrderInvoiceMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order;
    public $pdfOutput;

    /**
     * Create a new message instance.
     *
     * @param orders $order
     * @param string $pdfOutput Binary content of the PDF
     */
    public function __construct(orders $order, $pdfOutput)
    {
        $this->order = $order;
        $this->pdfOutput = $pdfOutput;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('ففاتورة طلبك #' . $this->order->number)
                    ->view('emails.order_invoice') // You might want a simple view for the email body too
                    ->attachData($this->pdfOutput, 'invoice_' . $this->order->number . '.pdf', [
                        'mime' => 'application/pdf',
                    ])
                    ->with([
                        'order' => $this->order,
                        'user' => $this->order->user,
                    ]);
    }
}
