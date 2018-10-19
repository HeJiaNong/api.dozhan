<?php
namespace App\Transformers;

use App\Models\Tag;
use League\Fractal\TransformerAbstract;

class TagTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['av'];

    public function transform(Tag $tag){
        return [
            'id' => $tag->id,
            'name' => $tag->name,
            'created_at' => $tag->created_at->toDateTimeString(),
            'updated_at' => $tag->updated_at->toDateTimeString(),
        ];
    }

    public function includeAv(Tag $tag){
        return $this->collection($tag->av,new AvTransformer());
    }
}