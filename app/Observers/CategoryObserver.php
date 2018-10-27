<?php
namespace App\Observers;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoryObserver
{
    public function deleting(Category $category){
        if ($category->id == 1){
            abort(401);
        }
    }

    public function deleted(Category $category){
        //分类删除后，该分类下所有的作品都会转移至id为1的分类下
        DB::table('works')->where('category_id',$category->id)->update(['category_id' => 1]);
    }
}