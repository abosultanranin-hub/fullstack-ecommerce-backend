<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Products;
use Carbon\Carbon;

class CartApi extends Model
{
    use HasFactory;

    /**
     * اسم الجدول (اختياري لكن واضح)
     */
    protected $table = 'cart_apis';

    /**
     * الحقول القابلة للـ mass assignment
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'options',
        'price',
        'abandoned_at',
        'abandoned_email_sent',
        'is_checked_out',
    ];

    /**
     * تحويل JSON تلقائيًا
     */
    protected $casts = [
        'options' => 'array',
        'abandoned_at' => 'datetime',
        'abandoned_email_sent' => 'boolean',
        'is_checked_out' => 'boolean',
        'updated_at' => 'datetime',
    ];

    /**
     * العلاقات
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Products::class);
    }
     public function isAbandonedAfterDays(int $days = 7): bool
    {
        if ($this->is_checked_out) {
            return false;
        }

        if ($this->abandoned_email_sent) {
            return false;
        }

        return $this->updated_at->lt(
            Carbon::now()->subDays($days)
        );
    }

    /**
     * مرّ عليها أسبوع ولم يتم الدفع ولم يُرسل إيميل
     */
    public function isAbandoned(int $days = 7): bool
    {
        return
            $this->is_checked_out == 0 &&
            $this->abandoned_email_sent == 0 &&
            $this->updated_at->diffInDays(now()) >= $days;
    }

    /**
     * تعليم أن إيميل الإهمال تم إرساله
     */
    public function markAbandonedEmailSent(): void
    {
        $this->update([
            'abandoned_email_sent' => true,
            'abandoned_at' => now(),
        ]);
    }
}
