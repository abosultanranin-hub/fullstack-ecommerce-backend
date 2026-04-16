<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\orders;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Laravel\Socialite\Facades\Socialite;

class OrderNotification extends Notification
{
    use Queueable;
     protected $order;
       protected $user;

    public function __construct(orders $order,$user)
    {
        $this->order=$order;
         $this->user=$user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    // حدد قناة الإرسال
    public function via($notifiable)
    {
        return ['database']; // ترسل إلى قاعدة البيانات
    }

    // البيانات التي سيتم تخزينها في جدول notifications
    public function toDatabase($notifiable)
{
    return [
        'message' => 'تم إنشاء طلب جديد رقم: ' . $this->order->number,
        'order_id' => $this->order->id,
        'user_name' => $this->user->name,
        'created_at' => now()
    ];
}

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
      return (new MailMessage)
          ->subject("طلب جديد رقم: {$this->order->number}")
        ->line("أهلاً بـ {$this->user->name}، تم إنشاء طلب جديد رقم {$this->order->number}.")
        ->action("عرض الطلب", url("/dashboard"))
        ->line('شكرًا لاستخدامك لتطبيقنا!');}

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

public function toBroadcast($notifiable)
{
    return new BroadcastMessage([
        'message' => "طلب جديد رقم: {$this->order->number}",
        'order_id' => $this->order->id,
    ]);
}

}
