<?php
namespace App\Transformers;

use App\Models\Banner;
use League\Fractal\TransformerAbstract;

class BannerTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['user'];

    public function transform(Banner $banner){
        return [
            'id' => $banner->id,
            'description' => htmlspecialchars($banner->description),
            'link_url' => htmlspecialchars($banner->link_url),
            'img_url' => htmlspecialchars($banner->img_url),
            'created_at' => $banner->created_at->toDateTimeString(),
            'updated_at' => $banner->updated_at->toDateTimeString(),
        ];
    }

}