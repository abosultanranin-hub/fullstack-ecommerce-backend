<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\BlockedEmailDomain;

class RegistrationCheckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('post') && $request->has('email')) {
            $email = $request->email;
            $domain = substr(strrchr($email, "@"), 1);

            if (BlockedEmailDomain::where('domain', $domain)->exists()) {
                return response()->json(['message' => 'نطاق البريد الإلكتروني هذا محظور'], 400);
            }
        }

        return $next($request);
    }
}
