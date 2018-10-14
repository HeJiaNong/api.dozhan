<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\VerificationCodeRequest;
use App\Mail\VerificationCodesMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class VerificationCodesController extends Controller
{

    public $expiredTime = 10;   //验证码失效时间 分钟

    //邮箱验证码
    public function email(VerificationCodeRequest $request){
        //获取邮箱地址
        $email = $request->email;

        //用于缓存的 key
        $key = 'email-'.md5(str_random(20));

        //生成随机4位数验证码，左侧补0
        $code = str_pad(random_int(1,9999), 4,0,STR_PAD_LEFT);

        //发送邮件
        try{
            Mail::to($email)->send(new VerificationCodesMail($code));
        }catch (\Exception $e){
            return $this->response->errorUnauthorized('邮件发送失败');
        }

        $expired_at = $this->getExpiredAt();

        //写入缓存
        Cache::put($key,compact('email','code'),$expired_at);

        return $this->response->array([
            'key' => $key,
            'expired_at' => $expired_at->toDateTimeString(),
        ]);
    }

    public function getExpiredAt(){
        return now()->addMinutes($this->expiredTime);
    }

    //短信验证码
    public function sms(){

    }
}
