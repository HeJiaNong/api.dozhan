<?php
namespace App\Observers;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

use App\Models\Favour;

class FavourObserver
{
    public function created(Favour $favour){
        //TODO 点赞，消息通知
    }

}