<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة رقم {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            direction: rtl;
        }

        .container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            background-color: white;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        /* رأس الفاتورة */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
        }

        .company-info h1 {
            color: #007bff;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .company-info p {
            color: #666;
            font-size: 14px;
            margin: 5px 0;
        }

        .invoice-title {
            text-align: center;
        }

        .invoice-title h2 {
            font-size: 32px;
            color: #333;
            margin-bottom: 10px;
        }

        .invoice-number {
            font-size: 18px;
            color: #007bff;
            font-weight: bold;
        }

        /* معلومات العميل والفاتورة */
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            gap: 40px;
        }

        .info-box {
            flex: 1;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }

        .info-box h3 {
            font-size: 14px;
            color: #007bff;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .info-box p {
            font-size: 13px;
            color: #555;
            margin: 5px 0;
            line-height: 1.6;
        }

        /* جدول العناصر */
        .table-container {
            margin-bottom: 30px;
            overflow: hidden;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        thead {
            background-color: #007bff;
            color: white;
        }

        th {
            padding: 12px 15px;
            text-align: right;
            font-weight: bold;
        }

        td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }

        tbody tr:hover {
            background-color: #f9f9f9;
        }

        /* الملخص المالي */
        .summary-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 40px;
        }

        .summary-box {
            width: 40%;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .summary-row.total {
            border-top: 2px solid #007bff;
            padding-top: 12px;
            font-weight: bold;
            font-size: 16px;
            color: #007bff;
        }

        .summary-label {
            text-align: right;
        }

        .summary-value {
            text-align: left;
            font-weight: 600;
        }

        /* الملاحظات */
        .notes-section {
            padding: 20px;
            background-color: #fff3cd;
            border-right: 4px solid #ffc107;
            border-radius: 5px;
            margin-bottom: 30px;
        }

        .notes-section h3 {
            color: #856404;
            margin-bottom: 10px;
        }

        .notes-section p {
            color: #856404;
            font-size: 13px;
            line-height: 1.6;
        }

        /* التوقيع والتذييل */
        .footer-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }

        .signature-box {
            width: 30%;
            text-align: center;
            font-size: 13px;
            color: #666;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 10px;
        }

        .footer-text {
            text-align: center;
            font-size: 11px;
            color: #999;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }

        /* حالة الدفع */
        .payment-status {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-top: 10px;
        }

        .payment-status.paid {
            background-color: #d4edda;
            color: #155724;
        }

        .payment-status.pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .payment-status.cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* الطباعة */
        @media print {
            body {
                background-color: white;
            }

            .container {
                box-shadow: none;
                padding: 0;
            }

            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- رأس الفاتورة -->
        <div class="invoice-header">
            <div class="company-info">
                <h1>متجري</h1>
                <p>{{ config('app.name') }}</p>
                <p>البريد الإلكتروني: contact@ecommerce.com</p>
                <p>الهاتف: +966-50-000-0000</p>
            </div>
            <div class="invoice-title">
                <h2>فاتورة</h2>
                <div class="invoice-number">{{ $invoice->invoice_number }}</div>
            </div>
        </div>

        <!-- معلومات الفاتورة والعميل -->
        <div class="info-section">
            <div class="info-box">
                <h3>معلومات العميل</h3>
                <p><strong>الاسم:</strong> {{ $user->name ?? 'بدون اسم' }}</p>
                <p><strong>البريد الإلكتروني:</strong> {{ $user->email ?? 'N/A' }}</p>
                <p><strong>الهاتف:</strong> {{ $user->phone ?? 'N/A' }}</p>
                @if($order->addresses && $order->addresses->first())
                    @php $address = $order->addresses->where('type', 'billing')->first() ?? $order->addresses->first(); @endphp
                    <p><strong>العنوان:</strong> {{ $address->address ?? 'N/A' }}</p>
                    <p>{{ $address->city ?? '' }} {{ $address->state ?? '' }} {{ $address->postal_code ?? '' }}</p>
                @endif
            </div>
            
            <div class="info-box">
                <h3>تفاصيل الفاتورة</h3>
                <p><strong>رقم الطلب:</strong> {{ $order->number ?? 'N/A' }}</p>
                <p><strong>تاريخ الفاتورة:</strong> {{ $invoice->created_at->format('d/m/Y') }}</p>
                <p><strong>تاريخ الاستحقاق:</strong> {{ $invoice->created_at->addDays(30)->format('d/m/Y') }}</p>
                <p><strong>طريقة الدفع:</strong> {{ ucfirst($invoice->payment_method ?? 'Online') }}</p>
                <p>
                    <strong>حالة الدفع:</strong>
                    <span class="payment-status {{ $order->payment_status ?? 'pending' }}">
                        {{ $order->payment_status === 'paid' ? 'مدفوع' : 'قيد الانتظار' }}
                    </span>
                </p>
            </div>
        </div>

        <!-- جدول المنتجات -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم المنتج</th>
                        <th>السعر</th>
                        <th>الكمية</th>
                        <th>الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orderItems as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->product_name ?? $item->product->name ?? 'بدون اسم' }}</td>
                            <td>${{ number_format($item->price, 2) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>${{ number_format($item->price * $item->quantity, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #999;">لا توجد عناصر</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- الملخص المالي -->
        <div class="summary-section">
            <div class="summary-box">
                <div class="summary-row">
                    <span class="summary-label">المبلغ الأساسي:</span>
                    <span class="summary-value">${{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                
                @if($invoice->shipping_amount > 0)
                    <div class="summary-row">
                        <span class="summary-label">تكاليف الشحن:</span>
                        <span class="summary-value">${{ number_format($invoice->shipping_amount, 2) }}</span>
                    </div>
                @endif
                
                @if($invoice->tax_amount > 0)
                    <div class="summary-row">
                        <span class="summary-label">الضرائب:</span>
                        <span class="summary-value">${{ number_format($invoice->tax_amount, 2) }}</span>
                    </div>
                @endif
                
                @if($invoice->discount_amount > 0)
                    <div class="summary-row">
                        <span class="summary-label">الخصم:</span>
                        <span class="summary-value">-${{ number_format($invoice->discount_amount, 2) }}</span>
                    </div>
                @endif
                
                <div class="summary-row total">
                    <span class="summary-label">المجموع:</span>
                    <span class="summary-value">${{ number_format($invoice->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- الملاحظات -->
        @if($invoice->notes)
            <div class="notes-section">
                <h3>ملاحظات</h3>
                <p>{{ $invoice->notes }}</p>
            </div>
        @endif

        <!-- التوقيع والتذييل -->
        <div class="footer-section">
            <div class="signature-box">
                <p>ختم وتوقيع</p>
                <div class="signature-line"></div>
            </div>
            <div class="signature-box">
                <p>شكراً لك على تعاملك معنا</p>
            </div>
        </div>

        <!-- نصوص التذييل -->
        <div class="footer-text">
            <p>هذه فاتورة إلكترونية تم إنشاؤها بواسطة نظام متجري الإلكتروني</p>
            <p>للاستفسارات والشكاوي: support@ecommerce.com | الهاتف: +966-50-000-0000</p>
            <p>شروط الاستخدام والخصوصية متاحة على موقعنا الإلكتروني</p>
        </div>
    </div>
</body>
</html>
