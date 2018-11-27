<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class Favour extends Notification implements ShouldQueue
{
    use Queueable;

    public $favourable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($favourable)
    {
        $this->favourable = $favourable;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return config('site.notifications.Favour.via');
    }

    public function toMail($notifiable)
    {
        return (new MailMessage())->line('新的点赞!');
    }

    public function toDatabase($notifiable)
    {
        //存储在data字段中的值
        return [
            'favourable' => $this->favourable->toArray(),
        ];
    }
}
