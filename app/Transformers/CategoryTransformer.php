<?php
namespace App\Transformers;

use App\Models\Category;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['album','av'];

    public function transform(Category $category){
        return [
            'id' => $category->id,
            'name' => $category->name,
            'description' => $category->description,
            'created_at' => $category->created_at->toDateTimeString(),
            'updated_at' => $category->updated_at->toDateTimeString(),
        ];
    }

    public function includeAlbum(Category $category){
        return $this->collection($category->album,new AlbumTransformer());
    }

    public function includeAv(Category $category){
        return $this->collection($category->av,new AvTransformer());
    }
}