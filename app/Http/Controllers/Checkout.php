<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Str;
use App\Models\Carts;
use App\Models\orders;
use App\Repositories\cart\CartRepository;
use App\Models\order_items;
use App\Models\OrderAddress;
use App\Notifications\OrderNotification;
use App\Jobs\SendWelcomeEmail;
class Checkout extends Controller
{
    public function index()
    {
        return view('cart.checkout');
        
    }




  public function store(Request $request){
     
    
      $cookieId = \App\Models\Carts::getCartCookieId();
$cart_items = \App\Models\Carts::with('products')->where('cookie_id', $cookieId)
                     ->get();
         $user = Auth::user();

        $order = orders::Create([
            'user_id' => $user->id,
             'number' => Str::uuid(),
            'payment_method' => 'cod',
        ]);
       foreach ($request->post('addr') as $type => $address) {   
 $address['type'] = $type;   
 $order->addresses()->create($address);  
       }

        foreach ($cart_items as $item) {
           order_items::create([
                'order_id'     => $order->id,
                'product_id'   =>$item->products->id,
                'product_name' =>$item->products->name,
                'price'        =>$item->products->price,
                'quantity'     => $item->quantity,
            ]);
        }
        $stripeSecret = config('services.stripe.secret');
        if (blank($stripeSecret)) {
            return back()->with('error', 'خدمة الدفع غير مهيأة على الخادم. أضف STRIPE_SECRET ثم أعد النشر.');
        }

        // Initialize Stripe
        \Stripe\Stripe::setApiKey($stripeSecret);

        $lineItems = [];
        foreach ($cart_items as $item) {
             // Ensure price is valid
             $price = $item->price > 0 ? $item->price : $item->products->price;

             $lineItems[] = [
                'price_data' => [
                    'currency'     => 'usd',
                    'product_data' => [
                        'name' => $item->products->name,
                    ],
                    'unit_amount'  => $price * 100,
                ],
                'quantity'   => $item->quantity,
            ];
        }

        try {
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items'           => $lineItems,
                'mode'                 => 'payment',
                'success_url'          => route('api.payment.success') . '?session_id={CHECKOUT_SESSION_ID}', // Updated route
                'cancel_url'           => route('api.payment.cancel'), // Updated route
                'metadata'             => [
                    'order_id' => $order->id,
                    'user_id'  => $user->id
                ],
            ]);

            return redirect($session->url);
        
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
