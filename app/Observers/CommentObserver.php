<?php
namespace App\Observers;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored
use App\Models\Comment;
use Illuminate\Support\Facades\DB;

class CommentObserver
{
    public function deleted(Comment $comment){
        //评论删除后，该评论下的所有回复都将会被删除
        DB::table('comments')->where('parent_id',$comment->id)->delete();
    }

    public function created(Comment $comment){

        $av = $comment->av;
        //增加视频的评论数量+1
        $av->increment('comment_count');
        //当有评论时，通知视频作者
        $av->user->notify(new \App\Notifications\Comment($comment));

        //如果是回复，则回复的目标用户也会收到消息
        if ($comment->target_id){
            $comment->target->notify(new \App\Notifications\Comment($comment));
        }
    }
}