<?php
namespace App\Transformers;

use App\Models\Comment;
use App\Models\User;
use League\Fractal\TransformerAbstract;

class ReplyTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['target'];

    public function transform(Comment $comment){
        return [
            'id' => $comment->id,
            'comment' => $comment->comment,
            'user_id' => $comment->user_id,
            'av_id' => $comment->av_id,
            'parent_id' => $comment->parent_id,
            'target_id' => $comment->target_id,
            'created_at' => $comment->created_at->toDateTimeString(),
            'updated_at' => $comment->updated_at->toDateTimeString(),
        ];
    }

    public function includeTarget(Comment $comment){
        if (isset($comment->target)){
            return $this->item($comment->target,new UserTransformer());
        }
    }

}