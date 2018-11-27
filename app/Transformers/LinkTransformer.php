<?php
namespace App\Transformers;

use App\Models\Link;
use League\Fractal\TransformerAbstract;

class LinkTransformer extends TransformerAbstract
{
    public function transform(Link $link){
        return [
            'id' => $link->id,
            'name' => htmlspecialchars($link->name),
            'description' => htmlspecialchars($link->description),
            'link' => htmlspecialchars($link->link),
            'created_at' => $link->created_at->toDateTimeString(),
            'updated_at' => $link->updated_at->toDateTimeString(),
        ];
    }

}