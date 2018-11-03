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
        //七牛默认域名
        'domain'   => 'phcczptg4.bkt.clouddn.com',
        //对象存储空间默认名
        'bucket' => 'dozhan',
        //自定义凭证有效期（expires单位为秒，为上传凭证的有效时间）
        'expires' => time()+ 60*60,
        //队列名
        'pipeline' => 'dozhan',
        //上传配置
        'upload' => [
            'video' => [
                'prefix' => 'video/',
                'mimeType' => 'video/*',
            ],
            'image' => [
                'prefix' => 'image/',
                'mimeType' => 'image/*',
            ],
            'other' => [
                'prefix' => 'other/',
                'mimeType' => null,
            ],
        ],

    ],

];
