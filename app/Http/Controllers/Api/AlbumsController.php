<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AlbumsRequest;
use App\Models\Album;
use App\Models\User;
use App\Transformers\AlbumTransformer;
use App\Transformers\CategoryTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlbumsController extends Controller
{
    //获取指定用户发布的专辑
    public function userIndex(User $user,Request $request){
        $albums = $user->album();
        $albums = $albums->paginate(20);
        return $this->response->paginator($albums,new AlbumTransformer());
    }

    //专辑列表
    public function index(){
        $albums = Album::paginate(20);
        return $this->response->paginator($albums,new AlbumTransformer());
    }

    //获取某专辑对应的分类
    public function albumIndex(Album $album){
        $category = $album->category;
        return $this->response->item($category,new CategoryTransformer());
    }

    //发布专辑
    public function store(AlbumsRequest $request){
        $data = $request->only(['name','description','category_id']);
        //获取当前登陆用户id
        $data['user_id'] = $this->user()->id;
        //创建专辑
        $album = Album::create($data);

        return $this->response->item($album,new AlbumTransformer());
    }

    public function update(Album $album,AlbumsRequest $request){
        //权限验证
        $this->authorize('update',$album);

        //只取出请求中的一部分数据
        $attributes = $request->only(['name','description','category_id']);

        $album->update($attributes);

        return $this->response->array($album->toArray());
    }

}
