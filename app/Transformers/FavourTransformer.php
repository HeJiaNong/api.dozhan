<?php
namespace App\Transformers;

use App\Models\Favour;
use League\Fractal\TransformerAbstract;

class FavourTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['user','favourable'];

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
        return $this->item($favour->user,new UserTransformer());
    }

    public function includeFavourable(Favour $favour){
        $transformerName = $this->getFavourableTransformerName($favour->favourable);

        return $this->item($favour->favourable,new $transformerName());
    }

    protected function getFavourableTransformerName($favourable){
        return '\App\Transformers\\'.substr(get_class($favourable),strrpos(get_class($favourable),'\\')+1).'Transformer';
    }
}