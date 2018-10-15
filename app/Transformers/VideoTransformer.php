<?php
namespace App\Transformers;

use App\Models\Video;
use League\Fractal\TransformerAbstract;

class VideoTransformer extends TransformerAbstract
{
    public function transform(Video $video){
        return [
            'id' => $video->id,
            'user_id' => $video->user_id,
            'mime' => $video->mime,
            'key' => $video->key,
            'created_at' => $video->created_at->toDateTimeString(),
            'updated_at' => $video->updated_at->toDateTimeString(),
        ];
    }
}