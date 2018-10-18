<?php
namespace App\Transformers;

use App\Models\Image;
use League\Fractal\TransformerAbstract;

class ImageTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['user'];

    public function transform(Image $image){
        return [
            'id' => $image->id,
            'user_id' => $image->user_id,
            'scene' => $image->scene,
            'mime' => $image->mime,
            'key' => $image->key,
            'created_at' => $image->created_at->toDateTimeString(),
            'updated_at' => $image->updated_at->toDateTimeString(),
        ];
    }

    public function includeUser(Image $image){
        return $this->item($image->user,new UserTransformer());
    }
}