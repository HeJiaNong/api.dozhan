<?php
namespace App\Transformers;

use App\Models\Work;
use League\Fractal\TransformerAbstract;

class WorkTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['tags','category','comments','user','favours','video','cover'];

    protected $defaultIncludes = ['video','cover'];

    public function transform(Work $work){
        return [
            'id' => $work->id,
            'user_id' => $work->user_id,
            'category_id' => $work->category_id,
            'name' => htmlspecialchars($work->name),
            'description' => htmlspecialchars($work->description),
            'video_id' => $work->video_id,
            'cover_id' => $work->cover_id,
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

    /*
     * 获取视频资源
     */
    public function includeVideo(Work $work){
        return $this->item($work->video,new ResourceTransformer(false));
    }

    /*
     * 获取封面资源
     */
    public function includeCover(Work $work){
        return $this->item($work->cover,new ResourceTransformer(true));
    }

}