<?php
namespace App\Transformers;

use App\Models\Work;
use League\Fractal\TransformerAbstract;

class WorkTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['tags','category','comments','user','favours'];

    public function transform(Work $work){
        return [
            'id' => $work->id,
            'user_id' => $work->user_id,
            'category_id' => $work->category_id,
            'name' => $work->name,
            'description' => $work->description,
            'url' => $work->url,
            'cover' => $work->cover,
            'page_view' => $work->page_view,
            'comment_count' => $work->comment_count,
            'favour_count' => $work->favour_count,
            'created_at' => $work->created_at->toDateTimeString(),
            'updated_at' => $work->updated_at->toDateTimeString(),
        ];
    }

    public function includeTags(Work $work){
        return $this->collection($work->tags,new TagTransformer());
    }

    public function includeCategory(Work $work){
        return $this->item($work->category,new CategoryTransformer());
    }

    public function includeComments(Work $work){
        return $this->collection($work->comments,new CommentTransformer());
    }

    public function includeUser(Work $work){
        return $this->item($work->user,new UserTransformer());
    }

    public function includeFavours(Work $work){
        return $this->collection($work->favours,new FavourTransformer());
    }

}