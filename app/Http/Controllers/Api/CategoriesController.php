<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CategoryRequest;
use App\Models\Album;
use App\Models\Category;
use App\Transformers\AlbumTransformer;
use App\Transformers\AvTransformer;
use App\Transformers\CategoryTransformer;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    //获取分类列表
    public function index(){
        return $this->response->item(Category::all(),new CategoryTransformer());
    }

    //获取某分类下的所有专辑
    public function albumsIndex(Category $category){
        return $this->response->paginator($category->album()->paginate(20),new AlbumTransformer());
    }

    //获取某分类下的所有视频
    public function avsIndex(Category $category){
        return $this->response->paginator($category->av()->paginate(20),new AvTransformer());
    }

    //新增分类
    public function store(CategoryRequest $request){
        //权限验证
        $this->authorize('create',Category::class);

        $categories = $request->only(['name','description']);

        Category::create($categories);

        return $this->response->created();
    }

    //删除分类
    public function destroy(Category $category){
        //权限验证
        $this->authorize('destroy',$category);

        //删除分类
        $category->delete();

        //返回响应
        return $this->response->noContent();
    }

    //修改分类
    public function update(Category $category,CategoryRequest $request){
        //权限验证
        $this->authorize('update',Category::class);

        $data = $request->only(['name','description']);

        $category->update($data);

        return $this->response->item($category,new CategoryTransformer());
    }

}
