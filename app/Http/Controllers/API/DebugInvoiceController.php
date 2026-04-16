<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\orders;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DebugInvoiceController extends Controller
{
    protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * اختبار إنشاء فاتورة يدوياً
     */
    public function testCreateInvoice($orderId)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // الحصول على الطلب
            $order = orders::find($orderId);
            if (!$order || $order->user_id !== $user->id) {
                return response()->json(['error' => 'الطلب غير موجود'], 404);
            }

            Log::info('=== اختبار إنشاء الفاتورة ===');
            Log::info('معرف الطلب: ' . $order->id);
            Log::info('رقم الطلب: ' . $order->number);
            Log::info('عدد عناصر الطلب: ' . $order->orderItems()->count());

            // محاولة إنشاء الفاتورة
            $invoiceData = [
                'subtotal' => $order->orderItems()->sum(\DB::raw('price * quantity')),
                'tax_amount' => 0,
                'shipping_amount' => 0,
                'discount_amount' => 0,
                'currency' => 'USD',
                'notes' => 'فاتورة اختبار',
            ];

            Log::info('بيانات الفاتورة: ' . json_encode($invoiceData));

            $result = $this->invoiceService->createAndSendInvoice($order, $invoiceData);

            return response()->json([
                'success' => $result,
                'message' => $result ? 'تم إنشاء الفاتورة بنجاح' : 'فشل إنشاء الفاتورة',
                'order' => [
                    'id' => $order->id,
                    'number' => $order->number,
                    'items_count' => $order->orderItems()->count(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('خطأ في اختبار الفاتورة: ' . $e->getMessage());
            Log::error('السبب: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }

    /**
     * التحقق من حالة الطلب والفاتورة
     */
    public function checkOrderStatus($orderId)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $order = orders::with(['orderItems', 'user'])->find($orderId);
            if (!$order || $order->user_id !== $user->id) {
                return response()->json(['error' => 'الطلب غير موجود'], 404);
            }

            $invoice = Invoice::where('order_id', $orderId)->first();

            return response()->json([
                'order' => [
                    'id' => $order->id,
                    'number' => $order->number,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'payment_method' => $order->payment_method,
                    'items_count' => $order->orderItems()->count(),
                    'subtotal' => $order->orderItems()->sum(\DB::raw('price * quantity')),
                ],
                'invoice' => $invoice ? [
                    'id' => $invoice->id,
                    'number' => $invoice->invoice_number,
                    'status' => $invoice->status,
                    'total_amount' => $invoice->total_amount,
                    'pdf_path' => $invoice->pdf_path ? '✅ موجود' : '❌ غير موجود',
                    'sent_at' => $invoice->sent_at,
                    'viewed_at' => $invoice->viewed_at,
                    'paid_at' => $invoice->paid_at,
                ] : '❌ لا توجد فاتورة',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('خطأ في التحقق من الطلب: ' . $e->getMessage());

            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * عرض السجلات الأخيرة
     */
    public function getLogs()
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->hasRole('admin')) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $logFile = storage_path('logs/laravel.log');
            if (!file_exists($logFile)) {
                return response()->json(['error' => 'ملف السجل غير موجود'], 404);
            }

            // قراءة آخر 100 سطر
            $lines = array_slice(file($logFile), -100);
            $logs = array_reverse($lines);

            return response()->json([
                'logs' => $logs,
                'file' => $logFile,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
