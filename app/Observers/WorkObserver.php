<?php
namespace App\Observers;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

use App\Models\Work;

class WorkObserver
{
    public function deleted(Work $work){
        //如果不是软删除
        if (!$work->trashed()){
            //删除该视频的所有标签
            $work->tags()->detach();

            //删除改作品的所有评论
            $work->comments()->forceDelete();

            //删除改作品的所有点赞
            $work->favours()->forceDelete();
        }
    }

}