<?php
namespace App\Transformers;

use App\Models\Comment;
use League\Fractal\TransformerAbstract;

class CommentTransformer extends TransformerAbstract
{
    public function transform(Comment $comment){
        return [
            'id' => $comment->id,
            'comment' => $comment->comemnt,
            'user_id' => $comment->user_id,
            'av_id' => $comment->av_id,
            'created_at' => $comment->created_at->toDateTimeString(),
            'updated_at' => $comment->updated_at->toDateTimeString(),
        ];
    }
}