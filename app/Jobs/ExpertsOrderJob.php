<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExpertsOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        // هنا ممكن تستقبل بيانات لو حبيت
    }

    public function handle()
    {
        // الكود اللي تريد تنفذه في المهمة المجدولة
        Log::info('ExpertsOrderJob تم تنفيذه.');
        // أي عملية تريدها مثل معالجة طلبات، إرسال إيميلات، تحديث بيانات...
    }
}
/*
خطوات عمل queue job

implements ShouldQueue يعني job عامل
2 
connection queue=database or redio 
او اي اشي بخزن و بخط ال jobs عل الطابور 

connection queue=sync 
معناها ما ما في queue علي ال job



*/