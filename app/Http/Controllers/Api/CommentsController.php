<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CommentRequest;
use App\Models\Av;
use App\Models\Comment;
use App\Models\Work;
use App\Transformers\CommentTransformer;
use App\Transformers\ReplyTransformer;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;

class CommentsController extends Controller
{
    //获取评论列表
    public function index(){
        return $this->response->paginator(Comment::where('parent_id',null)->paginate(20),new CommentTransformer());
    }

    //获取某视频下的一级评论列表
    public function worksIndex(Work $work){
        //一级评论列表
        return $this->response->paginator($work->comments()->where(['parent_id' => null])->paginate(20),new CommentTransformer());
    }

    //评论详情
    public function show(Comment $comment){
        //如果这条评论是条二级评论,直接返回上级评论的列表
        if ($comment->parent_id){
            return $this->response->item(Comment::find($comment->parent_id),new CommentTransformer('replies'));
        }
        return $this->response->item($comment,new CommentTransformer());
    }

    //增加评论
    public function store(CommentRequest $request,Comment $comment){
        //通过「多态映射表」名称获取对应的操作模型
        $commentable_type = Relation::getMorphedModel($request->commentable_type);
        //生成多态映射表对应实例
        $commentable = $commentable_type::find($request->commentable_id);

        $comment->content = $request['content'];
        $comment->user_id = $this->user()->id;

        //二级评论检测
        if ($comment->parent_id = $request->parent_id){
            //获取父级评论实例
            $parent = Comment::find($request->parent_id);

            //二级评论的多态映射表必须和一级评论的相同
            if ($parent->commentable_id != $request->commentable_id || $parent->commentable_type != $request->commentable_type){
                return $this->response->error('参数错误',422);
            }

            //如果此条评论是二级评论，那么它的父级评论就只能是一级评论
            if (!empty($parent->parent_id)){
                return $this->response->error('参数错误',422);
            }

            //如果有回复用户，那么回复的用户必须也是此父级评论的回复
            if ($comment->target_id = $request->target_id){
                if (!in_array($request->target_id,$parent->replies()->pluck('user_id')->toArray())){
                    return $this->response->error('参数错误',422);
                }
            }
        }

        //以多态映射表调用模型关联天添加评论
        $commentable->comments()->save($comment);

        $this->response->created();
    }

    //更新评论
    public function update(Comment $comment,CommentRequest $request){
        $this->authorize('update',$comment);

        $data = $request->only(['comment','target_id']);

        $comment->update($data);

        return $this->response->item($comment,new CommentTransformer());
    }

    //删除回复
    public function destroy(Comment $comment){
        $this->authorize('destroy',$comment);

        $comment->delete();

        return $this->response->noContent();
    }
}


