<?php
namespace App\Observers;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored
use App\Models\Tag;
use Illuminate\Support\Facades\DB;

class TagObserver
{
    public function deleted(Tag $tag){
        //标签删除后，在关联表中也一并删除
        DB::table('tag_work')->where('tag_id',$tag->id)->delete();
    }

}