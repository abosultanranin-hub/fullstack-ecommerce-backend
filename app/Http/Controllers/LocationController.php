<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\locationsending;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class LocationController extends Controller
{



    public function showLocationForm()
    {
        return view('location'); // هذا يعرض ملف resources/views/location.blade.php
    }






    public function showmap()
    {
        $order = auth()->user()->orders()->latest()->first();
 return view('mapmaker', [
            'orderId' => $order ? $order->id : null
        ]);
    }

public function saveLocation(Request $request)
{
    $user = Auth::user();

    if (!$user) {
        return response()->json(['error' => 'غير مصرح. يجب تسجيل الدخول أولاً.'], 401);
    }

    $validated = $request->validate([
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric'
    ]);

    $latestOrder = $user->orders()->latest()->first();

    if (!$latestOrder) {
        return response()->json([
            'error' => 'لا يمكنك تحديد الموقع قبل إنشاء طلب.'
        ], 403);
    }

    try {
  // إرسال الموقع إلى حدث إذا لزم الأمر:
        event(new LocationSending($request->latitude, $request->longitude, $latestOrder->id));

        return response()->json([
            'message' => 'تم حفظ الموقع بنجاح',
            'data' => [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'order_id' => $latestOrder->id
            ]
        ]);
    } catch (\Exception $e) {
        Log::error('خطأ أثناء حفظ الموقع: ' . $e->getMessage());

        return response()->json([
            'error' => 'حدث خطأ أثناء حفظ الموقع',
            'details' => $e->getMessage()
        ], 500);
    }
}

}      
