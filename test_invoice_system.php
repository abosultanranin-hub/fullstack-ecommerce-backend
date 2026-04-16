<?php
/**
 * سكريبت اختبار شامل لنظام الفواتير والطلبات والبريد الإلكتروني
 * 
 * الاستخدام:
 * php artisan tinker
 * include 'test_invoice_system.php'
 */

use App\Models\{orders, Invoice, CartApi, Products, User};
use App\Services\InvoiceService;
use Illuminate\Support\Facades\Log;

// ════════════════════════════════════════════════
// 1. التحقق من البيانات الأساسية
// ════════════════════════════════════════════════
echo "\n";
echo "════════════════════════════════════════════════\n";
echo "🧪 اختبار نظام الفواتير والطلبات\n";
echo "════════════════════════════════════════════════\n";

// التحقق من وجود مستخدم
$user = User::first();
if (!$user) {
    echo "❌ لا توجد مستخدمين في قاعدة البيانات\n";
    exit;
}
echo "✅ المستخدم الأول: " . $user->name . " (" . $user->email . ")\n";

// ════════════════════════════════════════════════
// 2. اختبار إنشاء طلب جديد
// ════════════════════════════════════════════════
echo "\n--- إنشاء طلب اختبار ---\n";

$order = orders::create([
    'user_id' => $user->id,
    'number' => 'TEST-ORD-' . \Illuminate\Support\Str::uuid(),
    'status' => 'completed',
    'payment_status' => 'paid',
    'payment_method' => 'test',
]);

if ($order) {
    echo "✅ تم إنشاء طلب اختبار:\n";
    echo "   - معرف الطلب: " . $order->id . "\n";
    echo "   - رقم الطلب: " . $order->number . "\n";
} else {
    echo "❌ فشل إنشاء الطلب\n";
    exit;
}

// ════════════════════════════════════════════════
// 3. إضافة عناصر للطلب
// ════════════════════════════════════════════════
echo "\n--- إضافة عناصر للطلب ---\n";

$products = Products::limit(2)->get();
if ($products->isEmpty()) {
    echo "⚠️ لا توجد منتجات - سيتم إنشاء عنصر وهمي\n";
    $itemCount = 1;
    $item = \App\Models\order_items::create([
        'order_id' => $order->id,
        'product_id' => 1,
        'product_name' => 'منتج اختبار',
        'price' => 99.99,
        'quantity' => 2,
    ]);
    echo "✅ تم إضافة عنصر اختبار\n";
} else {
    $itemCount = 0;
    foreach ($products as $product) {
        $item = \App\Models\order_items::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'price' => $product->price ?? 99.99,
            'quantity' => 2,
        ]);
        if ($item) {
            $itemCount++;
            echo "✅ تمت إضافة: " . $product->name . "\n";
        }
    }
}
echo "   - إجمالي العناصر: " . $itemCount . "\n";

// ════════════════════════════════════════════════
// 4. اختبار إنشاء الفاتورة
// ════════════════════════════════════════════════
echo "\n--- اختبار إنشاء الفاتورة ---\n";

try {
    $invoiceService = new InvoiceService();
    
    $invoiceData = [
        'subtotal' => 99.99 * 2,
        'tax_amount' => 20,
        'shipping_amount' => 10,
        'discount_amount' => 5,
        'currency' => 'USD',
        'notes' => 'فاتورة اختبار',
    ];
    
    $result = $invoiceService->createAndSendInvoice($order, $invoiceData);
    
    if ($result) {
        echo "✅ نجحت عملية إنشاء الفاتورة!\n";
    } else {
        echo "⚠️ عملية الفاتورة اكتملت لكن مع تحفظات\n";
    }
} catch (\Exception $e) {
    echo "❌ خطأ في إنشاء الفاتورة:\n";
    echo "   - الرسالة: " . $e->getMessage() . "\n";
    echo "   - الملف: " . $e->getFile() . " (السطر: " . $e->getLine() . ")\n";
}

// ════════════════════════════════════════════════
// 5. عرض بيانات الفاتورة
// ════════════════════════════════════════════════
echo "\n--- بيانات الفاتورة ---\n";

$invoice = Invoice::where('order_id', $order->id)->first();
if ($invoice) {
    echo "معرف الفاتورة: " . $invoice->id . "\n";
    echo "رقم الفاتورة: " . $invoice->invoice_number . "\n";
    echo "الحالة: " . $invoice->status . "\n";
    echo "المبلغ الأساسي: $" . $invoice->subtotal . "\n";
    echo "الضرائب: $" . $invoice->tax_amount . "\n";
    echo "الشحن: $" . $invoice->shipping_amount . "\n";
    echo "الخصم: $" . $invoice->discount_amount . "\n";
    echo "المجموع: $" . $invoice->total_amount . "\n";
    echo "البريد: " . $invoice->user->email . "\n";
    echo "وقت الإنشاء: " . $invoice->created_at . "\n";
    echo "وقت الإضافة للـ Queue: " . ($invoice->queued_at ? $invoice->queued_at : 'لم يُضف بعد') . "\n";
    echo "وقت الإرسال: " . ($invoice->sent_at ? $invoice->sent_at : 'لم يُرسل بعد') . "\n";
} else {
    echo "❌ لم يتم العثور على فاتورة\n";
}

// ════════════════════════════════════════════════
// 6. فحص Queue
// ════════════════════════════════════════════════
echo "\n--- فحص حالة Queue ---\n";

$jobsCount = \DB::table('jobs')->count();
$failedCount = \DB::table('failed_jobs')->count();

echo "عدد الوظائف في Queue: " . $jobsCount . "\n";
echo "عدد الوظائف الفاشلة: " . $failedCount . "\n";

if ($jobsCount > 0) {
    echo "✅ هناك رسائل بريد في الانتظار - تأكد من تشغيل: php artisan queue:work\n";
} else {
    echo "⚠️ لا توجد رسائل في Queue - تحقق من QUEUE_CONNECTION في .env\n";
}

// ════════════════════════════════════════════════
// 7. فحص السجلات
// ════════════════════════════════════════════════
echo "\n--- آخر السجلات ---\n";

$logPath = storage_path('logs/laravel.log');
if (file_exists($logPath)) {
    $lines = array_reverse(file($logPath));
    $count = 0;
    foreach ($lines as $line) {
        if ($count >= 10) break;
        if (strpos($line, 'Invoice') !== false || strpos($line, 'الفاتورة') !== false) {
            echo trim($line) . "\n";
            $count++;
        }
    }
} else {
    echo "⚠️ لم يتم العثور على ملف السجلات\n";
}

// ════════════════════════════════════════════════
// الانتهاء
// ════════════════════════════════════════════════
echo "\n";
echo "════════════════════════════════════════════════\n";
echo "✅ انتهى الاختبار\n";
echo "════════════════════════════════════════════════\n";
echo "\nالخطوات التالية:\n";
echo "1. تأكد من تشغيل Queue Worker: php artisan queue:work\n";
echo "2. تحقق من البريد الوارد في Mailtrap: mailtrap.io\n";
echo "3. راقب السجلات: tail -f storage/logs/laravel.log\n";
echo "\n";
