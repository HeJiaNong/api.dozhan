<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AuthorizationRequest;
use App\Jobs\SendMailboxVerificationCode;
use App\Models\User;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('throttle:60,1')->only(['store','register']);
        $this->middleware('throttle:5,1')->only(['resetPasswordCode']);
        $this->middleware('api.auth')->only(['refresh','logout']);
    }

    //注册邮箱验证码
    public function registerVerificationCode(Request $request){
        $request->validate([
            'email' => 'required|email|unique:users',
        ]);

        //获取邮箱地址
        $email = $request->email;

        list($key,$code,$expired_at) = $this->seedEmailVerificationCode($email);

        //写入缓存
        Cache::put($key,compact('email','code'),$expired_at);

        return $this->response->array([
            'key' => $key,
            'expired_at' => $expired_at->toDatetimestring(),
        ]);
    }

    //注册
    public function register(Request $request,User $user){
        $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'code' => 'required|integer',
            'key' => 'required|string',
        ]);

        //获取 Cache 中的数据
        $data = Cache::get($request->key);

        //如果用户的邮箱与缓存邮箱不匹配或者验证码不正确
        if ($request->email !== $data['email'] || $request->code !== $data['code']){
            return $this->response->errorUnauthorized('验证码错误');
        }

        //创建用户
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        //清除 此条验证码缓存
        Cache::forget($request->key);

        //dingo 封装的方法，就是返回 201 的状态码
        return $this->response->item($user,new UserTransformer())->setMeta([
            'access_token' => Auth::guard('api')->fromUser($user),
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
        ])->setStatusCode(201);
    }

    //登陆
    public function login(AuthorizationRequest $request){
        $username = $request->username;

        //filter_var() 函数通过指定的过滤器过滤变量 如果成功，则返回已过滤的数据，如果失败，则返回 false。
        //这里检测用户输入的是邮箱还是手机号登陆
        filter_var($username,FILTER_VALIDATE_EMAIL) ? $credentials['email'] = $username : $credentials['phone'] = $username;

        $credentials['password'] = $request->password;

        if (!$token = Auth::guard('api')->attempt($credentials)){
            return $this->response->errorUnauthorized('账号或密码错误');
        }

        return $this->responseWithToken($token)->setStatusCode(201);
    }

    //刷新
    public function refresh(){
        $token = Auth::guard('api')->refresh();
        return $this->responseWithToken($token);
    }

    //登出
    public function logout(){
        Auth::guard('api')->logout();
        return $this->response->noContent();
    }

    //响应
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

    //重置密码邮箱验证码
    public function resetPasswordCode(Request $request){
        $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        //获取邮箱地址
        $email = $request->email;

        list($key,$code,$expired_at) = $this->seedEmailVerificationCode($email);

        //写入缓存
        Cache::put($key,compact('email','code'),$expired_at);

        return $this->response->array([
            'key' => $key,
            'expired_at' => $expired_at->toDatetimestring(),
        ]);
    }

    //重置密码
    public function resetPassword(Request $request){
        $request->validate([
            'password' => 'required|string|min:6',
            'code' => 'required|integer',
            'key' => 'required|string',
        ],[],[
            'code' => '验证码',
        ]);

        $data = Cache::get($request->key) ?? abort(422,'验证码已过期');
        $user = User::where('email',$data['email'])->firstOrFail();

        if ($request->code !== $data['code']){
            return $this->response->errorUnauthorized('验证码错误');
        }

        $user->password = bcrypt($request->password);
        $user->save();

        //清除 此条验证码缓存
        Cache::forget($request->key);

        return $this->response->noContent();
    }

    //发送邮件验证码
    protected function seedEmailVerificationCode($email){

        //用于缓存的 key
        $key = 'VerificationCode-'.md5(str_random(20));

        //缓存过期时间
        $expired_at = now()->addMinutes(10);

        //生成随机4位数验证码，左侧补0
        $code = str_pad(mt_rand(1,9999), 4,mt_rand(1,9),STR_PAD_LEFT);

        //将发送邮件分发至列队中
        dispatch(new SendMailboxVerificationCode($email,$code))->onQueue('email');

        return [
            $key,
            $code,
            $expired_at,
        ];
    }

}
