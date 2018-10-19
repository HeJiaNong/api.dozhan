<?php
namespace App\Transformers;

use App\Models\Category;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['albums','avs'];

    public function transform(Category $category){
        return [
            'id' => $category->id,
            'name' => $category->name,
            'description' => $category->description,
            'created_at' => $category->created_at->toDateTimeString(),
            'updated_at' => $category->updated_at->toDateTimeString(),
        ];
    }

    public function includeAlbums(Category $category){
        return $this->collection($category->albums,new AlbumTransformer());
    }

    public function includeAvs(Category $category){
        return $this->collection($category->avs,new AvTransformer());
    }
}