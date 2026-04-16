<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\ExpertsOrderJob;
use App\Http\Middleware\Languagechange;
use App\Http\Middleware\EncryptCookies
;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
            api: __DIR__.'/../routes/api.php',   // ← هذا السطر الجديد

    )

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'language' => \App\Http\Middleware\Languagechange::class,
            'security' => \App\Http\Middleware\SecurityCheckMiddleware::class,
            'security_check' => \App\Http\Middleware\SecurityCheckMiddleware::class,
            'registration_check' => \App\Http\Middleware\RegistrationCheckMiddleware::class,
            'account_sharing' => \App\Http\Middleware\AccountSharingMiddleware::class,
        ]);

        $middleware->web(append: [

            \App\Http\Middleware\HandleInertiaRequests::class,
              \App\Http\Middleware\Languagechange::class,      

            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            \App\Http\Middleware\SecurityCheckMiddleware::class,
            \App\Http\Middleware\RegistrationCheckMiddleware::class,
       
        ]);


        $middleware->prepend(App\Http\Middleware\EncryptCookies::class);
        $middleware->append(Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withSchedule(function (Schedule $schedule) {
        //
    })
    ->create();


    