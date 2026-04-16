<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة رقم {{ $invoice->invoice_number }}</title>
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
            background-color: #28a745;
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
        .table-responsive {
            margin: 20px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            padding: 12px;
            text-align: right;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #555;
        }
        .details-section {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .details-section h5 {
            margin-top: 0;
            color: #28a745;
            border-bottom: 2px solid #28a745;
            padding-bottom: 10px;
        }
        .totals {
            margin-top: 15px;
        }
        .totals p {
            margin: 5px 0;
        }
        .total-amount {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
            border-top: 2px solid #28a745;
            padding-top: 10px;
            margin-top: 10px;
        }
        .footer {
            background-color: #f9f9f9;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 12px;
            border-top: 1px solid #ddd;
            margin-top: 20px;
        }
        .note {
            background-color: #d4edda;
            border-right: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>فاتورة رقم {{ $invoice->invoice_number }}</h1>
        </div>
        
        <div class="content">
            <p>عزيزي <strong>{{ $user->name }}</strong>،</p>
            <p>شكراً لك على طلبك. إليك فاتورة طلبك:</p>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>المنتج</th>
                            <th>الكمية</th>
                            <th>السعر</th>
                            <th>المجموع</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>${{ number_format($item->price, 2) }}</td>
                            <td>${{ number_format($item->price * $item->quantity, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="details-section">
                <h5>تفاصيل الفاتورة:</h5>
                <p><strong>رقم الفاتورة:</strong> {{ $invoice->invoice_number }}</p>
                <p><strong>تاريخ الفاتورة:</strong> {{ $invoice->created_at->format('Y-m-d') }}</p>
                <p><strong>حالة الدفع:</strong> {{ $invoice->status }}</p>
            </div>

            <div class="details-section totals">
                <h5>المجاميع:</h5>
                <p><strong>المبلغ الأساسي:</strong> ${{ number_format($invoice->subtotal, 2) }}</p>
                <p><strong>الضرائب:</strong> ${{ number_format($invoice->tax_amount, 2) }}</p>
                <p><strong>الشحن:</strong> ${{ number_format($invoice->shipping_amount, 2) }}</p>
                <p><strong>الخصم:</strong> ${{ number_format($invoice->discount_amount, 2) }}</p>
                <p class="total-amount"><strong>المجموع الكلي:</strong> ${{ number_format($invoice->total_amount, 2) }}</p>
            </div>

            <div class="note">
                <strong>ملاحظة:</strong> تم إرفاق ملف PDF للفاتورة مع هذا البريد الإلكتروني.
            </div>

            <p>شكراً لك على التسوق معنا!</p>
        </div>
        
        <div class="footer">
            <p>هذا البريد الإلكتروني تم إرساله تلقائياً. يرجى عدم الرد على هذا البريد.</p>
            <p>&copy; 2026 جميع الحقوق محفوظة</p>
        </div>
    </div>
</body>
