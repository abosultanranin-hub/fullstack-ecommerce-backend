<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CartApi;
use App\Mail\AbandonedCartMail;
use Illuminate\Support\Facades\Mail;

class CheckAbandonedCarts extends Command
{
    protected $signature = 'cart:check-abandoned';
    protected $description = 'Send email if cart is abandoned for 7 days';

    public function handle(): void
    {
        $this->info('Starting abandoned cart check...');

        $carts = CartApi::where('is_checked_out', 0)
            ->where('abandoned_email_sent', 0)
            ->get();

        $this->info("Found {$carts->count()} potential abandoned carts");

        foreach ($carts as $cart) {
            $this->info("Checking cart ID: {$cart->id}, updated: {$cart->updated_at}");

            if ($cart->isAbandoned(7)) {
                $this->info("Cart ID {$cart->id} is abandoned");

                // إرسال الإيميل (عن طريق Queue)
                if ($cart->user) {
                    $this->info("Queueing email to user: {$cart->user->email}");
                    Mail::to($cart->user)->queue(new AbandonedCartMail($cart));
                } else {
                    $this->warn("Cart ID {$cart->id} has no associated user");
                }

                // منع التكرار
                $cart->markAbandonedEmailSent();

                $this->info("Abandoned email sent for cart ID: {$cart->id}");
            } else {
                $this->info("Cart ID {$cart->id} is not abandoned yet");
            }
        }

        $this->info('Abandoned cart check completed.');
    }
}
