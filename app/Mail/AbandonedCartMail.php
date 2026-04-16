<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\CartApi;

class AbandonedCartMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public CartApi $cart;

    public function __construct(CartApi $cart)
    {
        $this->cart = $cart;
    }

    public function build()
    {
        return $this->subject('تذكير: لديك سلة مهجورة')
                    ->view('emails.abandoned_cart')
                    ->with([
                        'cart' => $this->cart,
                        'user' => $this->cart->user,
                    ]);
    }
}
