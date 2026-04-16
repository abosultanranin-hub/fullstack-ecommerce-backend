<?php

namespace App\Repositories\cart;

use App\Models\Cart;
use App\Models\Products;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
//implements CartRepository
class cartRepository 
{
    public function get(): Collection
    {
        return Cart::with('products')->get(); // Global Scope سيُطبّق تلقائيًا
    }

    public function add(Products $product, int $quantity = 1): Cart
    {
        $user_id = null;
        $cookie_id = $this->getCookieId();
    
        $cartItem = Carts::where('product_id', $product->id)
                        ->first();
    
        if ($cartItem) {
            // تحديث الكمية
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            // إضافة سجل جديد
            $cartItem = Carts::create([
                'id'         => Str::uuid(),
                'cookie_id'  => '9kjh99',
                'product_id' => $product->id,
                'quantity'   => $quantity,
                'user_id'    => $user_id,
            ]);
        }
    
        return $cartItem;
    }
    

    public function update($quantity, $id): void
    {
        Carts::where('product_id', $id)->update([
            'quantity' => $quantity,
        ]);
    }

    public function delete($id): void
    {
        Cart::where('product_id', $id)->delete();
    }

    public function empty(): void
    {
        Cart::query()->delete(); // Global Scope سيحذف فقط سلة المستخدم الحالي
    }

    public function total(): float
    {
        return (float) Cart::join('products', 'products.id', '=', 'carts.product_id')
            ->selectRaw('SUM(products.price * carts.quantity) as total')
            ->value('total') ?? 0;
    }
    function getCookieId()
    {
        // اسم الكوكي اللي بتستخدمه لتخزين المعرف
        $cookieName = 'cart_cookie_id';
    
        // إذا الكوكي موجود، رجع قيمته
        if (request()->hasCookie($cookieName)) {
            return request()->cookie($cookieName);
        }
    
        // إذا مش موجود، أنشئ معرف عشوائي (UUID مثلا)
        $cookieId = (string) Str::uuid();
    
        // خلي الكوكي يطلع مع الرد (مدة الصلاحية مثلاً 30 يوم)
        cookie()->queue(cookie($cookieName, $cookieId, 60 * 24 * 30)); // 30 يوم
    
        return $cookieId;
    }
    
}
