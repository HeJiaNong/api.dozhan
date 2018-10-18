<?php

namespace App\Http\Controllers\Api;

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
        return $this->response->collection($category->album,new AlbumTransformer());
    }

    public function avsIndex(Category $category){
        return $this->response->collection($category->av,new AvTransformer());
    }

    //新增分类
    public function store(){
        //todo 需要权限认证,管理员或者站长才能操作
    }

}
