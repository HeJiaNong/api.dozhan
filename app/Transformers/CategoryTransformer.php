<?php
namespace App\Transformers;

use App\Models\Category;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['works'];

    public function transform(Category $category){
        return [
            'id' => $category->id,
            'name' => $category->name,
            'cover' => $category->cover,
            'description' => $category->description,
            'created_at' => $category->created_at->toDateTimeString(),
            'updated_at' => $category->updated_at->toDateTimeString(),
        ];
    }

    public function includeWorks(Category $category){
        return $this->collection($category->works,new WorkTransformer());
    }
}