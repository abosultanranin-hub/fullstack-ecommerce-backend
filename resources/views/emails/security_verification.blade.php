<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تأكيد تسجيل الدخول</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-top: 5px solid #0275d8;">
        <h2>تأكيد محاولة تسجيل الدخول</h2>
        <p>مرحباً،</p>
        <p>نحاول التأكد من هويتك لأنك تقوم بتسجيل الدخول من جهاز أو موقع جديد:</p>
        <ul style="background: #f9f9f9; padding: 15px; border-radius: 5px; list-style: none;">
            <li><strong>عنوان IP:</strong> {{ $ip }}</li>
            <li><strong>الدولة:</strong> {{ $country ?? 'غير معروفة' }}</li>
            <li><strong>الوقت:</strong> {{ now()->toDateTimeString() }}</li>
        </ul>
        
        <p>لتأكيد هذا الدخول والسماح للجهاز بالوصول إلى حسابك، يرجى الضغط على الزر أدناه:</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('security.verify', ['token' => $token]) }}" 
               style="background-color: #0275d8; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                تأكيد هويتي والمتابعة
            </a>
        </div>

        <p style="font-size: 0.9em; color: #666;">لمدة صلاحية هذا الرابط هي 15 دقيقة فقط.</p>
        
        <p style="background-color: #fcf8e3; padding: 10px; border: 1px solid #faebcc; color: #8a6d3b;">
            إذا لم تكن أنت من قام بمحاولة تسجيل الدخول هذه، فنوصيك بتغيير كلمة المرور فوراً.
        </p>
        
        <p>شكراً لك،<br>فريق الأمان</p>
    </div>
</body>
</html>
