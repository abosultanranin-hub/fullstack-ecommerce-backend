<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use App\Models\Carts;
use App\Models\Products;
use App\Models\Order;
use Illuminate\Support\Facades\Gate;
use App\Http\Middleware\EncryptCookies
;
class CartController extends Controller
{
    // عرض صفحة السلة فقط (الـ View)
    public function view()
    {
      
         
$cookieId = $this->getCookieId();
             $items= \App\Models\Carts::where('cookie_id', $cookieId)
                     ->get();
return view('cart.viewcart', ['items' =>  $items]);


   
    }
   
    
    // عرض تفاصيل السلة بصيغة JSON (يتم استدعاؤه من AJAX)
    public function details()
    {
        $items = Carts::with('products')->get();
    
        return response()->json(['items' => $items]);
    }


/*

*/  // حذف منتج من السلة عبر AJAX
    public function remove(Request $request)
{
    $request->validate([
        'id' => 'required|exists:carts,product_id' // تحقق من وجود product_id في جدول carts
    ]);

    Carts::where('product_id', $request->id)->delete();

    return response()->json(['success' => true]);
}

    // تفريغ السلة بالكامل (غير مستخدمة في الواجهة الحالية لكن تبقى مفيدة)
    public function empty()
    {
        $this->cart->empty();
        return redirect()->route('cart.view')->with('success', 'تم تفريغ السلة');
    }




public function add(Request $request, $id)
{
    $isNewItem = false;

    try {
        // جلب معرف السلة من الكوكي
        $cookieId = $this->getCartCookieId();

        // التحقق من وجود المنتج في السلة
        $cartItem = Carts::where('product_id', $id) // استخدم $id بدلاً من $request->id
                        ->where('cookie_id', $cookieId)
                        ->first();

        if ($cartItem) {
            // تحديث الكمية
            $cartItem->increment('quantity', $request->quantity); // استخدم increment بدلاً من +=
        } else {
            // إضافة منتج جديد للسلة
            Carts::create([
                'id'         => Str::uuid(),
                'cookie_id'  => $cookieId,
                'product_id' => $id, // استخدم $id بدلاً من $request->id
                'quantity'   => $request->quantity ?? 1, // قيمة افتراضية 1 إذا لم يتم الإرسال
                'user_id'    => auth()->check() ? auth()->id() : null,
            ]);
            $isNewItem = true;
        }

        // حساب العدد الإجمالي لعناصر السلة
        $totalCount = Carts::where('cookie_id', $cookieId)->count(); // استخدم sum بدلاً من count

       return response()->json([
    'success'     => true,
    'new_item'    => $isNewItem,
    'total_count' => $totalCount,
    'message'     => 'تمت إضافة المنتج إلى السلة'
])->cookie(
    'carts_id',      // اسم الكوكي
    $cookieId,       // قيمة الكوكي
    30*24*60,        // المدة بالدقائق (30 يوم)
    '/',             // المسار (اختياري)
    null,            // الدومين (اختياري)
    false,           // Secure (اختياري)
    false,           // HttpOnly (اختياري)
    'lax'            // SameSite (اختياري)
);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء إضافة المنتج إلى السلة',
            'error' => $e->getMessage() // للتطوير فقط
        ], 500);
    }
}
   

 


public function getCartCookieId()
{
    $cookieName = 'carts_id';
    
    $cookie = Cookie::get($cookieName);
    if (!$cookie) {
        $cookie = (string) Str::uuid();
        Cookie::make($cookieName, $cookie,30*60*60 ); // 30 يوم
    }
    return $cookie;
}

 
    }

 