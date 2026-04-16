<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array<int, string>
     */
    protected $except = [
          'user_theme'

        // هنا تقدر تحط الكوكيز اللي ما بدك تتشفّر، أو تتركه فاضي
    ];
}
