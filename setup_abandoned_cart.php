<?php

use App\Models\CartApi;
use Carbon\Carbon;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// لا نجبر أي تغيير على إعدادات المايل هنا — نستخدم إعدادات البيئة (SMTP)
echo "Current Mail Driver (from config): " . Illuminate\Support\Facades\Config::get('mail.default') . "\n";


// البحث عن سلة غير مدفوعة
$cart = CartApi::where('is_checked_out', 0)->first();

if ($cart) {
    echo "جاري تحديث السلة رقم: {$cart->id} ...\n";

    // تعطيل التحديث التلقائي للوقت عشان نقدر نرجع التاريخ لورا
    $cart->timestamps = false;
    
    // تعديل البيانات لتكون "مهجورة"
    $cart->updated_at = Carbon::now()->subDays(8); // قبل 8 أيام
    $cart->abandoned_email_sent = 0; // لم يتم الإرسال بعد
    $cart->save();

    echo "تم تحديث السلة لتصبح 'مهجورة' (منذ 8 أيام).\n";
    echo "الآن يمكنك تشغيل الأمر:\n";
    echo "php artisan cart:check-abandoned\n";
} else {
    echo "لم يتم العثور على أي سلة نشطة لتجربتها.\n";
}
