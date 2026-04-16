<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تحقق من بريدك الإلكتروني</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            direction: rtl;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #007bff;
            color: #ffffff;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px;
            text-align: right;
            line-height: 1.6;
        }
        .content p {
            color: #333;
            margin: 10px 0;
        }
        .verification-button {
            display: inline-block;
            margin: 30px 0;
            padding: 12px 30px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }
        .verification-button:hover {
            background-color: #0056b3;
        }
        .footer {
            background-color: #f9f9f9;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 12px;
            border-top: 1px solid #ddd;
        }
        .link-text {
            color: #007bff;
            word-break: break-all;
            font-size: 12px;
        }
        .note {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>تحقق من بريدك الإلكتروني</h1>
        </div>
        
        <div class="content">
            <p>مرحباً <strong>{{ $user->name }}</strong>،</p>
            
            <p>شكراً لتسجيلك معنا! لاستكمال عملية التسجيل، يرجى تأكيد بريدك الإلكتروني بالضغط على الزر أدناه:</p>
            
            <div style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="verification-button">تأكيد البريد الإلكتروني</a>
            </div>
            
            <p>أو انسخ والصق هذا الرابط في متصفحك:</p>
            <p class="link-text">{{ $verificationUrl }}</p>
            
            <div class="note">
                <strong>ملاحظة هامة:</strong> هذا الرابط صالح لمدة 24 ساعة فقط. بعد انقضاء هذه المدة، ستحتاج إلى التسجيل مرة أخرى.
            </div>
            
            <p>إذا لم تقم بتسجيل هذا الحساب، يرجى تجاهل هذا البريد الإلكتروني.</p>
            
            <p>مع أطيب التحيات،<br>
            فريق التطبيق</p>
        </div>
        
        <div class="footer">
            <p>هذا البريد الإلكتروني تم إرساله تلقائياً. يرجى عدم الرد على هذا البريد.</p>
            <p>&copy; 2026 جميع الحقوق محفوظة</p>
        </div>
    </div>
</body>
</html>
