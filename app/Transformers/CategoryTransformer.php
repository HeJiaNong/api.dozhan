<?php
namespace App\Transformers;

use App\Models\Category;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['works','icon'];

    protected $defaultIncludes = ['icon'];

    public function transform(Category $category){
        return [
            'id' => $category->id,
            'name' => $category->name,
            'icon_id' => $category->icon_id,
            'description' => $category->description,
            'created_at' => $category->created_at->toDateTimeString(),
            'updated_at' => $category->updated_at->toDateTimeString(),
        ];
    }

    public function includeWorks(Category $category){
        return $this->collection($category->works,new WorkTransformer());
    }

    public function includeIcon(Category $category){
        return $this->item($category->icon,new ResourceTransformer());
    }
}