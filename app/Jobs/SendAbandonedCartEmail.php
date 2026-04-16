<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\CartApi;

class AbandonedCartMail extends Mailable
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
                    ->view('emails.abandoned_cart') // أنشئي هذا view
                    ->with([
                        'cart' => $this->cart,
                        'user' => $this->cart->user,
                    ]);
    }
}
