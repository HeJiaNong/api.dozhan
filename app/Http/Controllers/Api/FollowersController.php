<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\FormRequest as Request;
use App\Models\User;
use App\Transformers\UserTransformer;

class FollowersController extends Controller
{

    protected $per_page = 20;

    public function __construct()
    {
        $this->middleware('api.auth')->except(['userFollowers','userFollowings']);
    }

    public function meFollowers(){
        return $this->response->paginator($this->user()->followers()->paginate($this->per_page),new UserTransformer());
    }

    public function meFollowings(){
        return $this->response->paginator($this->user()->followings()->paginate($this->per_page),new UserTransformer());
    }

    public function userFollowers(User $user){
        return $this->response->paginator($user->followers()->paginate($this->per_page),new UserTransformer());
    }

    public function userFollowings(User $user){
        return $this->response->paginator($user->followings()->paginate($this->per_page),new UserTransformer());
    }

    public function follow(User $user){
        $this->user()->follow([$user->id]);
        //TODO 订阅消息通知
        return $this->response->noContent();
    }

    public function unfollow(User $user){
        $this->user()->unfollow([$user->id]);
        return $this->response->noContent();
    }


}
