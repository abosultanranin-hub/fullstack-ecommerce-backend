<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CarttController;
use App\Http\Controllers\Checkout;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\RoleController;

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;

use App\Http\Middleware\Languagechange;
use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\cors;
use App\Http\Middleware\VerifyCsrfToken;

use App\Http\Controllers\LocationController;
use App\Http\Controllers\CartControllerapi;
use App\Http\Controllers\API\AuthController;

Route::get('/', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'fullstack-ecommerce-api',
        'frontend_url' => env('FRONTEND_URL', 'https://ranin-store.netlify.app'),
    ]);
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'account_sharing'])->name('dashboard');





Route::middleware(['web'])->group(function () {

//Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

Route::get('/hello', function () {
    return view('hello');
})->name('hello');

Route::get('/test', function () {
    return view('test');
});

Route::get('/testcoo', [CarttController::class, 'store']);

Route::get('/store/{doman}', [CategoryController::class, 'index'])->name('store.show');;
Route::get('/category/index', [CategoryController::class, 'index'])->name('category.index');
Route::get('/category/show', [CartController::class, 'show']);
//Route::get('/cart', [CartController::class, 'index'])->name('cart.index'); // إرجاع محتويات السلة
//Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add'); // إضافة إلى السلة
Route::post('/category/store', [CategoryController::class, 'store'])->name('category.store');
Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('category.destroy');
Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
//                                                          هنا التصحيح ---^
// التالي هو تبعي
//Route::get('paypalview',[PayPalController::class,'CreatingOrder'])->name('paypal.view');
//Route::get('paypal/success', [PayPalController::class, 'success'])->name('paypal.success');
//Route::get('paypal/cancel', [PayPalController::class, 'cancel'])->name('paypal.cancel');
// cart

//Route::group(['middleware' => 'language','prefix' => '{locale}', ], function() {
Route::get('/cart', [CartController::class, 'view'])
    ->name('cart.view')
;
Route::get('/cart/details', [CartController::class, 'details'])->name('cart.details'); // تعرض البيانات JSON
Route::post('/cart/update-quantity', [CartController::class, 'updateQuantity'])->name('cart.updateQuantity');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/update/{id}', [CartController::class, 'update']);
Route::post('/cart/add/{id}', [CartController::class, 'add']);
Route::get('/checkout', [Checkout::class, 'index'])->name('checkout.index');
//Route::post('/checkout/redirect', [Checkout::class, 'store'])->name('checkout.redirect');
    Route::get('/product/show', [CarttController::class, 'view']);

    Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');



//});//

Route::get('/location', [LocationController::class, 'showLocationForm']);
Route::post('/save-location', [LocationController::class, 'saveLocation']);
Route::get('/mapmaker', [LocationController::class, 'showmap']);
    Route::get('/security/verify/{token}', [AuthController::class, 'verifySecurity'])->name('security.verify');

});
//});
//require __DIR__.'/auth.php';
//login with providers 
Route::get('/auth/redirect/{provider}', [SocialController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/callback/{provider}', [SocialController::class, 'callback']);


// صفحة عرض نموذج إنشاء دور جديد
Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');

// استقبال بيانات النموذج وحفظ الدور
Route::post('/roles', [RoleController::class, 'store'])->name('roles.store'); 
/*  
    Route::get('/', [CartControllerapi::class, 'getDBCartData']); // جلب السلة الحالية
    Route::get('/debug', [CartControllerapi::class, 'debugAllSessions']); // تصحيح الجلسات
    Route::get('/all-data', [CartControllerapi::class, 'getCartBySessionId']); // جميع البيانات
    Route::get('/by-session/{sessionId}', [CartControllerapi::class, 'getCartBySessionId']); // بجلسة محددة
    Route::get('/check-session', [CartControllerapi::class, 'checkSession']); // فحص الجلسة
    Route::post('/add/{productId}', [CartControllerapi::class, 'addToCart']); // إضافة منتج
    Route::put('/update/{productId}', [CartControllerapi::class, 'updateCartItem']); // تحديث كمية
    Route::delete('/remove/{productId}', [CartControllerapi::class, 'removeFromCart']); // حذف منتج
    Route::delete('/clear', [CartControllerapi::class, 'clearCart']); // تفريغ السلة
*/
