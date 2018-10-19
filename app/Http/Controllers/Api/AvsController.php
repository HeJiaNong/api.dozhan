<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AvRequest;
use App\Models\Av;
use App\Transformers\AvTransformer;
use Illuminate\Http\Request;

class AvsController extends Controller
{
    //获取视频列表
    public function index(){
        return $this->response->paginator(Av::paginate(20),new AvTransformer());
    }

    //发布视频
    public function store(AvRequest $request){
        $data = $request->only(['name','description','album_id','video_id','image_id','category_id']);
        $data['user_id'] = $this->user()->id;

        //获取标签信息
        $tag_ids = $request->tag_ids;

        //入库
        $av = Av::create($data);

        //多对多关联更新
        $av->tag()->attach(json_decode($tag_ids));

        return $this->response->created();
    }

    //修改视频
    public function update(Av $av,AvRequest $request){
        //权限验证
        $this->authorize('update',$av);

        $data = $request->only(['name','description','album_id','image_id','category_id']);
        $tag_ids = json_decode($request->tag_ids);

        //更新
        $av->update($data);

        //同步标签
        $av->tag()->sync($tag_ids);

        return $this->response->item($av,new AvTransformer());
    }

    //删除视频
    public function destroy(Av $av){
        //权限验证
        $this->authorize('destroy',$av);

        //执行删除
        $av->delete();

        return $this->response->noContent();
    }

    //视频详情
    public function show(Av $av){
        return $this->response->item($av,new AvTransformer());
    }
}
