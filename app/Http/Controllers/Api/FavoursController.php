<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\FormRequest as Request;
use App\Models\Comment;
use App\Models\Favour;
use App\Models\User;
use App\Models\Work;
use App\Transformers\FavourTransformer;

class FavoursController extends Controller
{
    protected $per_page = 20;

    public function __construct()
    {
        $this->middleware('api.auth')->except(['workIndex','commentIndex']);;
    }

    public function meIndex(){
        return $this->response->paginator($this->user()->favours()->paginate($this->per_page),new FavourTransformer());
    }

    public function workIndex(Work $work){
        return $this->response->paginator($work->favours()->paginate($this->per_page),new FavourTransformer());
    }

    public function commentIndex(Comment $comment){
        return $this->response->paginator($comment->favours()->paginate($this->per_page),new FavourTransformer());
    }

    public function workStore(Work $work){
        $favour = $work->favours()->firstOrCreate(['user_id' => $this->user->id]);

        return $this->response->item($favour,new FavourTransformer());
    }

    public function commentStore(Comment $comment){
        $favour = $comment->favours()->firstOrCreate(['user_id' => $this->user->id]);
        return $this->response->item($favour,new FavourTransformer());
    }

    public function destroy(Favour $favour){
        $this->authorize('destroy',$favour);
        $favour->delete();
        return $this->response->noContent();
    }

}
