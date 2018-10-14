<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\EmailRequest;
use App\Mail\UserRegisterMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function store(EmailRequest $request){
        //接收数据
        $email = $request->email;
        $password = $request->password;

        //生成随机key
        $key = 'email-'.str_random(20);

        //key 过期时间
        $expiredAt = now()->addMinutes(30);

        //通过 key 生成 url 链接 访问链接即可注册
        $url = app(\Dingo\Api\Routing\UrlGenerator::class)->version('v1')->route('api.user.emailRegister',['key' => $key]);

        try{
            //发送邮件;
            Mail::to($email)->send(new UserRegisterMail($url));
        }catch (\Exception $e){
            return $this->response->errorUnauthorized('请检查邮箱正确性');
        }

        //如果发送失败
        if (!empty(Mail::failures())){
            return $this->response->    error('邮件发送失败',500);
        }

        //将数据存入缓存
        Cache::put($key,compact('email','password'),$expiredAt);

        $result = [
            'url' => $url,
            'key' => $key,
            'expired_at' => $expiredAt,
        ];

        //发送成功，返回key
        return $this->response->created();
    }
}
