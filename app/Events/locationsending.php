<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LocationSending implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $latitude;
    public $longitude;
    public $orderId;

    public function __construct($latitude, $longitude, $orderId)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->orderId = $orderId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel("ranin.{$this->orderId}");
    }

    // إضافة هذه الدالة لتحديد اسم الحدث
    public function broadcastAs()
    {
        return 'location.updated';
    }
}