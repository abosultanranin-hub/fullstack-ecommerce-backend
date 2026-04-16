<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\CartApi;
use App\Models\orders;
use App\Models\order_items;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\OrderInvoiceMail;
use App\Services\InvoiceService;

class StripeController extends Controller
{
    private string $frontendUrl;

    public function __construct()
    {
        $this->frontendUrl = rtrim((string) config('app.frontend_url'), '/');

        // Set Stripe API key only when the backend is actually configured.
        $stripeSecret = config('services.stripe.secret');
        if (filled($stripeSecret)) {
            Stripe::setApiKey($stripeSecret);
        }
    }

    private function stripeIsConfigured(): bool
    {
        return filled(config('services.stripe.secret'));
    }

    /**
     * Create Stripe Checkout Session for cart payment
     */
    public function checkout(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if (! $this->stripeIsConfigured()) {
            Log::error('Stripe checkout blocked because STRIPE_SECRET is missing.');

            return response()->json([
                'error' => 'خدمة الدفع غير مهيأة على الخادم. أضف STRIPE_SECRET في إعدادات Render ثم أعد النشر.',
            ], 500);
        }

        // Get cart items for user
        $cartItems = CartApi::where('user_id', $user->id)
            ->where('is_checked_out', 0)
            ->with('product')
            ->get();
        if ($cartItems->isEmpty()) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }

        // Calculate total
        $total = 0;
        $lineItems = [];
        foreach ($cartItems as $item) {
            $price = $item->price ?? $item->product->price;
            $total += $price * $item->quantity;
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $item->product->name,
                    ],
                    'unit_amount' => $price * 100, // in cents
                ],
                'quantity' => $item->quantity,
            ];
        }

        // Create Checkout Session
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $this->frontendUrl . '/payment/callback?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $this->frontendUrl . '/cart',
            'metadata' => [
                'user_id' => (string) $user->id,
            ],
        ]);

        return response()->json([
            'session_id' => $session->id,
            'url' => $session->url,
        ]);
    }

    /**
     * Handle payment success
     */
    public function success(Request $request)
    {
        if (! $this->stripeIsConfigured()) {
            return response()->json([
                'error' => 'خدمة الدفع غير مهيأة على الخادم. أضف STRIPE_SECRET في إعدادات Render ثم أعد النشر.',
            ], 500);
        }

        $sessionId = $request->query('session_id');
        if (!$sessionId) {
            return response()->json(['error' => 'Session ID missing'], 400);
        }

        // Retrieve session from Stripe
        $session = Session::retrieve($sessionId);
        if ($session->payment_status !== 'paid') {
            return response()->json(['error' => 'Payment not completed'], 400);
        }

        $userId = $session->metadata['user_id'];

        // Get cart items
        $cartItems = CartApi::where('user_id', $userId)
            ->where('is_checked_out', 0)
            ->with('product')
            ->get();

        // Create order
        $order = orders::create([
            'user_id' => $userId,
            'number' => 'ORD-' . time(),
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);

        // Create order items
        foreach ($cartItems as $item) {
            order_items::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'price' => $item->price ?? $item->product->price,
                'quantity' => $item->quantity,
            ]);
        }


        // Clear cart (Mark as checked out)
        CartApi::where('user_id', $userId)
            ->where('is_checked_out', 0)
            ->update(['is_checked_out' => 1]);

        // --- Create Invoice, Generate PDF & Send Email ---
        try {
            Log::info('Starting invoice creation for order: ' . $order->id);

            $invoiceService = new InvoiceService();

            $invoiceData = [
                'tax_amount' => 0, // Can be calculated based on business logic
                'shipping_amount' => 0, // Can be added from order data
                'discount_amount' => 0, // Can be added from order data
                'currency' => 'USD',
                'notes' => 'Invoice generated after successful payment',
            ];

            $result = $invoiceService->createAndSendInvoice($order, $invoiceData);

            if ($result) {
                Log::info('Invoice created and queued successfully for order: ' . $order->id);
            } else {
                Log::warning('Invoice creation completed with warnings for order: ' . $order->id);
            }

        } catch (\Throwable $e) {
            Log::error('Failed to create invoice for order: ' . $order->id . ' - ' . $e->getMessage());
            // Log error but don't fail the payment request
        }

        return response()->json(['message' => 'Payment successful, order created']);
    }

    /**
     * Handle payment cancel
     */
    public function cancel(Request $request)
    {
        return response()->json(['message' => 'Payment cancelled']);
    }
}
