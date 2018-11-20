<?php
namespace App\Transformers;

use App\Models\Comment;
use App\Models\User;
use League\Fractal\TransformerAbstract;

class CommentTransformer extends TransformerAbstract
{

    public function __construct($default = null)
    {
        //加入需要默认展示的transformer
        if ($default){

            $this->defaultIncludes[] = $default;
        }
    }

    protected $availableIncludes = ['user','favours','replies'];

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

    public function includeUser(Comment $comment){
        return $this->item($comment->user,new UserTransformer());
    }

    public function includeFavours(Comment $comment){
        return $this->collection($comment->favours,new FavourTransformer());
    }

    public function includeReplies(Comment $comment){
        return $this->collection($comment->replies,new ReplyTransformer());
    }

}