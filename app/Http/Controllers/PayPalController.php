<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalHttpException;

class PayPalController extends Controller {

    public function connection() {
        // إعداد بيئة الاتصال
        $clientId = config('services.paypal.client_id');
        $secret = config('services.paypal.client_secret');

        $environment = new SandboxEnvironment($clientId, $secret);
        $client = new PayPalHttpClient($environment);

        return $client;
    }

    public function CreatingOrder() {
        // إعداد طلب إنشاء الطلب
        $client = $this->connection();

        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->body = [
            "intent" => "CAPTURE",
            "purchase_units" => [[
                "reference_id" => "test_ref_id1",
                "amount" => [
                    "value" => "100.00",
                    "currency_code" => "USD"
                ]
            ]],
            "application_context" => [
                "cancel_url" => url(route('paypal.success')), // استخدام route فقط
                "return_url" => url(route('paypal.cancel')) // استخدام route فقط
            ]
        ];

        try {
            // استدعاء API وتنفيذ الطلب
            $response = $client->execute($request);

            // إذا كانت النتيجة صحيحة
            if ($response->statusCode == 201) {
                // البحث عن الرابط للانتقال إلى صفحة الدفع
                foreach ($response->result->links as $link) { 
                    if ($link->rel == 'approve') { 
                        return redirect()->away($link->href); 
                    }
                }
            }

        } catch (PayPalHttpException $ex) {
            // التعامل مع الاستثناءات المتعلقة بالاتصال
            \Log::error('PayPal API Error: ' . $ex->getMessage());
            echo $ex->statusCode;
            print_r($ex->getMessage());
        }
    }
    
    public function success(Request $request)
    {
        // هنا منطق نجاح الدفع
        return 'Payment successful!';
    }

    public function cancel()
    {
        // هنا منطق إلغاء الدفع
        return 'Payment cancelled.';
    }
}

