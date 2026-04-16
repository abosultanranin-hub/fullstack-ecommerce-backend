<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Notifications\OrderNotification;
use Illuminate\Support\Facades\Log;

// هاد ال job هوevent  عادي  يعني
// يعني لو استخمت ال evevt بدل من ال jobs 
//لانو هادي ال job or event ما بستني عالدور
//بل فور انشاء طلب يتم ارسال ايميل 
// علشان هيك ما وضعنا implements ShouldQueue

class SendWelcomeEmail //implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $order;

    /**
     * Create a new job instance.
     */
    public function __construct( $order,User $user)
    {
        $this->user = $user;
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
     $this->user->notify(new OrderNotification($this->order,$this->user));
     //  Log::info("مهام مجدولة تعمل");
    }
}
