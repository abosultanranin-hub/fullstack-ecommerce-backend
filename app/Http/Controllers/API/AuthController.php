<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // أضف هذا أيضاً
use App\Models\LoginSession;
use App\Models\SecurityLog;
use App\Models\BlockedIp;
use App\Models\BlockedEmailDomain;
use App\Models\UserLoginIp;
use App\Models\SecurityVerification;
use App\Models\EmailVerificationToken;
use App\Services\GeoIPService;
use App\Mail\SuspiciousLoginMail;
use App\Mail\SecurityVerificationMail;
use App\Mail\EmailVerificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class AuthController extends Controller
{
 protected function frontendUrl(): string
{
    return rtrim(env('FRONTEND_URL', 'https://ranin-store.netlify.app'), '/');
}

protected function redirectToFrontend(array $query = [])
{
    $url = $this->frontendUrl() . '/';

    if (!empty($query)) {
        $url .= '?' . http_build_query($query);
    }

    return redirect()->away($url);
}

 public function login(Request $request)
{
    
    try {
        // التحقق من البيانات
        
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        
        
        // البحث عن المستخدم
        $user = User::where('email', $validated['email'])->first();
        $ip = $request->ip();
        
        if (!$user) {
            $this->handleFailedAttempt($ip);
            return response()->json([
                'message' => 'بيانات الاعتماد غير صحيحة'
            ], 401);
        }
        
        Log::info('User found, ID: ' . $user->id);
        
        // التحقق من كلمة المرور
        if (!Hash::check($validated['password'], $user->password)) {
            $this->handleFailedAttempt($ip, $user->id);
            return response()->json([
                'message' => 'بيانات الاعتماد غير صحيحة'
            ], 401);
        }
        
        
        $ip = $request->ip();
        $country = GeoIPService::getCountryFromIP($ip);

        // ===== معلّق: كود فحص IP الجديد وإرسال إيميل التحقق الأمني =====
        // // 1. Check if IP is known
        // $knownIp = UserLoginIp::where('user_id', $user->id)
        //     ->where('ip', $ip)
        //     ->first();

        // if (!$knownIp) {
        //     // New IP detected - Suspicious Login
        //     $token = Str::random(64);
        //     SecurityVerification::create([
        //         'user_id' => $user->id,
        //         'token' => $token,
        //         'ip' => $ip,
        //         'country' => $country,
        //         'expires_at' => now()->addMinutes(15)
        //     ]);

        //     try {
        //         Mail::to($user->email)->send(new SecurityVerificationMail($token, $ip, $country));
        //     } catch (\Exception $e) {
        //         Log::error('Failed to send security verification email: ' . $e->getMessage());
        //         // We still want to block login, but maybe header 500 is obscured.
        //         // Actually if mail fails, user can't verify.
        //         // But better to log it than crash.
        //     }

        //     SecurityLog::create([
        //         'type' => 'suspicious_login_blocked',
        //         'ip' => $ip,
        //         'user_id' => $user->id,
        //         'details' => "تم حظر الدخول من IP جديد ({$ip}) وفي انتظار التحقق"
        //     ]);

        //     return response()->json([
        //         'status' => 'verification_required',
        //         'message' => 'تم اكتشاف تسجيل دخول من جهاز جديد. يرجى التحقق من بريدك الإلكتروني لتأكيد هويتك.'
        //     ], 403);
        // }
        // ===== نهاية الكود المعلّق =====

        // 2. IP is known, update last_used_at (معلّق لأن $knownIp غير معرّف الآن)
        // $knownIp->update(['last_used_at' => now()]);

        // 3. Account Sharing Prevention (IP Limit within 24h)
        $ipCount = UserLoginIp::where('user_id', $user->id)
            ->where('last_used_at', '>=', now()->subDay())
            ->count();

        if ($ipCount > 3) {
            SecurityLog::create([
                'type' => 'account_sharing_blocked_api',
                'ip' => $ip,
                'user_id' => $user->id,
                'details' => "تم حظر الدخول بسبب تجاوز عدد العناوين المسموحة ({$ipCount})"
            ]);

            return response()->json(['message' => 'مشاركة الحساب ممنوعة (تجاوزت الحد المسموح للأجهزة)'], 403);
        }

        // Proceed with normal login
        $token = $user->createToken('auth_token')->plainTextToken;

        $user->update([
            'last_country' => $country,
            'last_login' => now()
        ]);

        SecurityLog::create([
            'type' => 'login_success_api',
            'ip' => $ip,
            'user_id' => $user->id,
            'details' => 'تسجيل دخول API ناجح'
        ]);

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'message' => 'تم تسجيل الدخول بنجاح'
        ]);
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'message' => 'فشل التحقق من البيانات',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        
        return response()->json([
            'message' => 'حدث خطأ أثناء تسجيل الدخول',
            'error' => $e->getMessage()
        ], 500);
    }
}
public function register(Request $request)
{
    try {
        // تحقق من البيانات
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Email domain check
        $domain = substr(strrchr($validated['email'], "@"), 1);
        if (BlockedEmailDomain::where('domain', $domain)->exists()) {
            return response()->json(['message' => 'نطاق البريد الإلكتروني هذا محظور'], 400);
        }
        
        // إنشاء المستخدم
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // تسجيل إنشاء الحساب في security_logs
        SecurityLog::create([
            'type' => 'account_created',
            'ip' => $request->ip(),
            'user_id' => $user->id,
            'details' => 'تم إنشاء الحساب بنجاح'
        ]);
        
        // إنشاء رمز التحقق من البريد
        $verificationToken = Str::random(64);
        EmailVerificationToken::create([
            'user_id' => $user->id,
            'token' => $verificationToken,
            'expires_at' => now()->addDay()
        ]);
        
        // إرسال بريد التحقق
        try {
            Mail::to($user->email)->send(new EmailVerificationMail($verificationToken, $user));
        } catch (\Exception $e) {
            Log::error('Failed to send email verification: ' . $e->getMessage());
            // حذف المستخدم إذا فشل إرسال البريد
            $user->delete();
            return response()->json([
                'message' => 'فشل إرسال بريد التحقق. يرجى المحاولة مجدداً.'
            ], 500);
        }
        
        Log::info('Verification email sent to: ' . $user->email);
        
        // إرجاع الرد بدون توكن (يجب التحقق من البريد أولاً)
        return response()->json([
            'message' => 'تم التسجيل بنجاح. يرجى تأكيد بريدك الإلكتروني للدخول إلى حسابك.',
            'email' => $user->email
        ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'message' => 'فشل التحقق من البيانات',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {

        return response()->json([
            'message' => 'فشل التسجيل',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function user(Request $request)
{
    try {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'المستخدم غير مصرح به'
            ], 401);
        }
        
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                // أضف أي حقول إضافية
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'حدث خطأ أثناء جلب بيانات المستخدم'
        ], 500);
    }
}




public function logout(Request $request)
{
    try {
        $user = $request->user();

        if ($user) {
            // حذف جميع توكنات المستخدم
            $user->tokens()->delete();

            // تسجيل تسجيل الخروج في security_logs
            SecurityLog::create([
                'type' => 'logout',
                'ip' => $request->ip(),
                'user_id' => $user->id,
                'details' => 'تم تسجيل الخروج بنجاح'
            ]);
        }

        return response()->json([
            'message' => 'تم تسجيل الخروج بنجاح'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'حدث خطأ أثناء تسجيل الخروج'
        ], 500);
    }
}
public function verifySecurity($token)
    {
        try {
            $verification = SecurityVerification::where('token', $token)
                ->where('expires_at', '>', now())
                ->first();

            if (!$verification) {
                abort(403, 'رابط التحقق غير صالح أو منتهي الصلاحية');
            }

            // Login the user
            Auth::loginUsingId($verification->user_id);

            // Add IP to known IPs
            UserLoginIp::updateOrCreate(
                ['user_id' => $verification->user_id, 'ip' => $verification->ip],
                ['last_used_at' => now()]
            );

            // Generate Token
            $token = $verification->user->createToken('auth_token')->plainTextToken;

            // Log the success
            SecurityLog::create([
                'type' => 'security_verification_success',
                'ip' => $verification->ip,
                'user_id' => $verification->user_id,
                'details' => 'تم التحقق من الجهاز الجديد بنجاح'
            ]);

            // Delete verification token
            $verification->delete();

            return $this->redirectToFrontend([
                'token' => $token,
                'message' => 'verified',
            ]);

        } catch (\Exception $e) {
            Log::error('Security verification error: ' . $e->getMessage());
            abort(500, 'حدث خطأ أثناء التحقق من الأمان');
        }
    }

    public function verifyEmail($token)
    {
        try {
            // البحث عن رمز التحقق
            $verificationToken = EmailVerificationToken::where('token', $token)
                ->where('expires_at', '>', now())
                ->first();

            if (!$verificationToken) {
                return $this->redirectToFrontend([
                    'error' => 'invalid_token',
                    'message' => 'رابط التحقق غير صالح أو منتهي الصلاحية',
                ]);
            }

            $user = $verificationToken->user;

            // إنشاء token للمستخدم وتسجيل دخوله تلقائياً
            $authToken = $user->createToken('auth_token')->plainTextToken;

            // حذف رمز التحقق
            $verificationToken->delete();

            // تحديث آخر تسجيل دخول
            $user->update([
                'last_login' => now()
            ]);

            Log::info('Email verified successfully for user: ' . $user->id);

            // إعادة التوجيه مع التوكن
            return $this->redirectToFrontend([
                'token' => $authToken,
                'verified' => 'true',
            ]);

        } catch (\Exception $e) {
            Log::error('Email verification error: ' . $e->getMessage());
            return $this->redirectToFrontend([
                'error' => 'verification_failed',
                'message' => 'حدث خطأ أثناء التحقق من البريد',
            ]);
        }
    }


    protected function handleFailedAttempt($ip, $userId = null)
    {
        SecurityLog::create([
            'type' => 'failed_login',
            'ip' => $ip,
            'user_id' => $userId,
            'details' => 'محاولة تسجيل دخول فاشلة'
        ]);

        $attempts = SecurityLog::where('ip', $ip)
            ->where('type', 'failed_login')
            ->where('created_at', '>', now()->subMinutes(15))
            ->count();

        if ($attempts >= 5) {
            BlockedIp::updateOrCreate(
                ['ip' => $ip],
                ['blocked_until' => now()->addMinutes(30)]
            );

            SecurityLog::create([
                'type' => 'ip_blocked',
                'ip' => $ip,
                'details' => 'تم حظر IP تلقائياً بعد 5 محاولات فاشلة'
            ]);
        }
    }
}
