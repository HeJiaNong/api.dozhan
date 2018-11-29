<?php
namespace App\Observers;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

use App\Models\Favour;

class FavourObserver
{
    public function created(Favour $favour){
        //点赞对应模型点赞总数+1
        $favour->favourable->increment('favour_count');

        //点赞消息通知
        $favour->favourable->user->notify(new \App\Notifications\Favour($favour->favourable));
    }

    public function deleted(Favour $favour){
        //点赞对应模型点赞总数-1
        $favour->favourable->decrement('favour_count');
    }

}