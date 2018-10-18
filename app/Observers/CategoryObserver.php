<?php
namespace App\Observers;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored
use App\Models\Album;
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
        //分类删除后，该分类下所有的视频和专辑都会转移至id为1的分类下
        DB::table('albums')->where('category_id',$category->id)->update(['category_id' => 1]);
        DB::table('avs')->where('category_id',$category->id)->update(['category_id' => 1]);
    }
}