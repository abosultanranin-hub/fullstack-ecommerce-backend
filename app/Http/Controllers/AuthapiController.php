<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // 1. تحقق من CSRF token يدوياً
        if (!hash_equals(request()->session()->token(), $request->header('X-CSRF-TOKEN') ?? '')) {
            return response()->json(['message' => 'CSRF token mismatch'], 419);
        }
        
        // 2. التحقق من البيانات
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // 3. إنشاء المستخدم
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 4. تسجيل دخول تلقائي
        Auth::login($user);

        // 5. إنشاء session جديدة
        $request->session()->regenerate();

        return response()->json([
            'message' => 'تم التسجيل بنجاح',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        // التحقق من CSRF token
        if (!hash_equals(request()->session()->token(), $request->header('X-CSRF-TOKEN') ?? '')) {
            return response()->json(['message' => 'CSRF token mismatch'], 419);
        }
        
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();
            
            return response()->json([
                'message' => 'تم تسجيل الدخول بنجاح',
                'user' => Auth::user()
            ]);
        }

        return response()->json([
            'message' => 'بيانات الدخول غير صحيحة'
        ], 401);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'تم تسجيل الخروج بنجاح']);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}