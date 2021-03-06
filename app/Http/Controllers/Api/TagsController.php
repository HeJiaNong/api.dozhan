<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\TagRequest;
use App\Models\Tag;
use App\Transformers\AvTransformer;
use App\Transformers\TagTransformer;
use App\Transformers\WorkTransformer;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    public function __construct()
    {
        $this->middleware('api.auth')->except(['index','show']);
    }


    //获取所有标签
    public function index(){
        return $this->response->collection(Tag::all(),new TagTransformer());
    }

    //新增标签
    public function store(TagRequest $request){
        $data = $request->only('name');

        Tag::create($data);

        return $this->response->created();
    }

    //修改标签
    public function update(Tag $tag,TagRequest $request){
        $this->authorize('update',Tag::class);
        $data = $request->only(['name']);

        $tag->update($data);

        return $this->response->item($tag,new TagTransformer());
    }

    //删除标签
    public function destroy(Tag $tag){
        $this->authorize('destroy',Tag::class);

        $tag->delete();

        return $this->response->noContent();
    }

    //标签信息
    public function show(Tag $tag){
        return $this->response->item($tag,new TagTransformer());
    }
}
