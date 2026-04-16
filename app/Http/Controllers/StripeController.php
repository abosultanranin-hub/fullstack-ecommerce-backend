<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\CartApi;
use App\Models\orders;
use App\Models\order_items;
use App\Models\Invoice;
use App\Services\InvoiceService;

class StripeController extends Controller
{
    protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        // Set Stripe API key
        Stripe::setApiKey(config('services.stripe.secret'));
        $this->invoiceService = $invoiceService;
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

        // Get cart items for user
        $cartItems = CartApi::where('user_id', $user->id)->with('product')->get();
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
            'success_url' => url('/api/payment/success?session_id={CHECKOUT_SESSION_ID}'),
            'cancel_url' => url('/api/payment/cancel'),
            'metadata' => [
                'user_id' => $user->id,
            ],
        ]);

        return response()->json([
            'session_id' => $session->id,
            'url' => $session->url,
        ]);
    }

    /**
     * Handle payment success - Create order and invoice
     */
    public function success(Request $request)
    {
        try {
            $sessionId = $request->query('session_id');
            if (!$sessionId) {
                \Log::error('❌ Session ID مفقود');
                return response()->json(['error' => 'Session ID missing'], 400);
            }

            \Log::info('');
            \Log::info('════════════════════════════════════════════════');
            \Log::info('🎯 معالجة نجاح الدفع عبر Stripe');
            \Log::info('════════════════════════════════════════════════');
            \Log::info('معرف الجلسة: ' . $sessionId);

            // Retrieve session from Stripe
            $session = Session::retrieve($sessionId);
            \Log::info('✅ تم الحصول على بيانات جلسة Stripe');
            \Log::info('   - حالة الدفع: ' . $session->payment_status);

            if ($session->payment_status !== 'paid') {
                \Log::error('❌ حالة الدفع ليست مدفوعة: ' . $session->payment_status);
                return response()->json(['error' => 'Payment not completed'], 400);
            }

            $userId = $session->metadata['user_id'] ?? null;
            if (!$userId) {
                \Log::error('❌ User ID مفقود من metadata');
                return response()->json(['error' => 'User ID missing'], 400);
            }

            \Log::info('معرف المستخدم: ' . $userId);

            // Get cart items
            $cartItems = CartApi::where('user_id', $userId)->with('product')->get();
            
            if ($cartItems->isEmpty()) {
                \Log::warning('⚠️ السلة فارغة للمستخدم: ' . $userId);
                return response()->json(['error' => 'Cart is empty'], 400);
            }

            \Log::info('📦 عدد عناصر السلة: ' . $cartItems->count());

            // ════════════════════════════════════════════════
            // الخطوة 1: إنشاء الطلب
            // ════════════════════════════════════════════════
            \Log::info('');
            \Log::info('--- الخطوة 1: إنشاء الطلب ---');
            
            $order = orders::create([
                'user_id' => $userId,
                'number' => 'ORD-' . Str::uuid(),
                'status' => 'completed',
                'payment_status' => 'paid',
                'payment_method' => 'stripe',
            ]);

            if (!$order) {
                throw new \Exception('فشل إنشاء الطلب');
            }

            \Log::info('✅ تم إنشاء الطلب بنجاح');
            \Log::info('   - معرف الطلب: ' . $order->id);
            \Log::info('   - رقم الطلب: ' . $order->number);
            \Log::info('   - الحالة: ' . $order->status);

            // ════════════════════════════════════════════════
            // الخطوة 2: إنشاء عناصر الطلب
            // ════════════════════════════════════════════════
            \Log::info('');
            \Log::info('--- الخطوة 2: إنشاء عناصر الطلب ---');
            
            $subtotal = 0;
            $createdItems = 0;
            
            foreach ($cartItems as $item) {
                $itemPrice = $item->price ?? $item->product->price;
                if (!$itemPrice) {
                    \Log::warning('⚠️ السعر مفقود للمنتج: ' . $item->product_id);
                    continue;
                }
                
                $itemTotal = $itemPrice * $item->quantity;
                $subtotal += $itemTotal;

                $orderItem = order_items::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name ?? 'بدون اسم',
                    'price' => $itemPrice,
                    'quantity' => $item->quantity,
                ]);

                if ($orderItem) {
                    $createdItems++;
                    \Log::info('   ✅ ' . $item->product->name . ' x' . $item->quantity . ' = $' . $itemTotal);
                } else {
                    \Log::error('❌ فشل إنشاء عنصر الطلب للمنتج: ' . $item->product_id);
                }
            }

            \Log::info('✅ تم إنشاء ' . $createdItems . ' عنصر في الطلب');
            \Log::info('   - المبلغ الأساسي: $' . $subtotal);

            // ════════════════════════════════════════════════
            // الخطوة 3: إنشاء وإرسال الفاتورة
            // ════════════════════════════════════════════════
            \Log::info('');
            \Log::info('--- الخطوة 3: إنشاء وإرسال الفاتورة ---');
            
            try {
                $invoiceData = [
                    'subtotal' => $subtotal > 0 ? $subtotal : 0,
                    'tax_amount' => 0,
                    'shipping_amount' => 0,
                    'discount_amount' => 0,
                    'currency' => 'USD',
                    'notes' => 'الدفع عبر Stripe بنجاح - جلسة: ' . $sessionId,
                ];

                $result = $this->invoiceService->createAndSendInvoice($order, $invoiceData);
                
                if ($result) {
                    \Log::info('✅ تم إنشاء وإضافة الفاتورة إلى Queue بنجاح');
                } else {
                    \Log::warning('⚠️ فشل إنشاء الفاتورة للطلب: ' . $order->number);
                }
            } catch (\Exception $e) {
                \Log::error('❌ خطأ في عملية الفاتورة: ' . $e->getMessage());
                // لا نمنع إكمال الطلب إذا فشلت الفاتورة
            }

            // ════════════════════════════════════════════════
            // الخطوة 4: مسح السلة
            // ════════════════════════════════════════════════
            \Log::info('');
            \Log::info('--- الخطوة 4: مسح السلة ---');
            
            $deletedCount = CartApi::where('user_id', $userId)->delete();
            \Log::info('✅ تم حذف ' . $deletedCount . ' عنصر من السلة');

            // ════════════════════════════════════════════════
            // النهاية - الرد على العميل
            // ════════════════════════════════════════════════
            \Log::info('');
            \Log::info('════════════════════════════════════════════════');
            \Log::info('✅ اكتملت جميع خطوات معالجة الدفع بنجاح!');
            \Log::info('════════════════════════════════════════════════');
            \Log::info('ملخص الطلب:');
            \Log::info('  - رقم الطلب: ' . $order->number);
            \Log::info('  - عدد العناصر: ' . $createdItems);
            \Log::info('  - المبلغ الإجمالي: $' . $subtotal);
            \Log::info('  - طريقة الدفع: Stripe');
            \Log::info('════════════════════════════════════════════════');
            \Log::info('');

            return response()->json([
                'success' => true,
                'message' => 'تم الدفع بنجاح وإنشاء الطلب والفاتورة',
                'order_id' => $order->id,
                'order_number' => $order->number,
                'total_amount' => $subtotal,
                'items_count' => $createdItems,
            ]);
        } catch (\Exception $e) {
            \Log::error('');
            \Log::error('════════════════════════════════════════════════');
            \Log::error('❌ خطأ في معالجة نجاح الدفع');
            \Log::error('════════════════════════════════════════════════');
            \Log::error('الخطأ: ' . $e->getMessage());
            \Log::error('التفاصيل: ' . $e->getTraceAsString());
            \Log::error('════════════════════════════════════════════════');
            \Log::error('');
            
            return response()->json([
                'success' => false,
                'error' => 'خطأ في معالجة الدفع: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle payment cancel
     */
    public function cancel(Request $request)
    {
        return response()->json(['message' => 'تم إلغاء الدفع']);
    }
}

