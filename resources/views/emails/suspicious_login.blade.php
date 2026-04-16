<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تنبيه أمني</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-top: 5px solid #d9534f;">
        <h2>تنبيه أمني: تسجيل دخول مشبوه</h2>
        <p>مرحباً،</p>
        <p>تم اكتشاف عملية تسجيل دخول إلى حسابك من موقع أو جهاز جديد:</p>
        <ul>
            <li><strong>عنوان IP:</strong> {{ $ip }}</li>
            <li><strong>الدولة:</strong> {{ $country ?? 'غير معروفة' }}</li>
            <li><strong>الوقت:</strong> {{ now()->toDateTimeString() }}</li>
        </ul>
        <p style="background-color: #fcf8e3; padding: 10px; border: 1px solid #faebcc; color: #8a6d3b;">
            إذا كنت أنت من قام بهذا الإجراء، يمكنك تجاهل هذا البريد. 
            <strong>أما إذا لم تكن أنت، فنوصيك بتغيير كلمة المرور فوراً لتأمين حسابك.</strong>
        </p>
        <p>شكراً لك،<br>فريق الأمان</p>
    </div>
</body>
</html>
