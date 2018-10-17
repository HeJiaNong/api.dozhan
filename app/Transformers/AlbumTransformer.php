<?php
namespace App\Transformers;

use App\Models\Album;
use League\Fractal\TransformerAbstract;

class AlbumTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['user','category'];

    public function transform(Album $album){
        return [
            'id' => $album->id,
            'name' => $album->name,
            'description' => $album->description,
            'user_id' => $album->user_id,
            'category_id' => $album->category_id,
            'created_at' => $album->created_at->toDateTimeString(),
            'updated_at' => $album->updated_at->toDateTimeString(),
        ];
    }

    public function includeUser(Album $album){
        return $this->item($album->user,new UserTransformer());
    }

    public function includeCategory(Album $album){
        return $this->item($album->category);
    }
}