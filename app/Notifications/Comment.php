<?php

namespace App\Notifications;

use Dingo\Api\Routing\UrlGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class Comment extends Notification
{
    use Queueable;

    public $comment;

    public function __construct($comment)
    {
        // 注入回复实体，方便 toDatabase 方法中的使用
        $this->comment = $comment;
    }

    public function via($notifiable)
    {
        // 开启通知的频道
        return ['database'];
    }

    //这个返回的数组将被转成 JSON 格式并存储到通知数据表的 data 列
    public function toDatabase($notifiable){
        $av = $this->comment->av;
        $user = $this->comment->user;

        //存储在data字段中的值
        return [
            'comment_id' => $this->comment->id,
            'comment_content' => $this->comment->comment,
            'comment_parent_id' => $this->comment->parent_id,
            'comment_target_id' => $this->comment->target_id,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_avatar' => $user->avatar,
            'av_id' => $av->id,
            'av_name' => $av->name,
        ];
    }
}
