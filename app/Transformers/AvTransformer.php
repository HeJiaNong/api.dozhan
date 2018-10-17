<?php
namespace App\Transformers;

use App\Models\Av;
use League\Fractal\TransformerAbstract;

class AvTransformer extends TransformerAbstract
{
    public function transform(Av $av){
        return [
            'id' => $av->id,
            'name' => $av->name,
            'description' => $av->description,
            'user_id' => $av->user_id,
            'album_id' => $av->album_id,
            'url_id' => $av->url_id,
            'cover_id' => $av->cover_id,
            'created_at' => $av->created_at->toDateTimeString(),
            'updated_at' => $av->updated_at->toDateTimeString(),
        ];
    }
}