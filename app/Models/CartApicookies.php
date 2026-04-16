<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartApicookies extends Model
{
    use HasFactory;

    protected $table = 'cart_apicookies';
    
    protected $fillable = [
        'session_id',
        'product_id', 
        'quantity',
        'options',
        'product_data'
    ];

    protected $casts = [
        'options' => 'array',
        'product_data' => 'array'
    ];

    // دالة لإضافة أو تحديث عنصر في السلة
    public static function addOrUpdateItem($sessionId, $productId, $quantity, $options = null, $productData = null)
    {
        $cartItem = self::where('session_id', $sessionId)
                        ->where('product_id', $productId)
                        ->first();

        if ($cartItem) {
            // تحديث العنصر الموجود
            $cartItem->update([
                'quantity' => $quantity,
                'options' => $options,
                'product_data' => $productData
            ]);
        } else {
            // إضافة عنصر جديد
            $cartItem = self::create([
                'session_id' => $sessionId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'options' => $options,
                'product_data' => $productData
            ]);
        }

        return $cartItem;
    }

    // علاقة مع المنتج
    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}