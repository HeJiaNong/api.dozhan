<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CommentRequest;
use App\Models\Av;
use App\Models\Comment;
use App\Transformers\CommentTransformer;
use App\Transformers\ReplyTransformer;
use Illuminate\Http\Request;

class CommentsController extends Controller
{
    //获取一级评论列表
    public function index(){
        return $this->response->collection(Comment::where('parent_id',null)->get(),new CommentTransformer());
    }

    //获取某视频下的一级评论列表
    public function AvsIndex(Av $av){
        //一级评论列表
        return $this->response->collection($av->comment()->where(['parent_id' => null])->get(),new CommentTransformer());
    }

    //获取某一级评论下的二级评论
    public function replies(Comment $comment){
        if (isset($comment->parent_id)){
            return '这不是一级评论';
        }

        $data = Comment::where('parent_id',$comment->id)->get();


        return $this->response->collection($data,new ReplyTransformer());
    }

    //评论详情
    public function show(Comment $comment){
        return $this->response->item($comment,new CommentTransformer());
    }

    //增加评论
    public function store(CommentRequest $request){
        //接收数据
        $comment = $request->only(['comment','av_id','parent_id','target_id']);
        //当前登陆用户
        $comment['user_id'] = $this->user()->id;

        //todo 根据不同级别的评论限制不同的要求
        //todo 如果是三级评论，那么此评论的target_id(目标用户)的parent_id和Av_id必须和此评论的parent_id和Av_id一致

        //创建评论
        Comment::create($comment);

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
