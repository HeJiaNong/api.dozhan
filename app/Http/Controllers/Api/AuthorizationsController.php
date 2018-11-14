<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AuthorizationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthorizationsController extends Controller
{
    //用户登陆
    public function store(AuthorizationRequest $request){
        $username = $request->username;

        //filter_var() 函数通过指定的过滤器过滤变量 如果成功，则返回已过滤的数据，如果失败，则返回 false。
        //这里检测用户输入的是邮箱还是手机号登陆
        filter_var($username,FILTER_VALIDATE_EMAIL) ? $credentials['email'] = $username : $credentials['phone'] = $username;

        $credentials['password'] = $request->password;
//        dd($credentials);
        if (!$token = Auth::guard('api')->attempt($credentials)){
            return $this->response->errorUnauthorized('账号或密码错误');
        }

        return $this->responseWithToken($token)->setStatusCode(201);
    }

    //更新token
    public function update(){
        $token = Auth::guard('api')->refresh();
        return $this->responseWithToken($token);
    }

    //删除token
    public function destroy(){
        Auth::guard('api')->logout();
        return $this->response->noContent();
    }

    //封装响应规范
    protected function responseWithToken(string $token){
        return $this->response->array([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => \Auth::guard('api')->factory()->getTTL() * 60
        ]);
    }

    //获取某用户token(开发环境)
    public function showToken(User $user){
        if (in_array(app()->environment(),['local','testing'])){
            return $this->responseWithToken(Auth::guard('api')->login($user));
        }
        return '非 开发/测试 环境';
    }
}
