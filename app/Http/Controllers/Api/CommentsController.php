<?php

namespace App\Http\Controllers\Api;

use App\Models\Comment;
use App\Models\Work;
use App\Transformers\CommentTransformer;
use App\Http\Requests\Api\FormRequest as Request;
use Illuminate\Support\Facades\Validator;

class CommentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('api.auth')->except(['index','workIndex','show']);
    }

    //获取评论列表
    public function index(){
        return $this->response->paginator(Comment::where('parent_id',null)->paginate(10),new CommentTransformer());
    }

    //获取某视频下的一级评论列表
    public function workIndex(Work $work){
        //一级评论列表
        return $this->response->paginator($work->comments()->where(['parent_id' => null])->paginate(10),new CommentTransformer());
    }

    //评论详情
    public function show(Comment $comment){
        //如果这条评论是条二级评论,直接返回上级评论的列表
        if ($comment->parent_id){
            return $this->response->item(Comment::find($comment->parent_id),new CommentTransformer('replies'));
        }
        return $this->response->item($comment,new CommentTransformer());
    }

    //增加作品评论
    public function workStore(Work $work,Request $request,Comment $comment){
        $v = Validator::make($request->all(),[
            'content' => 'required|string|max:255',
            'parent_id' => 'required_with:target_id|integer|exists:comments,id',
            'target_id' => 'integer|exists:users,id',
        ]);

        $v->validate();

        $comment->content = $request['content'];
        $comment->user_id = $this->user()->id;
        if ($comment->parent_id = $request->parent_id){
            //获取父级评论实例
            $parent = Comment::findOrFail($request->parent_id);

            //不能回复自己
            if ($request->target_id == $this->user->id){
                return $this->response->error('不能回复自己',422);
            }

            //二级评论的多态映射表必须和一级评论的相同
            if ($parent->commentable_id != $work->id || $parent->commentable_type != $work->getTable()){
                return $this->response->error('该评论不属于此作品',422);
            }

            //如果有回复用户，那么回复的用户必须也是此父级评论的回复
            if ($comment->target_id = $request->target_id){
                if (!in_array($request->target_id,$parent->replies()->pluck('user_id')->toArray())){
                    return $this->response->error('此用户不在评论列表',422);
                }
            }
        }

        //调用模型关联天添加评论
        $work->comments()->save($comment);

        return $this->response->item($comment,new CommentTransformer());
    }

    //修改评论
    public function update(Comment $comment,Request $request){
        $this->authorize('update',$comment);

        Validator::make($request->all(),[
            'content' => 'required|string|max:255',
        ])->validate();

        $comment->fill($request->all())->save();

        return $this->response->item($comment,new CommentTransformer());
    }

    //删除评论
    public function destroy(Comment $comment){
        $this->authorize('destroy',$comment);

        $comment->delete();

        return $this->response->noContent();
    }
}


