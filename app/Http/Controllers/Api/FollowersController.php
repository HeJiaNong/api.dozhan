<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\FormRequest as Request;
use App\Models\User;
use App\Transformers\UserTransformer;

class FollowersController extends Controller
{

    public function __construct()
    {
        $this->middleware('api.auth')->except(['userFollowers','userFollowed']);
    }

    public function meFollowers(){
        return $this->response->collection($this->user()->followers,new UserTransformer());
    }

    public function meFollowed(){
        return $this->response->collection($this->user()->followed,new UserTransformer());
    }

    public function userFollowers(User $user){
        return $this->response->collection($user->followers,new UserTransformer());
    }

    public function userFollowed(User $user){
        return $this->response->collection($user->followed,new UserTransformer());
    }

    public function store(User $user){
        $this->user->followed()->syncWithoutDetaching($user->id);
        return $this->response->noContent();
    }

    public function destroy(User $user){
        $this->user->followed()->detach($user->id);
        return $this->response->noContent();
    }


}
