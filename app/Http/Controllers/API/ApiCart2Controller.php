<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CartApi;
use App\Models\Products;

use Illuminate\Support\Facades\Auth;

class ApiCart2Controller extends Controller
{
    /**
     * عرض محتوى السلة
     */
    public function index(Request $request)
    {
        $cart = CartApi::with('product')
            ->where('is_checked_out', 0)
            ->when($request->user(), function ($query, $user) {
                return $query->where('user_id', $user->id);
            })
            ->get();

        return response()->json([
            'items' => $cart
        ]);
    }

    /**
     * إضافة منتج للسلة
     */
   public function add(Request $request)
{
    $user = $request->user();

    if (!$user) {
        return response()->json([
            'message' => 'Unauthenticated'
        ], 401);
    }

    $request->validate([
        'product_id' => 'required|exists:products,id',
        'quantity'   => 'nullable|integer|min:1',
    ]);

    $quantity = $request->quantity ?? 1;

    $product = Products::findOrFail($request->product_id);

    $cartItem = CartApi::where('user_id', $user->id)
        ->where('product_id', $request->product_id)
        ->where('is_checked_out', 0)
        ->first();

    if ($cartItem) {
        $cartItem->quantity += $quantity;
        $cartItem->price = $product->price;
        $cartItem->save();
    } else {
        CartApi::create([
            'user_id'    => $user->id,
            'product_id' => $request->product_id,
            'quantity'   => $quantity,
            'price'      => $product->price,
        ]);
    }

    return response()->json([
        'items' => CartApi::with('product')
            ->where('user_id', $user->id)
            ->where('is_checked_out', 0)
            ->get()
    ]);
}


    public function update(Request $request, $id)  // ✅ استخدم $id (مش $productId)
    {
        // التحقق من تسجيل الدخول
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'يجب تسجيل الدخول'
            ], 401);
        }

        // التحقق من صحة البيانات
        $request->validate([
            'quantity' => 'required|integer|min:1|max:999'
        ]);

        try {
            // ✅ البحث بـ id (primary key) مش product_id
            $cartItem = CartApi::where('user_id', Auth::id())
                              ->where('id', $id)
                              ->where('is_checked_out', 0) // ✅ التأكد من أن المنتج غير مدفوع
                              ->firstOrFail();

            $cartItem->quantity = $request->quantity;
            $cartItem->save();

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الكمية بنجاح',
                'data' => $cartItem
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'المنتج غير موجود في السلة'
            ], 404);
        }
    }


    /**
     * حذف منتج من السلة
     */
    public function remove(Request $request, $id)
    {
        CartApi::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->where('is_checked_out', 0)
            ->delete();

        return $this->index($request);
    }

    /**
     * تفريغ السلة بالكامل
     */
    public function clear(Request $request)
    {
        CartApi::where('user_id', $request->user()->id)
            ->where('is_checked_out', 0)
            ->delete();

        return response()->json([
            'message' => 'Cart cleared successfully',
            'items'   => []
        ]);
    }
}
