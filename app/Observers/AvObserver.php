<?php
namespace App\Observers;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored
use App\Models\Av;

class AvObserver
{
    public function deleting(Av $av){
        //删除该视频的所有标签
        $av->tag()->detach();
    }
}