<?php
namespace App\Transformers;

use App\Models\Tag;
use League\Fractal\TransformerAbstract;

class TagTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['works'];

    public function transform(Tag $tag){
        return [
            'id' => $tag->id,
            'name' => $tag->name,
            'use_count' => $tag->use_count,
            'created_at' => $tag->created_at->toDateTimeString(),
            'updated_at' => $tag->updated_at->toDateTimeString(),
        ];
    }

    public function includeWorks(Tag $tag){
        return $this->collection($tag->works,new WorkTransformer());
    }
}