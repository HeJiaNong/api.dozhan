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

        $commentable = $comment->commentable;

        $commentable->increment('comment_count');

        if ($comment->target_id){
            //如果是回复，则回复的目标用户也会收到消息
            $comment->target->notify(new \App\Notifications\Comment($comment));
        }else{
            //当有评论时，通知视频作者
            $commentable->user->notify(new \App\Notifications\Comment($comment));
        }
    }
}