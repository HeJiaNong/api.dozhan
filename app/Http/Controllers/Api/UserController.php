<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    public function store(UserRequest $request){

        //获取邮箱数据
        $data = Cache::get($request->key);

        //如果缓存中没有值或者邮箱已存在
        if (empty($data) || !empty(User::where('email',$data['email'])->get()->toArray())){
            //清除缓存
            Cache::forget($request->key);
            return $this->response->error('链接已失效',422);
        }

        $data['password'] = bcrypt($data['password']);

        //新建用户
        User::create($data);

        //清除缓存
        Cache::forget($request->key);

        //跳转到登陆页面
        return redirect(route('login'));
    }
}
