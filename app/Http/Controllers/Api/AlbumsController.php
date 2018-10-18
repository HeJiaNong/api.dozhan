<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AlbumsRequest;
use App\Models\Album;
use App\Models\User;
use App\Transformers\AlbumTransformer;
use App\Transformers\AvTransformer;
use App\Transformers\CategoryTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlbumsController extends Controller
{
    //获取指定用户发布的专辑
    public function userIndex(User $user,Request $request){
        return $this->response->paginator($user->album()->paginate(20),new AlbumTransformer());
    }

    //专辑列表
    public function index(){
        return $this->response->paginator(Album::paginate(20),new AlbumTransformer());
    }

    //获取某专辑对应的分类
    public function CategoryIndex(Album $album){
        return $this->response->item($album->category,new CategoryTransformer());
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

    //更新专辑信息
    public function update(Album $album,AlbumsRequest $request){
        //权限验证
        $this->authorize('update',$album);

        //只取出请求中的一部分数据
        $attributes = $request->only(['name','description','category_id']);

        $album->update($attributes);

        return $this->response->array($album->toArray());
    }

    //删除专辑
    public function destroy(Album $album){
        //权限验证
        $this->authorize('destroy',$album);

        //执行删除
        $album->delete();

        return $this->response->noContent();
    }

    //获取某专辑下的所有视频
    public function AvsIndex(Album $album){
        return $this->response->paginator($album->av()->paginate(20),new AvTransformer());
    }

}
