<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\UserLoginIp;
use Illuminate\Http\Request;

class RecordLoginIp
{
    /**
     * Create the event listener.
     */
    public function __construct(protected Request $request)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $ip = $this->request->ip();

        UserLoginIp::updateOrCreate(
            ['user_id' => $event->user->id, 'ip' => $ip],
            ['last_used_at' => now()]
        );
    }
}
