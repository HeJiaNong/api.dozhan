<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Transformers\NotificationTransformer;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationsController extends Controller
{
    //获取当前用户的所有通知
    public function index(){
        return $this->response->paginator($this->user()->notifications()->paginate(20),new NotificationTransformer());
    }

    //设置当前用户所有未读通知有通知为已读
    public function readAll(){
        //标记已读
        $this->user()->unreadNotifications->markAsRead();

        $this->user()->update(['notification_count' => 0]);

        return $this->response->noContent();
    }

    //设置某条通知为已读
    public function readSingle(DatabaseNotification $notification){
        //权限验证，只能操作自己的消息
        $this->authorize('readSingle',$notification);

        //如果已经标记过已读，就直接跳出
        if ($notification->read_at){
            return $this->response->noContent();
        }

        $this->user()->decrement('notification_count');

        //标记为已读
        $notification->markAsRead();

        return $this->response->noContent();
    }

    //获取当前用户的未读通知
    public function unreadNotifications(){
        return $this->response->paginator($this->user()->unreadNotifications()->paginate(20),new NotificationTransformer());
    }

    //获取当前用户的已读通知
    public function markReadNotifications(){
        return $this->response->paginator($this->user()->notifications()->where('read_at','!=',null)->paginate(20),new NotificationTransformer());
    }

    //获取当前用户的未读通知统计
    public function stats(){
        return $this->user()->unreadNotifications->count();
    }

}
