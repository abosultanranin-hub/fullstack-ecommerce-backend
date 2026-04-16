<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;
class Languagechange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
  $path = $request->path();

    // إذا كانت الطلب لملف ستايل أو سكريبت أو صورة
    if (
        str_starts_with($path, 'assets/') || 
        str_contains($path, '/assets/') || 
        str_ends_with($path, '.js') || 
        str_ends_with($path, '.css') || 
        str_ends_with($path, '.png') || 
        str_ends_with($path, '.jpg') || 
        str_ends_with($path, '.jpeg') || 
        str_ends_with($path, '.ico')
    ) {
        return $next($request);
    }



         $locale = $request->route('locale');

        // 2. إذا لم تكن موجودة، نأخذها من الكوكي
        if (!$locale) {
            $locale = $request->cookie('app_locale', 'ar'); // default to 'ar'
        }

        // 3. نحفظ اللغة في الكوكي (إذا جاءت من الرابط)
        if ($locale) {
    $cookie = cookie('app_locale', $locale, 60); // إزالة علامات التنصيص الزائدة
           
        }

        // 4. نعين اللغة في التطبيق
        App::setLocale($locale);

        // 5. نضبطها كقيمة افتراضية في الروابط
        URL::defaults(['locale' => $locale]);
        Route::current()->forgetParameter('locale');


        return $next($request);
    }
    
}
