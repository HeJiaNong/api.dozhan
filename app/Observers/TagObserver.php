<?php
namespace App\Observers;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored
use App\Models\Tag;
use Illuminate\Support\Facades\DB;

class TagObserver
{
    public function deleting(Tag $tag){
        if ($tag->id == 1){
            abort(401);
        }
    }

    public function deleted(Tag $tag){
        //分类删除后，该分类下所有的视频和专辑都会转移至id为1的分类下
        DB::table('av_tag')->where('tag_id',$tag->id)->delete();
    }
}