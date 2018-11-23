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
        $this->middleware('api.auth')->except(['userFollowers','userFollowed']);
    }

    public function meFollowers(){
        return $this->response->paginator($this->user()->followers()->paginate($this->per_page),new UserTransformer());
    }

    public function meFollowed(){
        return $this->response->paginator($this->user()->followed()->paginate($this->per_page),new UserTransformer());
    }

    public function userFollowers(User $user){
        return $this->response->paginator($user->followers()->paginate($this->per_page),new UserTransformer());
    }

    public function userFollowed(User $user){
        return $this->response->paginator($user->followed()->paginate($this->per_page),new UserTransformer());
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
