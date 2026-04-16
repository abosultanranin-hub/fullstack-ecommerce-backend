<!DOCTYPE html>
<html>
<head>
    <title>فاتورة طلبك</title>
</head>
<body style="font-family: Arial, sans-serif; direction: rtl; text-align: right;">

    <h2>مرحبًا {{ $user->name }}،</h2>

    <p>شكرًا لتسوقك معنا. تجد مرفقًا فاتورة طلبك رقم <strong>{{ $order->number }}</strong>.</p>

    <p>إذا كان لديك أي استفسار، لا تتردد في التواصل معنا.</p>

    <br>
    <p>مع تحياتنا،<br>فريق Saja Store</p>

</body>
</html>
