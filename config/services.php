<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\Models\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    //七牛服务配置
    'qiniu' => [
        //AK
        'access_key' => '7jLUeoq1Un9H5fJjTid-kNwva3x2uAASEsD3DXyd',
        //SK
        'secret_key' => '07HYzerzkxElZyPgnShHn9luffNPNuKwegpE16oY',
        //空间域名
        'domain'   => 'phcczptg4.bkt.clouddn.com',
        //对象存储空间默认名
        'bucket' => 'dozhan',
        //自定义凭证有效期（expires单位为秒，为上传凭证的有效时间）
        'expires' => time() + 60*60,
        //上传策略
        'policy' => [
            //七牛callbackUrl回调地址
            'callbackUrl' => function(){
                return (string)app(\Dingo\Api\Routing\UrlGenerator::class)->version('v1')->route('api.resources.qiniu.callback');
            },

            //回调数据类型
            'callbackBodyType' => 'application/json',

            //持久化处理队列选择
            'persistentPipeline' => 'dozhan',

            //七牛持久化处理结果消息通知地址
            'persistentNotifyUrl' => function(){
                return (string)app(\Dingo\Api\Routing\UrlGenerator::class)->version('v1')->route('api.resources.qiniu.notification');
            },
        ],
        'image' => [
            //图片尺寸缩放限制
            'size' => [
                'user_avatar' => '200x200',
                'site_banner' => '1024x400',
                'video_cover' => '480x300',
                'category_icon' => '200x200',
            ],
        ],
        'video' => [
            //水印logo地址
            'watermarkUrl' => 'http://phcczptg4.bkt.clouddn.com/seeder/logo/logo.png',
        ],
    ],

];
