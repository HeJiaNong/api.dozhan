<?php
namespace App\Transformers;

use App\Models\Comment;
use App\Models\User;
use League\Fractal\TransformerAbstract;

class ReplyTransformer extends TransformerAbstract
{
    protected $defaultIncludes = ['target'];

    public function transform(Comment $comment){
        return [
            'id' => $comment->id,
            'content' => $comment->content,
            'favour_count' => $comment->favour_count,
//            'user_id' => $comment->user_id,
//            'parent_id' => $comment->parent_id,
//            'target_id' => $comment->target_id,
//            'commentable_id' => $comment->commentable_id,
//            'commentable_type' => $comment->commentable_type,
            'created_at' => $comment->created_at->diffForHumans(),
//            'updated_at' => $comment->updated_at->diffForHumans(),
        ];
    }

    public function includeTarget(Comment $comment){
        if (isset($comment->target)){
            return $this->item($comment->target,new TargetTransformer());
        }
    }

}