<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class Follow extends Notification implements ShouldQueue
{
    use Queueable;

    public $follower;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($follower)
    {
        $this->follower = $follower;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail','database'];

    }

    public function toDatabase($notifiable){
        //存储在data字段中的值
        return [
            'user' => $this->follower->toArray(),
        ];
    }

    public function toMail($notifiable){
        return (new MailMessage())
            ->line($this->follower->name.' 订阅了您!')
            ->action('点击链接查看',app('Dingo\Api\Routing\UrlGenerator')
                ->version('v1')
                ->route('api.me.followers')
            );
    }
}
