<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'order_id',
        'user_id',
        'subtotal',
        'tax_amount',
        'shipping_amount',
        'discount_amount',
        'total_amount',
        'status',
        'currency',
        'payment_method',
        'notes',
        'pdf_path',
        'sent_at',
        'viewed_at',
        'paid_at',
        'queued_at',       // وقت إضافة البريد إلى Queue
        'failed_reason',   // سبب الفشل إن وجد
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'sent_at' => 'datetime',
        'viewed_at' => 'datetime',
        'paid_at' => 'datetime',
        'queued_at' => 'datetime',
    ];

    /**
     * العلاقة مع الطلب
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(orders::class, 'order_id');
    }

    /**
     * العلاقة مع المستخدم
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * توليد رقم فاتورة فريد
     */
    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . date('Y');
        $year = date('Y');

        // البحث عن أكبر رقم فاتورة لهذا العام
        $lastInvoice = self::where('invoice_number', 'like', $prefix . '-%')
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            // استخراج الرقم من آخر فاتورة
            $lastNumber = (int) substr($lastInvoice->invoice_number, -5);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * تحديث حالة الفاتورة
     */
    public function updateStatus(string $status): void
    {
        $statusMap = [
            'pending' => null,
            'sent' => 'sent_at',
            'viewed' => 'viewed_at',
            'paid' => 'paid_at',
        ];

        if (isset($statusMap[$status])) {
            $this->update(['status' => $status]);
            
            if ($statusMap[$status]) {
                $this->update([$statusMap[$status] => now()]);
            }
        }
    }

    /**
     * الحصول على عناصر الطلب
     */
    public function orderItems()
    {
        return $this->order->orderItems();
    }
}
