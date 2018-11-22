<?php
namespace App\Observers;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

use App\Models\Favour;

class FollowerObserver
{
    public function created(){
        //TODO 订阅用户，消息通知
    }

    public function deleted(){
        //TODO 取消订阅,消息通知
    }

}