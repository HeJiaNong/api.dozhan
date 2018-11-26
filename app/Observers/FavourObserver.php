<?php
namespace App\Observers;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

use App\Models\Favour;

class FavourObserver
{
    public function created(Favour $favour){
        //TODO 点赞对应模型点赞总数+1

        //TODO 点赞消息通知队列

        //TODO 用户表消息通知数+1

        dd(666,$favour);
    }

}