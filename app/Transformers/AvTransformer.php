<?php
namespace App\Transformers;

use App\Models\Av;
use League\Fractal\TransformerAbstract;

class AvTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['tag','category','album','comment','user','video','image'];

    public function transform(Av $av){
        return [
            'id' => $av->id,
            'name' => $av->name,
            'description' => $av->description,
            'user_id' => $av->user_id,
            'album_id' => $av->album_id,
            'video_id' => $av->video_id,
            'image_id' => $av->image_id,
            'created_at' => $av->created_at->toDateTimeString(),
            'updated_at' => $av->updated_at->toDateTimeString(),
        ];
    }

    public function includeTag(Av $av){
        return $this->collection($av->tag,new TagTransformer());
    }

    public function includeCategory(Av $av){
        return $this->item($av->category,new CategoryTransformer());
    }

    public function includeAlbum(Av $av){
        return $this->item($av->album,new AlbumTransformer());
    }

    public function includeComment(Av $av){
        return $this->collection($av->comment,new CommentTransformer());
    }

    public function includeUser(Av $av){
        return $this->item($av->user,new UserTransformer());
    }

    public function includeVideo(Av $av){
        return $this->item($av->video,new VideoTransformer());
    }

    public function includeImage(Av $av){
        return $this->item($av->image,new ImageTransformer());
    }
}