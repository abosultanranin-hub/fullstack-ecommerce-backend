@php
/**
 * سكريبت اختبار بسيط وسريع لنظام الفواتير
 * 
 * الاستخدام:
 * php artisan tinker
 * include 'quick_test.php'
 */

echo "\n";
echo "╔════════════════════════════════════════════════════════╗\n";
echo "║        🧪 اختبار سريع لنظام الفواتير والبريد         ║\n";
echo "╚════════════════════════════════════════════════════════╝\n";
echo "\n";

// ====== الخطوة 1: التحقق من المستخدم ======
echo "1️⃣  التحقق من وجود مستخدم...\n";
$user = \App\Models\User::first();
if (!$user) {
    echo "❌ لا توجد مستخدمين\n";
    exit;
}
echo "   ✅ المستخدم: " . $user->name . " (" . $user->email . ")\n\n";

// ====== الخطوة 2: إنشاء طلب ======
echo "2️⃣  إنشاء طلب اختبار...\n";
$order = \App\Models\orders::create([
    'user_id' => $user->id,
    'number' => 'TEST-' . substr(\Illuminate\Support\Str::uuid(), 0, 8),
    'status' => 'completed',
    'payment_status' => 'paid',
    'payment_method' => 'test',
]);
echo "   ✅ رقم الطلب: " . $order->number . "\n\n";

// ====== الخطوة 3: إضافة عناصر ======
echo "3️⃣  إضافة عناصر للطلب...\n";
$item = \App\Models\order_items::create([
    'order_id' => $order->id,
    'product_id' => 1,
    'product_name' => 'منتج اختبار',
    'price' => 99.99,
    'quantity' => 2,
]);
echo "   ✅ تم إضافة: منتج اختبار x2 = \$199.98\n\n";

// ====== الخطوة 4: إنشاء الفاتورة ======
echo "4️⃣  إنشاء الفاتورة...\n";
try {
    $service = new \App\Services\InvoiceService();
    $result = $service->createAndSendInvoice($order, [
        'subtotal' => 199.98,
        'tax_amount' => 0,
        'shipping_amount' => 0,
        'discount_amount' => 0,
    ]);
    echo "   ✅ تم إنشاء الفاتورة بنجاح\n\n";
} catch (\Exception $e) {
    echo "   ❌ خطأ: " . $e->getMessage() . "\n\n";
}

// ====== الخطوة 5: عرض النتائج ======
echo "5️⃣  النتائج:\n";
$invoice = \App\Models\Invoice::where('order_id', $order->id)->first();
if ($invoice) {
    echo "   📄 رقم الفاتورة: " . $invoice->invoice_number . "\n";
    echo "   📊 الحالة: " . $invoice->status . "\n";
    echo "   💰 المبلغ: \$" . $invoice->total_amount . "\n";
    echo "   📧 البريد: " . $invoice->user->email . "\n";
    echo "   ⏰ الوقت: " . $invoice->created_at . "\n";
} else {
    echo "   ❌ لم يتم إنشاء الفاتورة\n";
}

// ====== الخطوة 6: فحص Queue ======
echo "\n6️⃣  فحص Queue:\n";
$jobsCount = \DB::table('jobs')->count();
$failedCount = \DB::table('failed_jobs')->count();
echo "   📬 رسائل في الانتظار: " . $jobsCount . "\n";
echo "   ❌ رسائل فاشلة: " . $failedCount . "\n";
if ($jobsCount > 0) {
    echo "   ℹ️  شغّل: php artisan queue:work\n";
}

echo "\n";
echo "╔════════════════════════════════════════════════════════╗\n";
echo "║                   ✅ الاختبار اكتمل                      ║\n";
echo "╚════════════════════════════════════════════════════════╝\n";
echo "\n";
@endphp
