<?php

return [
    //七牛默认域名
    'qiniuDomain'   => 'pglgpkuzs.bkt.clouddn.com',
    //对象存储空间默认名
    'bucket'        => 'dozhan-testing',
    //AK
    'access_key'    => '7jLUeoq1Un9H5fJjTid-kNwva3x2uAASEsD3DXyd',
    //SK
    'secret_key'    => '07HYzerzkxElZyPgnShHn9luffNPNuKwegpE16oY',
    //自定义凭证有效期（expires单位为秒，为上传凭证的有效时间）
    'expires'       => time()+ 60*60,
    //队列名
    'pipeline'      => 'dozhan',

    //上传策略
    'policy' => [
        'callbackUrl',
        'callbackBody',
        'callbackHost',
        'callbackBodyType',

        'returnUrl',
        'returnBody',

        'endUser',
        'saveKey',
        'insertOnly',

        'detectMime',
        'mimeLimit',
        'fsizeMin',
        'fsizeLimit',

        'persistentOps',
        'persistentNotifyUrl',
        'persistentPipeline',

        'deleteAfterDays',
        'fileType',
        'isPrefixalScope',
    ],

];
