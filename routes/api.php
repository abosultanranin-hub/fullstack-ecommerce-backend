<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartControllerapi;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\Checkout;
use App\Http\Controllers\API\ProductController as ApiProductController;
use App\Http\Controllers\API\ApiCart2Controller;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\InvoiceController;
use App\Http\Controllers\API\DebugInvoiceController;

// CORS handled by config/cors.php middleware

// CSRF Cookie Route
Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
});

// Authentication Routes
Route::post('/register', [AuthController::class, 'register'])->middleware('registration_check');
// Route::post('/login', [AuthController::class, 'login'])->middleware(['security_check', 'throttle:5,15']); // معلّق: كود المحاولات المتكررة وفحص الأمان
Route::post('/login', [AuthController::class, 'login']);
Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail'])->name('verify.email');

// Protected Routes
Route::middleware(['auth:sanctum', 'account_sharing'])->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Cart API
    Route::get('/getcart', [ApiCart2Controller::class, 'index']);
    Route::post('/add', [ApiCart2Controller::class, 'add']);
    Route::put('/update/{id}', [ApiCart2Controller::class, 'update']);
    Route::post('/decrease/{id}', [ApiCart2Controller::class, 'decrease']);
    Route::delete('/remove/{id}', [ApiCart2Controller::class, 'remove']);
    Route::delete('/clear', [ApiCart2Controller::class, 'clear']);

    // Stripe Checkout API
    Route::post('/checkout', [App\Http\Controllers\API\StripeController::class, 'checkout'])->name('api.checkout');
    Route::get('/payment/success', [App\Http\Controllers\API\StripeController::class, 'success'])->name('api.payment.success');
    Route::get('/payment/cancel', [App\Http\Controllers\API\StripeController::class, 'cancel'])->name('api.payment.cancel');

    // Debug & Extra Cart Info (Keeping for now based on previous code)
    Route::get('/cart-db', [CartControllerapi::class, 'getDBCartData']);
    Route::get('/cart-debug', [CartControllerapi::class, 'debugAllSessions']);
    Route::get('/cart-session/{sessionId}', [CartControllerapi::class, 'getCartBySessionId']);
    Route::get('/cart-check', [CartControllerapi::class, 'checkSession']);

    // Checkout and PayPal
    Route::get('/checkout', [Checkout::class, 'index'])->name('checkout.index');
    Route::post('/checkout/redirect', [Checkout::class, 'store'])->name('checkout.redirect');
    Route::get('paypalview',[PayPalController::class,'CreatingOrder'])->name('paypal.view');
    Route::get('paypal/success', [PayPalController::class, 'success'])->name('paypal.success');
    Route::get('paypal/cancel', [PayPalController::class, 'cancel'])->name('paypal.cancel');
    
    // Invoice API Routes
    Route::get('/invoices', [InvoiceController::class, 'getUserInvoices'])->name('api.invoices.index');
    Route::get('/invoices/{invoiceId}', [InvoiceController::class, 'getInvoice'])->name('api.invoices.show');
    Route::get('/invoices/{invoiceId}/download', [InvoiceController::class, 'downloadInvoice'])->name('api.invoices.download');
    Route::post('/invoices/{invoiceId}/resend', [InvoiceController::class, 'resendInvoice'])->name('api.invoices.resend');
    Route::get('/invoices/statistics', [InvoiceController::class, 'getInvoiceStatistics'])->name('api.invoices.statistics');
    
    // Debug Invoice Routes (للاختبار والتشخيص)
    Route::post('/debug/invoice/test/{orderId}', [DebugInvoiceController::class, 'testCreateInvoice']);
    Route::get('/debug/order/{orderId}', [DebugInvoiceController::class, 'checkOrderStatus']);
    Route::get('/debug/logs', [DebugInvoiceController::class, 'getLogs']);
    
    // Order API
    Route::post('/orders', [App\Http\Controllers\API\OrderController::class, 'store']);
});

// Public Product Routes
Route::get('/showproduct', [ApiProductController::class, 'index']);




