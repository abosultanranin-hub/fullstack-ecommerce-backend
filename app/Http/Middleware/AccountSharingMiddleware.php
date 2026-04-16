<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\UserLoginIp;
use Illuminate\Support\Facades\Auth;

class AccountSharingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user) {
            $count = UserLoginIp::where('user_id', $user->id)
                ->where('last_used_at', '>=', now()->subDay())
                ->count();

            if ($count > 3) {
                // In API context, we revoke tokens and return 403
                $user->tokens()->delete();
                
                return response()->json([
                    'message' => 'Account sharing detected. Access denied.'
                ], 403);
            }
        }

        return $next($request);
    }
}
