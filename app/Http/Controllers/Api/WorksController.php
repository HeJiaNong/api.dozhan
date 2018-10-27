<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\WorkRequest;
use App\Models\User;
use App\Models\Work;
use App\Transformers\WorkTransformer;
use Illuminate\Http\Request;

class WorksController extends Controller
{
    /*
     * 获取作品列表
     */
    public function index(){
        return $this->response->paginator(Work::paginate(20),new WorkTransformer());
    }

    /*
     * 发布作品
     */
    public function store(WorkRequest $request,Work $work){
        $work->fill($request->only(['name','description','category_id','url','cover','tag_ids']));
        $work->user_id = $this->user()->id;

        //入库
        $work->save();

        //更新标签数据
        if ($tag_ids = json_decode($request->tag_ids)){
            $work->tags()->sync($tag_ids);
            //更新标签使用数量
            foreach ($work->tags as $tag){
                $tag->increment('use_count');
            }
        }

        return $this->response->created();
    }

    /*
     * 修改作品
     */
    public function update(Work $work,WorkRequest $request){
        //权限验证
        $this->authorize('update',$work);

        //更新作品数据
        $work->update($request->only(['name','description','category_id','cover']));

        //更新标签数据
        if ($tag_ids = json_decode($request->tag_ids)){
            $work->tags()->sync($tag_ids);
        }

        //返回Transformer
        return $this->response->item($work,new WorkTransformer());
    }

    /*
     * 软删除作品
     */
    public function destroy(Work $work){
        //权限验证
        $this->authorize('destroy',$work);

        //执行删除
        $work->delete();

        return $this->response->noContent();
    }

    /*
     * 获取被软删除的作品
     */
    public function destroys(){
        //权限验证
        $this->authorize('destroy',$work);

        return $this->response->paginator($this->user()->works()->withTrashed()->where('deleted_at','!=',null)->paginate(20),new WorkTransformer());
    }

    /*
     * 恢复已软删除的作品
     */
    public function restore($workId){
        if (!$work = Work::withTrashed()->find($workId)){
            return $this->response->errorNotFound();
        }

        //权限验证
        $this->authorize('destroy',$work);

        //恢复
        $work->restore();

        return $this->response->created();
    }

    /*
     * 彻底删除作品
     */
    public function forceDestroy($workId){

        if (!$work = Work::withTrashed()->find($workId)){
            return $this->response->errorNotFound();
        }

        //权限验证
        $this->authorize('destroy',$work);

        //执行删除
        $work->forceDelete();

        return $this->response->noContent();
    }

    /*
     * 作品详情
     */
    public function show(Work $work){
        return $this->response->item($work,new WorkTransformer());
    }

    /*
     * 获取某用户发布的作品列表
     */
    public function userIndex(User $user){
        return $this->response->paginator($user->works()->paginate(20),new WorkTransformer());
    }
}
