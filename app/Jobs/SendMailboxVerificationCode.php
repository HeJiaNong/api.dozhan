<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mail\VerificationCodesMail;
use Illuminate\Support\Facades\Mail;
use Mockery\Exception;

class SendMailboxVerificationCode implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 任务最大尝试次数。
     *
     * @var int
     */
    public $tries = 1;

    /**
     * 任务运行的超时时间。
     *
     * @var int
     */
    public $timeout = 120;

    //自定义属性
    public $email;
    public $code;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email,$code)
    {
        $this->email = $email;
        $this->code = $code;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //发送邮件
        try{
            Mail::to($this->email)->send(new VerificationCodesMail($this->code));
        }catch (\Exception $e){
            throw new Exception($this->email.'邮件发送失败');
        }
    }

    /**
     * 要处理的失败任务。
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        // 给用户发送失败通知，等等...
        logger('==============邮件验证码错误==============');
        logger($exception->getMessage());
        logger('==============邮件验证码错误==============');
    }
}
