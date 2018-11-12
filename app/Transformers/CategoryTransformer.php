<?php
namespace App\Transformers;

use App\Models\Category;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['works','cover'];

    protected $defaultIncludes = ['cover'];

    public function transform(Category $category){
        return [
            'id' => $category->id,
            'name' => $category->name,
            'cover_id' => $category->cover_id,
            'description' => $category->description,
            'created_at' => $category->created_at->toDateTimeString(),
            'updated_at' => $category->updated_at->toDateTimeString(),
        ];
    }

    public function includeWorks(Category $category){
        return $this->collection($category->works,new WorkTransformer());
    }

    public function includeCover(Category $category){
        return $this->item($category->cover,new ResourceTransformer());
    }
}