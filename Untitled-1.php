<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CarttController;
use App\Http\Controllers\Checkout;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\AuthapiController;

// الصفحة الرئيسية (عام)
Route::get('/', function () {
    return view('welcome');
});
Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['csrf' => csrf_token()]);
});

// API routes بدون جلسة
Route::post('/registerapi', [AuthapiController::class, 'register']);
Route::post('/loginapi', [AuthapiController::class, 'login']);
Route::post('/logout', [AuthapiController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
});

// Routes خاصة بالمستخدمين الغير مسجلين (Guest)
Route::middleware(['web', 'guest'])->group(function () {
    Route::get('/auth/redirect/{provider}', [SocialController::class, 'redirect'])->name('social.redirect');
    Route::get('/auth/callback/{provider}', [SocialController::class, 'callback']);
    Route::get('/login', function() { return view('auth.login'); })->name('login');
    Route::get('/register', function() { return view('auth.register'); })->name('register');
});

// Routes محمية بالمستخدمين المسجلين فقط
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware('verified')->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    
    // صفحات اختبارية
    Route::get('/hello', function () { return view('hello'); })->name('hello');
    Route::get('/test', function () { return view('test'); });
    Route::get('/testcoo', [CarttController::class, 'store']);

    // إدارة الفئات
    Route::get('/store/{doman}', [CategoryController::class, 'index'])->name('store.show');
    Route::get('/category/index', [CategoryController::class, 'index'])->name('category.index');
    Route::get('/category/show', [CartController::class, 'show']);
    Route::post('/category/store', [CategoryController::class, 'store'])->name('category.store');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('category.destroy');
    Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');

    // PayPal
    Route::get('paypalview',[PayPalController::class,'CreatingOrder'])->name('paypal.view');
    Route::get('paypal/success', [PayPalController::class, 'success'])->name('paypal.success');
    Route::get('paypal/cancel', [PayPalController::class, 'cancel'])->name('paypal.cancel');

    // Cart
    Route::get('/cart/details', [CartController::class, 'details'])->name('cart.details');
    Route::post('/cart/update-quantity', [CartController::class, 'updateQuantity'])->name('cart.updateQuantity');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/add/{id}', [CartController::class, 'add']);
    Route::post('/cart/update/{id}', [CartController::class, 'update']);
    Route::get('/product/show', [CarttController::class, 'index']);

    
    // Product search

    
    // Location
    Route::get('/location', [LocationController::class, 'showLocationForm']);
    Route::post('/save-location', [LocationController::class, 'saveLocation']);
    Route::get('/mapmaker', [LocationController::class, 'showmap']);

    // إدارة الأدوار (Roles)
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
});

// تحميل ملفات auth الخاصة بـ Laravel
//require __DIR__.'/auth.php';
// Checkout


    // PayPal
    Route::get('paypalview',[PayPalController::class,'CreatingOrder'])->name('paypal.view');
    Route::get('paypal/success', [PayPalController::class, 'success'])->name('paypal.success');
    Route::get('paypal/cancel', [PayPalController::class, 'cancel'])->name('paypal.cancel');

    Route::get('/checkout', [Checkout::class, 'index'])->name('checkout.index');
    Route::post('/checkoutredirect', [Checkout::class, 'store'])->name('checkout.redirect');

 
