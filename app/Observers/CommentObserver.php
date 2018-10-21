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
}