<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;

return function (Schedule $schedule) {

    // 🛒 فحص السلات المهجورة مرة يوميًا
    $schedule->command('cart:check-abandoned')
        ->daily()
        ->onSuccess(function () {
            Log::info('Abandoned carts check completed successfully');
        })
        ->onFailure(function () {
            Log::error('Abandoned carts check failed');
        });

};

  