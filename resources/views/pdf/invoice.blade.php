<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>فاتورة #{{ $order->number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif; /* Supports Arabic if configured, otherwise use standard fonts */
            direction: rtl;
            text-align: right;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .order-details, .user-details {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: right;
        }
        th {
            background-color: #f4f4f4;
        }
        .total {
            margin-top: 20px;
            text-align: left;
            font-size: 1.2em;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>فاتورة طلب</h2>
        <p>Saja Store</p>
    </div>

    <div class="user-details">
        <strong>العميل:</strong> {{ $order->user->name ?? 'N/A' }}<br>
        <strong>البريد الإلكتروني:</strong> {{ $order->user->email ?? 'N/A' }}
    </div>

    <div class="order-details">
        <strong>رقم الطلب:</strong> {{ $order->number }}<br>
        <strong>تاريخ الطلب:</strong> {{ $order->created_at->format('Y-m-d H:i') }}<br>
        <strong>حالة الدفع:</strong> {{ $order->payment_status }}
    </div>

    <table>
        <thead>
            <tr>
                <th>المنتج</th>
                <th>الكمية</th>
                <th>السعر</th>
                <th>الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product_name ?? $item->product->name ?? 'منتج' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>${{ number_format($item->price, 2) }}</td>
                <td>${{ number_format($item->price * $item->quantity, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        الإجمالي الكلي: ${{ number_format($order->items->sum(function($item){ return $item->price * $item->quantity; }), 2) }}
    </div>

    <br>
    <p style="text-align: center;">شكرًا لتسوقك معنا!</p>

</body>
</html>
