<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
  use Illuminate\Support\Facades\Crypt;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Database\Eloquent\Builder;
class Carts extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'carts';

    protected $fillable = [
        'id', 'cookie_id', 'user_id', 'product_id', 'quantity', 'options',
    ];

    protected $casts = [
        'options' => 'array',
    ];

   /* protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
            if (!$model->cookie_id) {
                $model->cookie_id = self::getCookieId();
            }
        });
    }

  // protected static function booted()
   // {
     //   static::addGlobalScope('cookie_id', function (Builder $builder) {
      //      $builder->where('cookie_id', self::getCookieId());
      //  });
   // }

    protected static function getCookieId(): string
    {
        $cookie = Cookie::get('cart_cookie_id');
        if (!$cookie) {
            $cookie = (string) Str::uuid();
            Cookie::queue('cart_cookie_id', $cookie, 60 * 24 * 30);
        }
        return $cookie;
    }*/

    public function products()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'Anonymous',
        ]);
    }
 public static function savec()
    {
     $cookieName = 'ranin';
    $cookieId = \Str::uuid();
    $cookie = cookie($cookieName, $cookieId, 60 * 24 * 30); // بالدقائق
    
    return response('تم تخزين الكوكي')->cookie($cookie);
    }







public static function getCartCookieId()
{
    $cookieName = 'carts_id';
    
    $cookie = Cookie::get($cookieName);
    if (!$cookie) {
        $cookie = (string) Str::uuid();
        Cookie::make($cookieName, $cookie,30*60*60 ); // 30 يوم
    }
    return $cookie;
}

//  // إنشاء أو جلب الكوكيز
//   public static function getCartCookieId(Request $request)
// {
//     $cookieName = 'cartapi_cookie_id';

//     if ($request->hasCookie($cookieName)) {
//         return $request->cookie($cookieName);
//     }

//     $cookieId = Str::uuid()->toString();

//     Cookie::queue(cookie(
//         $cookieName,    // اسم الكوكي
//         $cookieId,      // القيمة (UUID)
//         60*24*30,       // مدة 30 يوم
//         '/',             // path
//         null,            // domain
//         false,           // secure
//         false            // httpOnly
//     ));

//     return $cookieId;
// }

//     // جلب محتوى السلة
//     public static function getCartItems(Request $request)
//     {
//         $cookieId = self::getCartCookieId($request);
//         $cart = self::where('cookie_id', $cookieId)->get();
//         $totalItems = $cart->sum('quantity');

//         return [
//             'cart' => $cart,
//             'totalItems' => $totalItems,
//         ];
//     }
// }
}