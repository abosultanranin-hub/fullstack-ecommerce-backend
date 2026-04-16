<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use App\Models\CartApicookies;
use App\Models\Products;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CartControllerapi extends Controller
{
    /**
     * جلب بيانات السلة من قاعدة البيانات للـ session_id الحالي
     */
    public function getDBCartData(Request $request)
    {
        try {
            // جلب session_id من الكوكيز
            $sessionId = $request->cookie('session_id');
            
            // إذا لم يكن هناك session_id
            if (!$sessionId) {
                return response()->json([
                    'success' => true,
                    'message' => 'لا توجد جلسة نشطة',
                    'session_id' => null,
                    'cart_data' => [],
                    'total_items' => 0,
                    'total_price' => 0
                ]);
            }

            // جلب بيانات السلة من قاعدة البيانات
            $cartItems = CartApicookies::where('session_id', $sessionId)->get();

            // إذا لم توجد عناصر في السلة
            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'السلة فارغة',
                    'session_id' => $sessionId,
                    'cart_data' => [],
                    'total_items' => 0,
                    'total_price' => 0
                ]);
            }

            // تحضير بيانات السلة
            $cartData = [];
            $totalItems = 0;
            $totalPrice = 0;

            foreach ($cartItems as $item) {
                $itemPrice = $item->product_data['price'] ?? 0;
                $itemTotal = $itemPrice * $item->quantity;
                
                $cartData[] = [
                    'cart_item_id' => $item->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $itemPrice,
                    'total_price' => $itemTotal,
                    'product_data' => $item->product_data,
                    'added_at' => $item->created_at->toDateTimeString(),
                    'updated_at' => $item->updated_at->toDateTimeString()
                ];

                $totalItems += $item->quantity;
                $totalPrice += $itemTotal;
            }

            // الرد النهائي
            return response()->json([
                'success' => true,
                'message' => 'تم جلب بيانات السلة بنجاح',
                'session_id' => $sessionId,
                'cart_data' => $cartData,
                'summary' => [
                    'total_items' => $totalItems,
                    'total_products' => $cartItems->count(),
                    'total_price' => $totalPrice,
                    'cart_updated' => $cartItems->max('updated_at')->toDateTimeString()
                ]
            ]);

        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ في جلب بيانات السلة',
                'session_id' => $request->cookie('session_id'),
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * إضافة أو تحديث منتج في السلة
     */
    public function addToCart(Request $request, $productId)
    {
        try {
            $product = Products::find($productId);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'error' => 'المنتج غير موجود'
                ], 404);
            }

            $request->validate([
                'quantity' => 'sometimes|integer|min:1'
            ]);

            $quantity = $request->quantity ?? 1;
            
            // التأكد من وجود session_id أو إنشاء جديد
            $sessionId = $request->cookie('session_id');
            if (!$sessionId) {
                $sessionId = (string) Str::uuid();
            }

            $productData = [
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image ?? null,
                'description' => $product->description ?? null
            ];

            // حفظ في قاعدة البيانات
            $cartItem = CartApicookies::where('session_id', $sessionId)
                                    ->where('product_id', $productId)
                                    ->first();

            if ($cartItem) {
                $cartItem->quantity += $quantity;
                $cartItem->product_data = $productData;
                $cartItem->save();
            } else {
                $cartItem = CartApicookies::create([
                    'session_id' => $sessionId,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'product_data' => $productData
                ]);
            }

            // جلب السلة المحدثة من DB
            $updatedCartItems = CartApicookies::where('session_id', $sessionId)->get();
            $updatedCart = $updatedCartItems->map(function($item) {
                return [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'product_data' => $item->product_data,
                    'added_at' => $item->created_at->toDateTimeString()
                ];
            })->toArray();

            // الرد مع حفظ الكوكيز بشكل صحيح
            return response()->json([
                'success' => true,
                'message' => 'تمت إضافة المنتج إلى السلة',
                'session_id' => $sessionId,
                'cart' => $updatedCart,
                'db_items_count' => $updatedCartItems->count(),
                'total_items' => $updatedCartItems->sum('quantity')
            ])->withCookie(cookie('session_id', $sessionId, 60 * 24 * 365))
              ->withCookie(cookie('cart', json_encode($updatedCart), 60 * 24 * 365));

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ في إضافة المنتج إلى السلة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تحديث كمية المنتج
     */
    public function updateCartItem(Request $request, $productId)
    {
        try {
            $request->validate([
                'quantity' => 'required|integer|min:1'
            ]);

            $sessionId = $request->cookie('session_id');
            if (!$sessionId) {
                return response()->json([
                    'success' => false,
                    'error' => 'الجلسة غير موجودة'
                ], 404);
            }

            $item = CartApicookies::where('session_id', $sessionId)
                                ->where('product_id', $productId)
                                ->first();

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'error' => 'المنتج غير موجود في السلة'
                ], 404);
            }

            $item->quantity = $request->quantity;
            $item->save();

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث كمية المنتج بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ في تحديث الكمية'
            ], 500);
        }
    }

    /**
     * حذف منتج من السلة
     */
    public function removeFromCart(Request $request, $productId)
    {
        try {
            $sessionId = $request->cookie('session_id');
            if (!$sessionId) {
                return response()->json([
                    'success' => false,
                    'error' => 'الجلسة غير موجودة'
                ], 404);
            }

            $deleted = CartApicookies::where('session_id', $sessionId)
                                   ->where('product_id', $productId)
                                   ->delete();

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم حذف المنتج من السلة'
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'المنتج غير موجود في السلة'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ في حذف المنتج'
            ], 500);
        }
    }

    /**
     * تفريغ السلة
     */
    public function clearCart(Request $request)
    {
        try {
            $sessionId = $request->cookie('session_id');
            if (!$sessionId) {
                return response()->json([
                    'success' => false,
                    'error' => 'الجلسة غير موجودة'
                ], 404);
            }

            CartApicookies::where('session_id', $sessionId)->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم تفريغ السلة بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ في تفريغ السلة'
            ], 500);
        }
    }

    /**
     * دالة لتصحيح جميع الجلسات
     */
    public function debugAllSessions(Request $request)
    {
        try {
            // جلب جميع الجلسات من قاعدة البيانات
            $allSessions = CartApicookies::select('session_id')
                ->distinct()
                ->get()
                ->pluck('session_id');

            // جلب جميع العناصر
            $allItems = CartApicookies::all();

            // تحضير البيانات
            $sessionsData = [];
            foreach ($allSessions as $session) {
                $items = CartApicookies::where('session_id', $session)->get();
                $sessionsData[] = [
                    'session_id' => $session,
                    'items_count' => $items->count(),
                    'items' => $items->map(function($item) {
                        return [
                            'id' => $item->id,
                            'product_id' => $item->product_id,
                            'quantity' => $item->quantity,
                            'product_data' => $item->product_data,
                            'created_at' => $item->created_at
                        ];
                    })
                ];
            }

            return response()->json([
                'success' => true,
                'current_session_id_from_cookie' => $request->cookie('session_id'),
                'all_sessions_in_db' => $sessionsData,
                'total_sessions' => $allSessions->count(),
                'total_items' => $allItems->count(),
                'all_cookies' => $request->cookie()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * جلب بيانات أي session_id من DB
     */
    public function getCartBySessionId($sessionId = null)
    {
        try {
            // إذا لم يتم تمرير session_id، نجلب من الكوكيز
            if (!$sessionId) {
                $sessionId = request()->cookie('session_id');
            }

            // إذا لا يزال لا يوجد session_id، نعرض كل البيانات
            if (!$sessionId) {
                $allData = CartApicookies::all();
                
                return response()->json([
                    'success' => true,
                    'message' => 'عرض جميع البيانات (لا توجد جلسة محددة)',
                    'all_cart_data' => $allData->map(function($item) {
                        return [
                            'id' => $item->id,
                            'session_id' => $item->session_id,
                            'product_id' => $item->product_id,
                            'quantity' => $item->quantity,
                            'product_data' => $item->product_data,
                            'created_at' => $item->created_at
                        ];
                    }),
                    'total_records' => $allData->count()
                ]);
            }

            // جلب البيانات للـ session_id المحدد
            $cartItems = CartApicookies::where('session_id', $sessionId)->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'لا توجد بيانات للجلسة المحددة',
                    'session_id' => $sessionId,
                    'cart_data' => []
                ]);
            }

            $cartData = $cartItems->map(function($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'product_data' => $item->product_data,
                    'created_at' => $item->created_at->toDateTimeString()
                ];
            });

            return response()->json([
                'success' => true,
                'session_id' => $sessionId,
                'cart_data' => $cartData,
                'items_count' => $cartItems->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * فحص الجلسة الحالية
     */
    public function checkSession(Request $request)
    {
        try {
            $sessionId = $request->cookie('session_id');
            
            $existsInDB = $sessionId ? CartApicookies::where('session_id', $sessionId)->exists() : false;
            $itemsCount = $sessionId ? CartApicookies::where('session_id', $sessionId)->count() : 0;

            return response()->json([
                'success' => true,
                'session_id' => $sessionId,
                'session_exists' => !empty($sessionId),
                'exists_in_database' => $existsInDB,
                'items_in_cart' => $itemsCount,
                'all_sessions_count' => CartApicookies::distinct('session_id')->count('session_id')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
     public function productshow()
    {
        try {
        // جلب كل المنتجات من المودل
        $products = Products::select('id', 'name', 'description', 'price', 'stock_quantity')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'products' => $products
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'فشل جلب المنتجات: ' . $e->getMessage()
        ], 500);
    }
    }}