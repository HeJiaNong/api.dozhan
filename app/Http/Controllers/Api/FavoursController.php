<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\FormRequest as Request;
use App\Models\Comment;
use App\Models\Favour;
use App\Models\Work;
use App\Transformers\FavourTransformer;

class FavoursController extends Controller
{
    public function __construct()
    {
        $this->middleware('api.auth')->except(['workIndex','commentIndex']);;
    }

    public function meIndex(){
        return $this->response->collection($this->user()->favours,new FavourTransformer());
    }

    public function workIndex(Work $work){
        return $this->response->collection($work->favours,new FavourTransformer());
    }

    public function commentIndex(Comment $comment){
        return $this->response->collection($comment->favours,new FavourTransformer());
    }

    public function workStore(Work $work){
        $favour = $work->favours()->firstOrCreate(['user_id' => $this->user->id]);

        return $this->response->item($favour,new FavourTransformer());
    }

    public function destroy(Favour $favour){
        $this->authorize('destroy',$favour);
        $favour->delete();
        return $this->response->noContent();
    }

    public function commentStore(Comment $comment){
        $favour = $comment->favours()->firstOrCreate(['user_id' => $this->user->id]);

        return $this->response->item($favour,new FavourTransformer());
    }
}
