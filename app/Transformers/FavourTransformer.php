<?php
namespace App\Transformers;

use App\Models\Favour;
use League\Fractal\TransformerAbstract;

class FavourTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['user'];

    public function transform(Favour $favour){
        return [
            'id' => $favour->id,
            'user_id' => $favour->user_id,
            'favourable_id' => $favour->favourable_id,
            'favourable_type' => $favour->favourable_type,
            'created_at' => $favour->created_at->toDateTimeString(),
            'updated_at' => $favour->updated_at->toDateTimeString(),
        ];
    }

    public function includeUser(Favour $favour){
        return $this->collection($favour->user,new UserTransformer());
    }
}