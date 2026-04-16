<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $except = [

 'sanctum/csrf-cookie',
        
        // استثناء لواجهات API التي ستستخدمها من React
        'api/register',
        'api/login',
         'api/logout',
        
        // أو يمكنك استخدام النمط العام (ولكن أقل أماناً)
        // 'api/*',
        
        // إذا كنت تستخدم web routes للـ authentication
        'register',
        'login', 
        'logout',
        
        // إذا كنت تستخدم API tokens بدلاً من cookies
       'api/*'
    ];
}
