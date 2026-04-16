<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\LoginSession;
use App\Models\SecurityLog;
use App\Models\User;
use App\Services\GeoIPService;
use App\Mail\SuspiciousLoginMail;
use Illuminate\Support\Facades\Mail;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): Response
    {
        $request->authenticate();

        $user = Auth::user();
        $ip = $request->ip();
        $country = GeoIPService::getCountryFromIP($ip);

        // 1. Account Sharing Prevention (IP Limit)
        $ipCount = LoginSession::where('user_id', $user->id)
            ->where('created_at', '>', now()->subHour())
            ->distinct('ip')
            ->count();

        if ($ipCount >= 3) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            SecurityLog::create([
                'type' => 'account_sharing_blocked',
                'ip' => $ip,
                'user_id' => $user->id,
                'details' => 'تم حظر الدخول بسبب تخطي عدد IPs المسموح به (3)'
            ]);

            abort(403, 'مشاركة الحساب ممنوعة');
        }

        // 2. Suspicious Login Detection (Country change)
        if ($user->last_country && $user->last_country !== $country) {
            Mail::to($user->email)->send(new SuspiciousLoginMail($ip, $country));
            
            SecurityLog::create([
                'type' => 'suspicious_login',
                'ip' => $ip,
                'user_id' => $user->id,
                'details' => "تسجيل دخول من بلد مختلف: {$country} (سابقاً: {$user->last_country})"
            ]);
        }

        // Update user stats
        $user->update([
            'last_country' => $country,
            'last_login' => now()
        ]);

        // Log session
        LoginSession::create([
            'user_id' => $user->id,
            'ip' => $ip
        ]);

        SecurityLog::create([
            'type' => 'login_success',
            'ip' => $ip,
            'user_id' => $user->id,
            'details' => 'تسجيل دخول ناجح'
        ]);

        $request->session()->regenerate();

        return response()->noContent();
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
