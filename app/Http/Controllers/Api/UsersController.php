<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UserRequest;
use App\Models\User;
use App\Transformers\UserTransformer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UsersController extends Controller
{
    //邮箱认证注册用户
    public function emailRegister(UserRequest $request){
        if (!$request->key){
            return $this->response->error('key不能为空',422);
        }

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

    //用户注册
    public function store(UserRequest $request,User $user){
        //获取 Cache 中的数据
        $data = Cache::get($request->key);

        //如果用户的邮箱与缓存邮箱不匹配或者验证码不正确
        if ($request->email !== $data['email'] || $request->code !== $data['code']){
            Cache::forget($request->key);
            return $this->response->errorUnauthorized('验证码错误');
        }

        //创建用户
        $user = $user->create([
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        //清除 此条验证码缓存
        Cache::forget($request->key);

        //dingo 封装的方法，就是返回 201 的状态码
        return $this->response->item($user,new UserTransformer())->setMeta([
            'access_token' => Auth::guard('api')->fromUser($user),
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
        ])->setStatusCode(201);
    }

    //获取用户个人信息
    public function me(){
        return $this->response->item($this->user(),new UserTransformer());
    }

    //修改用户信息
    public function update(UserRequest $request){
        $user = $this->user();

        //只取出请求中的一部分数据
        $attributes = $request->only(['name','avatar','phone_number','qq_number']);

        $user->update($attributes);

        return $this->response->item($user,new UserTransformer());
    }
}
