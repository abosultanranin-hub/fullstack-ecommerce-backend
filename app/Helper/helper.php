<?php

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

if (! function_exists('getOrCreateCookieId')) {
    function getOrCreateCookieId() {
   /*     $cookieName = 'carts_id';
        if ($cookieId = Cookie::get($cookieName)) {

            return $cookieId;

        }*/

        $cookieId = (string) Str::uuid();

       Cookie(
            $cookieName,
            $cookieId,
            60 * 24 * 30, // 30 يوم
            '/', null, false, true, false, 'Lax'
        );
       // setcookie($cookieName, $cookieId, time() + (30 * 24 * 60 * 60), '/');

        return $cookieId; // هذه كانت الناقصة في الكود الأصلي
    
    }
}
