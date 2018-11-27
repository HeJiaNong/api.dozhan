<?php

namespace App\Http\Controllers\Api;


use App\Models\Link;
use App\Transformers\LinkTransformer;
use Illuminate\Http\Request;

class LinksController extends Controller
{
    public function __construct()
    {
        $this->middleware('api.auth')->except(['index']);
    }

    public function index(){
        return $this->response->collection(Link::all(),new LinkTransformer());
    }

    public function store(Link $link,Request $request){
        $this->authorize('create',$link);

        $link->fill($request->all())->save();

        return $this->response->item($link,new LinkTransformer());
    }

    public function update(Link $link,Request $request){
        $this->authorize('update',$link);

        $link->update($request->all());

        return $this->response->item($link,new LinkTransformer());
    }

    public function destroy(Link $link){
        $this->authorize('destroy',$link);

        $link->delete();

        return $this->response->noContent();
    }
}
