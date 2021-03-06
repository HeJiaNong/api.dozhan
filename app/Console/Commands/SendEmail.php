<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Overtrue\EasySms\EasySms;

class SendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '发送一条测试短信';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @throws \Overtrue\EasySms\Exceptions\InvalidArgumentException
     * @return mixed
     */
    public function handle()
    {
        $phone = $this->ask('pls enter your phone number:');

        $config = [
            // HTTP 请求的超时时间（秒）
            'timeout' => 5.0,

            // 默认发送配置
            'default' => [
                // 网关调用策略，默认：顺序调用
                'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

                // 默认可用的发送网关
                'gateways' => [
                    'aliyun',
                ],
            ],
            // 可用的网关配置
            'gateways' => [
                'aliyun' => [
                    'access_key_id' => env('ALIYUN_SMS_AK'),
                    'access_key_secret' => env('ALIYUN_SMS_SK'),
                    'sign_name' => env('ALIYUN_SMS_SIGN_NAME'),
                ],
            ],
        ];

        $easySms = new EasySms($config);
        
        try{
            $easySms->send($phone, [
                'template' => 'SMS_156990210',
            ]);
        } catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception) {
            $message = $exception->getException('aliyun')->getMessage();
            dd($message);
        }

    }
}
