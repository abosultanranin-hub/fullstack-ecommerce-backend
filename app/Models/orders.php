<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Products;
 use App\Models\OrderAddress;
 use Illuminate\Database\Eloquent\Relations\BelongsTo;
 use Illuminate\Database\Eloquent\Relations\HasMany;
class orders extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'user_id',
        'number',
        'status',
        'payment_status',
        'payment_method',
    ];
 public function products()
    {
        return $this->belongsToMany(Products::class, 'order_items')
                    ->withPivot(['quantity', 'price', 'totall']);
    }
    
    /**
     * الحصول على عناصر الطلب
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(order_items::class, 'order_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(order_items::class, 'order_id');
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'name'=>'Guast'
        ]);
    }
    // ✅ علاقة مع العناوين
    public function addresses()
    {
        return $this->hasMany(OrderAddress::class, 'order_id');
    }
}
