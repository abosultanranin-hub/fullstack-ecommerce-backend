<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\BlockedIp;
use App\Models\BlockedCountry;
use App\Services\GeoIPService;
use App\Models\SecurityLog;

class SecurityCheckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        // 1. Check if IP is blocked
        $blockedIp = BlockedIp::where('ip', $ip)
            ->where('blocked_until', '>', now())
            ->first();

        if ($blockedIp) {
            return response()->json(['message' => 'تم حظر IP الخاص بك مؤقتاً'], 403);
        }

        // 2. Check if Country is blocked
        $country = GeoIPService::getCountryFromIP($ip);
        if ($country && BlockedCountry::where('country_code', $country)->exists()) {
            SecurityLog::create([
                'type' => 'blocked_country_attempt',
                'ip' => $ip,
                'details' => "محاولة دخول من دولة محظورة: {$country}"
            ]);
            abort(403, 'الدخول من هذه الدولة محظور');
        }

        return $next($request);
    }
}
