<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class ApiAuthenticate extends Middleware
{
    protected function redirectTo($request)
    {
        // لا تعيد توجيه إلى route login
        return null;
    }
}
