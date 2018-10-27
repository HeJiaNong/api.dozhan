<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Notifications\DatabaseNotification;

class NotificationPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    //过滤器
    public function before($user, $ability)
    {
        if ($user->hasPermissionTo('manage_notifications')) {
            return true;
        }
    }

    //将单挑消息标记为已读
    public function readSingle(User $user,DatabaseNotification $notification){
        return $user->id == $notification->notifiable->id;
    }

    public function destroy(User $user,DatabaseNotification $notification){
        return $user->id == $notification->notifiable->id;
    }

}
